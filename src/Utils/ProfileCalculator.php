<?php

namespace Utils;

use Models\Startup;
use Models\Investor;

class ProfileCalculator
{
    private $startup;
    private $investor;

    public function __construct()
    {
        $this->startup = new Startup();
        $this->investor = new Investor();
    }

    /**
     * 🔥 CRITICAL FIX: Calculate actual profile completion percentage
     */
    public static function calculateProfileCompletion($userId, $userType)
    {
        $calculator = new self();
        
        if ($userType === 'startup') {
            return $calculator->calculateStartupCompletion($userId);
        } else {
            return $calculator->calculateInvestorCompletion($userId);
        }
    }

    /**
     * Calculate startup profile completion with detailed breakdown
     */
    private function calculateStartupCompletion($userId)
    {
        $startup = $this->startup->findBy('user_id', $userId);
        if (!$startup) {
            return [
                'percentage' => 0,
                'missing_items' => ['Complete profile setup'],
                'completed_items' => [],
                'next_steps' => ['Create your startup profile']
            ];
        }

        // Define required fields with weights
        $requirements = [
            // Basic Information (40 points)
            'company_name' => ['weight' => 10, 'label' => 'Company name'],
            'description' => ['weight' => 10, 'label' => 'Company description'],
            'industry_id' => ['weight' => 10, 'label' => 'Industry selection'],
            'stage' => ['weight' => 10, 'label' => 'Company stage'],
            
            // Business Details (30 points)  
            'funding_goal' => ['weight' => 10, 'label' => 'Funding goal'],
            'funding_type' => ['weight' => 10, 'label' => 'Funding type'],
            'location' => ['weight' => 10, 'label' => 'Company location'],
            
            // Media & Documents (30 points)
            'logo_url' => ['weight' => 10, 'label' => 'Company logo'],
            'website' => ['weight' => 5, 'label' => 'Website URL'],
            'pitch_deck_url' => ['weight' => 10, 'label' => 'Pitch deck'],
            'business_plan_url' => ['weight' => 5, 'label' => 'Business plan']
        ];

        $completedPoints = 0;
        $totalPoints = 0;
        $completedItems = [];
        $missingItems = [];

        foreach ($requirements as $field => $config) {
            $totalPoints += $config['weight'];
            
            if ($this->isFieldCompleted($startup, $field)) {
                $completedPoints += $config['weight'];
                $completedItems[] = $config['label'];
            } else {
                $missingItems[] = $config['label'];
            }
        }

        $percentage = $totalPoints > 0 ? round(($completedPoints / $totalPoints) * 100) : 0;

        // Generate next steps based on missing items
        $nextSteps = [];
        if (empty($startup['logo_url'])) {
            $nextSteps[] = 'Upload company logo';
        }
        if (empty($startup['pitch_deck_url'])) {
            $nextSteps[] = 'Upload pitch deck';
        }
        if (empty($startup['business_plan_url'])) {
            $nextSteps[] = 'Upload business plan';
        }
        if (empty($startup['website'])) {
            $nextSteps[] = 'Add website URL';
        }

        return [
            'percentage' => $percentage,
            'missing_items' => $missingItems,
            'completed_items' => $completedItems,
            'next_steps' => $nextSteps ?: ['Profile looks complete!']
        ];
    }

    /**
     * Calculate investor profile completion with detailed breakdown
     */
    private function calculateInvestorCompletion($userId)
    {
        $investor = $this->investor->findBy('user_id', $userId);
        if (!$investor) {
            return [
                'percentage' => 0,
                'missing_items' => ['Complete profile setup'],
                'completed_items' => [],
                'next_steps' => ['Create your investor profile']
            ];
        }

        // Define required fields with weights
        $requirements = [
            // Basic Information (40 points)
            'investor_type' => ['weight' => 10, 'label' => 'Investor type'],
            'bio' => ['weight' => 15, 'label' => 'Professional bio'],
            'location' => ['weight' => 10, 'label' => 'Location'],
            'availability_status' => ['weight' => 5, 'label' => 'Investment status'],
            
            // Investment Criteria (40 points)
            'investment_range_min' => ['weight' => 10, 'label' => 'Minimum investment'],
            'investment_range_max' => ['weight' => 10, 'label' => 'Maximum investment'],
            'preferred_industries' => ['weight' => 10, 'label' => 'Preferred industries'],
            'investment_stages' => ['weight' => 10, 'label' => 'Investment stages'],
            
            // Optional but valuable (20 points)
            'company_name' => ['weight' => 5, 'label' => 'Company/fund name'],
            'website' => ['weight' => 5, 'label' => 'Website URL'],
            'linkedin_url' => ['weight' => 5, 'label' => 'LinkedIn profile'],
            'profile_picture_url' => ['weight' => 5, 'label' => 'Profile picture']
        ];

        $completedPoints = 0;
        $totalPoints = 0;
        $completedItems = [];
        $missingItems = [];

        foreach ($requirements as $field => $config) {
            $totalPoints += $config['weight'];
            
            if ($this->isFieldCompleted($investor, $field)) {
                $completedPoints += $config['weight'];
                $completedItems[] = $config['label'];
            } else {
                $missingItems[] = $config['label'];
            }
        }

        $percentage = $totalPoints > 0 ? round(($completedPoints / $totalPoints) * 100) : 0;

        // Generate next steps based on missing items
        $nextSteps = [];
        if (empty($investor['profile_picture_url'])) {
            $nextSteps[] = 'Upload profile picture';
        }
        if (empty($investor['company_name'])) {
            $nextSteps[] = 'Add company/fund name';
        }
        if (empty($investor['website'])) {
            $nextSteps[] = 'Add website URL';
        }
        if (empty($investor['linkedin_url'])) {
            $nextSteps[] = 'Add LinkedIn profile';
        }

        return [
            'percentage' => $percentage,
            'missing_items' => $missingItems,
            'completed_items' => $completedItems,
            'next_steps' => $nextSteps ?: ['Profile looks complete!']
        ];
    }

    /**
     * Check if a profile field is completed - PHP 8+ safe
     */
    private function isFieldCompleted($profile, $field)
    {
        $value = $profile[$field] ?? null;
        
        // Handle null values safely for PHP 8+
        if ($value === null || $value === '') {
            return false;
        }
        
        // Special cases for JSON fields
        if (in_array($field, ['preferred_industries', 'investment_stages'])) {
            $decoded = json_decode($value, true);
            return !empty($decoded);
        }
        
        // Numeric fields
        if (in_array($field, ['funding_goal', 'investment_range_min', 'investment_range_max', 'industry_id'])) {
            return !empty($value) && $value > 0;
        }
        
        // String fields - safe trim for PHP 8+
        return !empty(trim((string)$value));
    }

    /**
     * Get profile progress data for dashboard
     */
    public static function getProgressData($userId, $userType)
    {
        $completion = self::calculateProfileCompletion($userId, $userType);
        
        // Calculate additional progress metrics
        $outreachProgress = 0;
        $documentationProgress = 0;
        
        if ($userType === 'startup') {
            // Outreach progress based on matches and activity
            $startup = (new Startup())->findBy('user_id', $userId);
            if ($startup) {
                // Simple calculation: if they have matches, they're doing outreach
                $matchModel = new \Models\MatchModel();
                $matchCount = count($matchModel->getMatchesForStartup($startup['id']));
                $outreachProgress = min(100, $matchCount * 10); // 10% per match, max 100%
            }
            
            // Documentation progress based on uploaded files
            if ($startup) {
                $docPoints = 0;
                if (!empty($startup['pitch_deck_url'])) $docPoints += 50;
                if (!empty($startup['business_plan_url'])) $docPoints += 30;
                if (!empty($startup['website'])) $docPoints += 20;
                $documentationProgress = $docPoints;
            }
            
        } else {
            // For investors, different metrics
            $investor = (new Investor())->findBy('user_id', $userId);
            if ($investor) {
                // Outreach progress based on startup connections
                $matchModel = new \Models\MatchModel();
                $matchCount = count($matchModel->getMatchesForInvestor($investor['id']));
                $outreachProgress = min(100, $matchCount * 15); // 15% per match
            }
            
            // Documentation progress for investors (portfolio, etc.)
            if ($investor) {
                $docPoints = 0;
                if (!empty($investor['website'])) $docPoints += 30;
                if (!empty($investor['linkedin_url'])) $docPoints += 30;
                if (!empty($investor['portfolio_companies'])) $docPoints += 40;
                $documentationProgress = $docPoints;
            }
        }
        
        return [
            'profile_completion' => [
                'percentage' => $completion['percentage'],
                'missing_items' => $completion['missing_items'],
                'next_steps' => $completion['next_steps']
            ],
            'outreach_progress' => [
                'percentage' => $outreachProgress,
                'description' => $userType === 'startup' ? 'Investor connections' : 'Startup evaluations'
            ],
            'documentation_progress' => [
                'percentage' => $documentationProgress,
                'description' => $userType === 'startup' ? 'Business documents' : 'Investment materials'
            ]
        ];
    }
}
