<?php

namespace Models;

class Investor extends BaseModel
{
    protected $table = 'investors';
    protected $fillable = [
        'user_id', 'investor_type', 'company_name', 'bio', 'preferred_industries',
        'investment_stages', 'investment_range_min', 'investment_range_max',
        'location', 'portfolio_companies', 'availability_status',
        'linkedin_url', 'website', 'avatar_url'
    ];

    public function createInvestor($data)
    {
        // Ensure JSON fields are properly encoded
        if (isset($data['preferred_industries']) && is_array($data['preferred_industries'])) {
            $data['preferred_industries'] = json_encode($data['preferred_industries']);
        }
        
        if (isset($data['investment_stages']) && is_array($data['investment_stages'])) {
            $data['investment_stages'] = json_encode($data['investment_stages']);
        }
        
        if (isset($data['portfolio_companies']) && is_array($data['portfolio_companies'])) {
            $data['portfolio_companies'] = json_encode($data['portfolio_companies']);
        }
        
        return $this->create($data);
    }

    public function updateInvestor($id, $data)
    {
        // Ensure JSON fields are properly encoded
        if (isset($data['preferred_industries']) && is_array($data['preferred_industries'])) {
            $data['preferred_industries'] = json_encode($data['preferred_industries']);
        }
        
        if (isset($data['investment_stages']) && is_array($data['investment_stages'])) {
            $data['investment_stages'] = json_encode($data['investment_stages']);
        }
        
        if (isset($data['portfolio_companies']) && is_array($data['portfolio_companies'])) {
            $data['portfolio_companies'] = json_encode($data['portfolio_companies']);
        }
        
        return $this->update($id, $data);
    }

    public function getInvestorWithUser($investorId)
    {
        $sql = "
            SELECT i.*, u.first_name, u.last_name, u.email, u.phone
            FROM investors i
            JOIN users u ON i.user_id = u.id
            WHERE i.id = ?
        ";
        
        $investor = $this->db->fetch($sql, [$investorId]);
        
        if ($investor) {
            // Decode JSON fields
            $investor['preferred_industries'] = json_decode($investor['preferred_industries'], true) ?? [];
            $investor['investment_stages'] = json_decode($investor['investment_stages'], true) ?? [];
            $investor['portfolio_companies'] = json_decode($investor['portfolio_companies'], true) ?? [];
        }
        
        return $investor;
    }

    public function getInvestorsByType($investorType, $limit = 10)
    {
        $sql = "
            SELECT i.*, u.first_name, u.last_name
            FROM investors i
            JOIN users u ON i.user_id = u.id
            WHERE i.investor_type = ?
            ORDER BY i.created_at DESC
            LIMIT ?
        ";
        
        return $this->db->fetchAll($sql, [$investorType, $limit]);
    }

    public function getActiveInvestors($limit = 20)
    {
        $sql = "
            SELECT i.*, u.first_name, u.last_name
            FROM investors i
            JOIN users u ON i.user_id = u.id AND u.is_active = 1
            WHERE i.availability_status = 'actively_investing'
            ORDER BY i.created_at DESC
            LIMIT ?
        ";
        
        return $this->db->fetchAll($sql, [$limit]);
    }

    public function searchInvestors($query, $filters = [], $page = 1, $perPage = 20)
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        
        $sql = "
            SELECT i.*, u.first_name, u.last_name
            FROM investors i
            JOIN users u ON i.user_id = u.id
            WHERE 1=1
        ";
        
        // Text search
        if (!empty($query)) {
            $sql .= " AND (i.company_name LIKE ? OR i.bio LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)";
            $searchTerm = '%' . $query . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        // Apply filters
        if (!empty($filters['investor_type'])) {
            $sql .= " AND i.investor_type = ?";
            $params[] = $filters['investor_type'];
        }
        
        if (!empty($filters['availability_status'])) {
            $sql .= " AND i.availability_status = ?";
            $params[] = $filters['availability_status'];
        }
        
        if (!empty($filters['location'])) {
            $sql .= " AND i.location LIKE ?";
            $params[] = '%' . $filters['location'] . '%';
        }
        
        if (!empty($filters['investment_min'])) {
            $sql .= " AND i.investment_range_min >= ?";
            $params[] = $filters['investment_min'];
        }
        
        if (!empty($filters['investment_max'])) {
            $sql .= " AND i.investment_range_max <= ?";
            $params[] = $filters['investment_max'];
        }
        
        // Get total count
        $countSql = str_replace('SELECT i.*, u.first_name, u.last_name', 'SELECT COUNT(*) as total', $sql);
        $totalResult = $this->db->fetch($countSql, $params);
        $total = $totalResult['total'];
        
        // Add ordering and pagination
        $sql .= " ORDER BY i.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $results = $this->db->fetchAll($sql, $params);
        
        return [
            'data' => $results,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }

    public function getInvestorsForMatching($startupCriteria)
    {
        $sql = "
            SELECT i.*, u.first_name, u.last_name
            FROM investors i
            JOIN users u ON i.user_id = u.id AND u.is_active = 1
            WHERE i.availability_status = 'actively_investing'
        ";
        
        $params = [];
        
        // Filter by industry preferences
        if (!empty($startupCriteria['industry_id'])) {
            $sql .= " AND JSON_CONTAINS(i.preferred_industries, ?)";
            $params[] = json_encode([$startupCriteria['industry_id']]);
        }
        
        // Filter by investment stages
        if (!empty($startupCriteria['stage'])) {
            $sql .= " AND JSON_CONTAINS(i.investment_stages, ?)";
            $params[] = json_encode([$startupCriteria['stage']]);
        }
        
        // Filter by funding range
        if (!empty($startupCriteria['funding_goal'])) {
            $sql .= " AND i.investment_range_min <= ? AND i.investment_range_max >= ?";
            $params[] = $startupCriteria['funding_goal'];
            $params[] = $startupCriteria['funding_goal'];
        }
        
        $sql .= " ORDER BY i.created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }

    public function getInvestorsByIndustry($industryId, $limit = 10)
    {
        $sql = "
            SELECT i.*, u.first_name, u.last_name
            FROM investors i
            JOIN users u ON i.user_id = u.id
            WHERE JSON_CONTAINS(i.preferred_industries, ?)
            AND i.availability_status = 'actively_investing'
            ORDER BY i.created_at DESC
            LIMIT ?
        ";
        
        return $this->db->fetchAll($sql, [json_encode([$industryId]), $limit]);
    }

    public function getInvestorStats($investorId)
    {
        $investor = $this->find($investorId);
        
        if (!$investor) {
            return null;
        }
        
        // Get portfolio count
        $portfolioCompanies = json_decode($investor['portfolio_companies'], true) ?? [];
        $portfolioCount = count($portfolioCompanies);
        
        // Get match count
        $matchSql = "SELECT COUNT(*) as count FROM matches WHERE investor_id = ?";
        $matchResult = $this->db->fetch($matchSql, [$investorId]);
        $matchCount = $matchResult['count'];
        
        // Get mutual interest count
        $mutualSql = "SELECT COUNT(*) as count FROM matches WHERE investor_id = ? AND status = 'mutual_interest'";
        $mutualResult = $this->db->fetch($mutualSql, [$investorId]);
        $mutualCount = $mutualResult['count'];
        
        return [
            'portfolio_count' => $portfolioCount,
            'match_count' => $matchCount,
            'mutual_interest_count' => $mutualCount,
            'investment_range' => [
                'min' => $investor['investment_range_min'],
                'max' => $investor['investment_range_max']
            ]
        ];
    }

    public function getRecentInvestors($limit = 10)
    {
        $sql = "
            SELECT i.*, u.first_name, u.last_name
            FROM investors i
            JOIN users u ON i.user_id = u.id
            ORDER BY i.created_at DESC
            LIMIT ?
        ";
        
        return $this->db->fetchAll($sql, [$limit]);
    }

    public function updateAvailabilityStatus($investorId, $status)
    {
        $validStatuses = ['actively_investing', 'selective', 'not_investing'];
        
        if (!in_array($status, $validStatuses)) {
            throw new \Exception("Invalid availability status");
        }
        
        return $this->update($investorId, ['availability_status' => $status]);
    }
}
