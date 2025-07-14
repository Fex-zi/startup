<?php

namespace Models;

class MatchModel extends BaseModel
{
    protected $table = 'matches';
    protected $fillable = [
        'startup_id', 'investor_id', 'match_score', 'match_reasons',
        'startup_interested', 'investor_interested', 'status'
    ];

    public function createMatch($data)
    {
        // Ensure match_reasons is JSON encoded
        if (isset($data['match_reasons']) && is_array($data['match_reasons'])) {
            $data['match_reasons'] = json_encode($data['match_reasons']);
        }
        
        return $this->create($data);
    }

    public function updateMatch($id, $data)
    {
        // Ensure match_reasons is JSON encoded
        if (isset($data['match_reasons']) && is_array($data['match_reasons'])) {
            $data['match_reasons'] = json_encode($data['match_reasons']);
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Safely decode JSON, handling null values
     */
    private function safeJsonDecode($json)
    {
        if ($json === null || $json === '') {
            return null;
        }
        
        return json_decode($json, true);
    }

    public function getMatchWithDetails($matchId)
    {
        $sql = "
            SELECT 
                m.*,
                s.company_name, s.description as startup_description, s.stage, s.funding_goal,
                su.first_name as startup_first_name, su.last_name as startup_last_name,
                i.company_name as investor_company, i.investor_type, i.bio as investor_bio,
                iu.first_name as investor_first_name, iu.last_name as investor_last_name,
                ind.name as industry_name
            FROM matches m
            JOIN startups s ON m.startup_id = s.id
            JOIN users su ON s.user_id = su.id
            JOIN investors i ON m.investor_id = i.id
            JOIN users iu ON i.user_id = iu.id
            LEFT JOIN industries ind ON s.industry_id = ind.id
            WHERE m.id = ?
        ";
        
        $match = $this->db->fetch($sql, [$matchId]);
        
        if ($match && $match['match_reasons']) {
            // FIX: Safely decode JSON
            $match['match_reasons'] = $this->safeJsonDecode($match['match_reasons']) ?? [];
        }
        
        return $match;
    }

    public function getMatchesForStartup($startupId, $status = null)
    {
        $sql = "
            SELECT 
                m.*,
                i.company_name as investor_company, i.investor_type, i.bio,
                u.first_name, u.last_name,
                i.investment_range_min, i.investment_range_max
            FROM matches m
            JOIN investors i ON m.investor_id = i.id
            JOIN users u ON i.user_id = u.id
            WHERE m.startup_id = ?
        ";
        
        $params = [$startupId];
        
        if ($status) {
            $sql .= " AND m.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY m.match_score DESC, m.created_at DESC";
        
        $matches = $this->db->fetchAll($sql, $params);
        
        // FIX: Decode match_reasons safely for each match
        foreach ($matches as &$match) {
            if ($match['match_reasons']) {
                $match['match_reasons'] = $this->safeJsonDecode($match['match_reasons']) ?? [];
            }
        }
        
        return $matches;
    }

    public function getMatchesForInvestor($investorId, $status = null)
    {
        $sql = "
            SELECT 
                m.*,
                s.company_name, s.description, s.stage, s.funding_goal, s.logo_url,
                u.first_name, u.last_name,
                ind.name as industry_name
            FROM matches m
            JOIN startups s ON m.startup_id = s.id
            JOIN users u ON s.user_id = u.id
            LEFT JOIN industries ind ON s.industry_id = ind.id
            WHERE m.investor_id = ?
        ";
        
        $params = [$investorId];
        
        if ($status) {
            $sql .= " AND m.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY m.match_score DESC, m.created_at DESC";
        
        $matches = $this->db->fetchAll($sql, $params);
        
        // FIX: Decode match_reasons safely for each match
        foreach ($matches as &$match) {
            if ($match['match_reasons']) {
                $match['match_reasons'] = $this->safeJsonDecode($match['match_reasons']) ?? [];
            }
        }
        
        return $matches;
    }

    public function findExistingMatch($startupId, $investorId)
    {
        $sql = "SELECT * FROM matches WHERE startup_id = ? AND investor_id = ?";
        return $this->db->fetch($sql, [$startupId, $investorId]);
    }

    public function recordInterest($matchId, $userType, $interested)
    {
        $field = $userType === 'startup' ? 'startup_interested' : 'investor_interested';
        
        $updateData = [$field => $interested ? 1 : 0];
        
        // Check if this creates a mutual interest
        $match = $this->find($matchId);
        if ($match) {
            $startupInterested = $userType === 'startup' ? $interested : $match['startup_interested'];
            $investorInterested = $userType === 'investor' ? $interested : $match['investor_interested'];
            
            if ($startupInterested && $investorInterested) {
                $updateData['status'] = 'mutual_interest';
            } elseif ($startupInterested === false || $investorInterested === false) {
                $updateData['status'] = $userType === 'startup' ? 'startup_declined' : 'investor_declined';
            }
        }
        
        return $this->update($matchId, $updateData);
    }

    public function getMutualMatches($userId, $userType)
    {
        if ($userType === 'startup') {
            $sql = "
                SELECT 
                    m.*,
                    i.company_name as investor_company, i.investor_type,
                    u.first_name, u.last_name, u.email
                FROM matches m
                JOIN startups s ON m.startup_id = s.id
                JOIN investors i ON m.investor_id = i.id
                JOIN users u ON i.user_id = u.id
                WHERE s.user_id = ? AND m.status = 'mutual_interest'
                ORDER BY m.updated_at DESC
            ";
        } else {
            $sql = "
                SELECT 
                    m.*,
                    s.company_name, s.stage, s.funding_goal,
                    u.first_name, u.last_name, u.email
                FROM matches m
                JOIN investors i ON m.investor_id = i.id
                JOIN startups s ON m.startup_id = s.id
                JOIN users u ON s.user_id = u.id
                WHERE i.user_id = ? AND m.status = 'mutual_interest'
                ORDER BY m.updated_at DESC
            ";
        }
        
        return $this->db->fetchAll($sql, [$userId]);
    }

    public function getMatchStats($userId, $userType)
    {
        $table = $userType === 'startup' ? 'startups' : 'investors';
        $joinField = $userType === 'startup' ? 'startup_id' : 'investor_id';
        
        $sql = "
            SELECT 
                COUNT(*) as total_matches,
                SUM(CASE WHEN status = 'mutual_interest' THEN 1 ELSE 0 END) as mutual_matches,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_matches,
                AVG(match_score) as avg_match_score
            FROM matches m
            JOIN {$table} t ON m.{$joinField} = t.id
            WHERE t.user_id = ?
        ";
        
        return $this->db->fetch($sql, [$userId]);
    }

    public function getTopMatches($userId, $userType, $limit = 10)
    {
        if ($userType === 'startup') {
            return $this->getMatchesForStartup($this->getStartupIdByUserId($userId), null);
        } else {
            return $this->getMatchesForInvestor($this->getInvestorIdByUserId($userId), null);
        }
    }

    private function getStartupIdByUserId($userId)
    {
        $sql = "SELECT id FROM startups WHERE user_id = ?";
        $result = $this->db->fetch($sql, [$userId]);
        return $result ? $result['id'] : null;
    }

    private function getInvestorIdByUserId($userId)
    {
        $sql = "SELECT id FROM investors WHERE user_id = ?";
        $result = $this->db->fetch($sql, [$userId]);
        return $result ? $result['id'] : null;
    }

    public function expireOldMatches($days = 30)
    {
        $sql = "
            UPDATE matches 
            SET status = 'expired' 
            WHERE status = 'pending' 
            AND created_at < DATE_SUB(NOW(), INTERVAL ? DAY)
        ";
        
        return $this->db->update($sql, [$days]);
    }

    public function getRecentMatches($limit = 20)
    {
        $sql = "
            SELECT 
                m.*,
                s.company_name, 
                i.company_name as investor_company,
                su.first_name as startup_first_name, su.last_name as startup_last_name,
                iu.first_name as investor_first_name, iu.last_name as investor_last_name
            FROM matches m
            JOIN startups s ON m.startup_id = s.id
            JOIN users su ON s.user_id = su.id
            JOIN investors i ON m.investor_id = i.id
            JOIN users iu ON i.user_id = iu.id
            ORDER BY m.created_at DESC
            LIMIT ?
        ";
        
        return $this->db->fetchAll($sql, [$limit]);
    }
}
