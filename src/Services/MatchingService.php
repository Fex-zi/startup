<?php
namespace Services;

use Models\Startup;
use Models\Investor;
use Models\MatchModel;

class MatchingService
{
    private $startup;
    private $investor;
    private $match;

    public function __construct()
    {
        $this->startup = new Startup();
        $this->investor = new Investor();
        $this->match = new MatchModel();
    }

    public function findMatchesForStartup($startupId)
    {
        $startup = $this->startup->find($startupId);
        if (!$startup) return [];

        // Get potential investor matches
        $sql = "
            SELECT i.*, u.first_name, u.last_name, u.email
            FROM investors i
            JOIN users u ON i.user_id = u.id
            WHERE JSON_CONTAINS(i.preferred_industries, ?) 
            AND i.investment_range_min <= ? 
            AND i.investment_range_max >= ?
            AND JSON_CONTAINS(i.investment_stages, ?)
            AND i.availability_status = 'actively_investing'
        ";

        $params = [
            json_encode([$startup['industry_id']]),
            $startup['funding_goal'],
            $startup['funding_goal'],
            json_encode([$startup['stage']])
        ];

        $potentialMatches = $this->investor->db->fetchAll($sql, $params);

        $matches = [];
        foreach ($potentialMatches as $investor) {
            $score = $this->calculateMatchScore($startup, $investor);
            if ($score > 60) { // Only show high-quality matches
                $matches[] = [
                    'investor' => $investor,
                    'score' => $score,
                    'reasons' => $this->getMatchReasons($startup, $investor)
                ];
            }
        }

        // Sort by match score
        usort($matches, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return array_slice($matches, 0, 10); // Return top 10 matches
    }

    public function findMatchesForInvestor($investorId)
    {
        $investor = $this->investor->find($investorId);
        if (!$investor) return [];

        // FIX: Handle null JSON values properly
        $preferredIndustries = $this->safeJsonDecode($investor['preferred_industries']) ?? [];
        $investmentStages = $this->safeJsonDecode($investor['investment_stages']) ?? [];

        if (empty($preferredIndustries) || empty($investmentStages)) {
            return [];
        }

        // Get potential startup matches
        $industryPlaceholders = str_repeat('?,', count($preferredIndustries) - 1) . '?';
        $stagePlaceholders = str_repeat('?,', count($investmentStages) - 1) . '?';

        $sql = "
            SELECT s.*, u.first_name, u.last_name, u.email, i.name as industry_name
            FROM startups s
            JOIN users u ON s.user_id = u.id AND u.is_active = 1
            LEFT JOIN industries i ON s.industry_id = i.id
            WHERE s.industry_id IN ({$industryPlaceholders})
            AND s.stage IN ({$stagePlaceholders})
            AND s.funding_goal >= ?
            AND s.funding_goal <= ?
        ";

        $params = array_merge(
            $preferredIndustries,
            $investmentStages,
            [$investor['investment_range_min']],
            [$investor['investment_range_max']]
        );

        $potentialMatches = $this->startup->db->fetchAll($sql, $params);

        $matches = [];
        foreach ($potentialMatches as $startup) {
            $score = $this->calculateMatchScore($startup, $investor);
            if ($score > 60) { // Only show high-quality matches
                $matches[] = [
                    'startup' => $startup,
                    'score' => $score,
                    'reasons' => $this->getMatchReasons($startup, $investor)
                ];
            }
        }

        // Sort by match score
        usort($matches, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return array_slice($matches, 0, 10); // Return top 10 matches
    }

    private function calculateMatchScore($startup, $investor)
    {
        $score = 0;

        // Industry match (30 points)
        // FIX: Handle null JSON values properly
        $investorIndustries = $this->safeJsonDecode($investor['preferred_industries']) ?? [];
        if (in_array($startup['industry_id'], $investorIndustries)) {
            $score += 30;
        }

        // Stage match (25 points)
        // FIX: Handle null JSON values properly
        $investorStages = $this->safeJsonDecode($investor['investment_stages']) ?? [];
        if (in_array($startup['stage'], $investorStages)) {
            $score += 25;
        }

        // Investment size match (20 points)
        if ($startup['funding_goal'] >= $investor['investment_range_min'] &&
            $startup['funding_goal'] <= $investor['investment_range_max']) {
            $score += 20;
        }

        // Geographic proximity (15 points)
        if ($this->sameRegion($startup['location'], $investor['location'])) {
            $score += 15;
        }

        // Track record relevance (10 points)
        // FIX: Handle null JSON values properly
        $portfolioCompanies = $this->safeJsonDecode($investor['portfolio_companies']) ?? [];
        foreach ($portfolioCompanies as $company) {
            if (isset($company['industry_id']) && $company['industry_id'] == $startup['industry_id']) {
                $score += 10;
                break;
            }
        }

        return min($score, 100); // Cap at 100
    }

    private function getMatchReasons($startup, $investor)
    {
        $reasons = [];

        // FIX: Handle null JSON values properly
        $investorIndustries = $this->safeJsonDecode($investor['preferred_industries']) ?? [];
        if (in_array($startup['industry_id'], $investorIndustries)) {
            $reasons[] = 'Industry expertise match';
        }

        $investorStages = $this->safeJsonDecode($investor['investment_stages']) ?? [];
        if (in_array($startup['stage'], $investorStages)) {
            $reasons[] = 'Investment stage alignment';
        }

        if ($startup['funding_goal'] >= $investor['investment_range_min'] &&
            $startup['funding_goal'] <= $investor['investment_range_max']) {
            $reasons[] = 'Investment size fit';
        }

        if ($this->sameRegion($startup['location'], $investor['location'])) {
            $reasons[] = 'Geographic proximity';
        }

        return $reasons;
    }

    private function sameRegion($location1, $location2)
    {
        if (empty($location1) || empty($location2)) {
            return false;
        }

        // Simple region matching - can be enhanced
        $region1 = explode(',', $location1)[1] ?? '';
        $region2 = explode(',', $location2)[1] ?? '';
        return trim($region1) === trim($region2);
    }

    public function createMatch($startupId, $investorId, $score, $reasons)
    {
        // Check if match already exists
        $existingMatch = $this->match->findExistingMatch($startupId, $investorId);
        if ($existingMatch) {
            return $existingMatch;
        }

        $matchData = [
            'startup_id' => $startupId,
            'investor_id' => $investorId,
            'match_score' => $score,
            'match_reasons' => $reasons,
            'status' => 'pending'
        ];

        return $this->match->createMatch($matchData);
    }

    public function generateMatchesForStartup($startupId)
    {
        $matches = $this->findMatchesForStartup($startupId);
        $createdMatches = [];

        foreach ($matches as $match) {
            $matchId = $this->createMatch(
                $startupId,
                $match['investor']['id'],
                $match['score'],
                $match['reasons']
            );

            if ($matchId) {
                $createdMatches[] = $matchId;
            }
        }

        return $createdMatches;
    }

    public function generateMatchesForInvestor($investorId)
    {
        $matches = $this->findMatchesForInvestor($investorId);
        $createdMatches = [];

        foreach ($matches as $match) {
            $matchId = $this->createMatch(
                $match['startup']['id'],
                $investorId,
                $match['score'],
                $match['reasons']
            );

            if ($matchId) {
                $createdMatches[] = $matchId;
            }
        }

        return $createdMatches;
    }

    /**
     * Safely decode JSON, handling null values
     * FIX: This prevents the deprecation warning
     */
    private function safeJsonDecode($json)
    {
        if ($json === null || $json === '') {
            return null;
        }
        
        return json_decode($json, true);
    }
}