<?php

namespace Models;

use Exception;

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

    public function getMatchWithDetails($matchId)
    {
        try {
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
                $match['match_reasons'] = json_decode($match['match_reasons'], true) ?? [];
            }
            
            return $match;
        } catch (Exception $e) {
            error_log("Get match with details error: " . $e->getMessage());
            return null;
        }
    }

    public function getMatchesForStartup($startupId, $status = null)
    {
        try {
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
            
            // Decode match_reasons for each match
            foreach ($matches as &$match) {
                if ($match['match_reasons']) {
                    $match['match_reasons'] = json_decode($match['match_reasons'], true) ?? [];
                }
            }
            
            return $matches ?: [];
        } catch (Exception $e) {
            error_log("Get matches for startup error: " . $e->getMessage());
            return [];
        }
    }

    public function getMatchesForInvestor($investorId, $status = null)
    {
        try {
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
            
            // Decode match_reasons for each match
            foreach ($matches as &$match) {
                if ($match['match_reasons']) {
                    $match['match_reasons'] = json_decode($match['match_reasons'], true) ?? [];
                }
            }
            
            return $matches ?: [];
        } catch (Exception $e) {
            error_log("Get matches for investor error: " . $e->getMessage());
            return [];
        }
    }

    public function findExistingMatch($startupId, $investorId)
    {
        try {
            $sql = "SELECT * FROM matches WHERE startup_id = ? AND investor_id = ?";
            return $this->db->fetch($sql, [$startupId, $investorId]);
        } catch (Exception $e) {
            error_log("Find existing match error: " . $e->getMessage());
            return null;
        }
    }

    public function recordInterest($matchId, $userType, $interested)
    {
        try {
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
        } catch (Exception $e) {
            error_log("Record interest error: " . $e->getMessage());
            return false;
        }
    }

    public function getMutualMatches($userId, $userType)
    {
        try {
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
            
            return $this->db->fetchAll($sql, [$userId]) ?: [];
        } catch (Exception $e) {
            error_log("Get mutual matches error: " . $e->getMessage());
            return [];
        }
    }

    public function getMatchStats($userId, $userType)
    {
        try {
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
            
            $result = $this->db->fetch($sql, [$userId]);
            
            return [
                'total_matches' => (int)($result['total_matches'] ?? 0),
                'mutual_matches' => (int)($result['mutual_matches'] ?? 0),
                'pending_matches' => (int)($result['pending_matches'] ?? 0),
                'avg_match_score' => (float)($result['avg_match_score'] ?? 0)
            ];
        } catch (Exception $e) {
            error_log("Get match stats error: " . $e->getMessage());
            return [
                'total_matches' => 0,
                'mutual_matches' => 0,
                'pending_matches' => 0,
                'avg_match_score' => 0
            ];
        }
    }

    public function getTopMatches($userId, $userType, $limit = 10)
    {
        try {
            if ($userType === 'startup') {
                $startupId = $this->getStartupIdByUserId($userId);
                return $startupId ? $this->getMatchesForStartup($startupId) : [];
            } else {
                $investorId = $this->getInvestorIdByUserId($userId);
                return $investorId ? $this->getMatchesForInvestor($investorId) : [];
            }
        } catch (Exception $e) {
            error_log("Get top matches error: " . $e->getMessage());
            return [];
        }
    }

    private function getStartupIdByUserId($userId)
    {
        try {
            $sql = "SELECT id FROM startups WHERE user_id = ?";
            $result = $this->db->fetch($sql, [$userId]);
            return $result ? $result['id'] : null;
        } catch (Exception $e) {
            error_log("Get startup ID error: " . $e->getMessage());
            return null;
        }
    }

    private function getInvestorIdByUserId($userId)
    {
        try {
            $sql = "SELECT id FROM investors WHERE user_id = ?";
            $result = $this->db->fetch($sql, [$userId]);
            return $result ? $result['id'] : null;
        } catch (Exception $e) {
            error_log("Get investor ID error: " . $e->getMessage());
            return null;
        }
    }

    public function expireOldMatches($days = 30)
    {
        try {
            $sql = "
                UPDATE matches 
                SET status = 'expired' 
                WHERE status = 'pending' 
                AND created_at < DATE_SUB(NOW(), INTERVAL ? DAY)
            ";
            
            return $this->db->update($sql, [$days]);
        } catch (Exception $e) {
            error_log("Expire old matches error: " . $e->getMessage());
            return 0;
        }
    }

    public function getRecentMatches($limit = 20)
    {
        try {
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
            
            return $this->db->fetchAll($sql, [$limit]) ?: [];
        } catch (Exception $e) {
            error_log("Get recent matches error: " . $e->getMessage());
            return [];
        }
    }
}