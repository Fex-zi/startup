<?php

namespace Models;

class Startup extends BaseModel
{
    protected $table = 'startups';
    protected $fillable = [
        'user_id', 'company_name', 'slug', 'description', 'industry_id', 
        'stage', 'employee_count', 'website', 'logo_url', 'pitch_deck_url', 
        'business_plan_url', 'funding_goal', 'funding_type', 'location', 'is_featured'
    ];

    public function createStartup($data)
    {
        // Generate slug from company name
        if (isset($data['company_name']) && !isset($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['company_name']);
        }
        
        return $this->create($data);
    }

    public function updateStartup($id, $data)
    {
        // Update slug if company name changed
        if (isset($data['company_name'])) {
            $data['slug'] = $this->generateSlug($data['company_name'], $id);
        }
        
        return $this->update($id, $data);
    }

    public function findBySlug($slug)
    {
        return $this->findBy('slug', $slug);
    }

    public function getStartupWithUser($startupId)
    {
        $sql = "
            SELECT s.*, u.first_name, u.last_name, u.email, u.phone, i.name as industry_name
            FROM startups s
            JOIN users u ON s.user_id = u.id
            LEFT JOIN industries i ON s.industry_id = i.id
            WHERE s.id = ?
        ";
        
        return $this->db->fetch($sql, [$startupId]);
    }

    public function getStartupsByIndustry($industryId, $limit = 10)
    {
        $sql = "
            SELECT s.*, u.first_name, u.last_name, i.name as industry_name
            FROM startups s
            JOIN users u ON s.user_id = u.id
            LEFT JOIN industries i ON s.industry_id = i.id
            WHERE s.industry_id = ?
            ORDER BY s.created_at DESC
            LIMIT ?
        ";
        
        return $this->db->fetchAll($sql, [$industryId, $limit]);
    }

    public function getStartupsByStage($stage, $limit = 10)
    {
        $conditions = ['stage' => $stage];
        return $this->where($conditions, $limit, null, 'created_at DESC');
    }

    public function getFeaturedStartups($limit = 5)
    {
        $sql = "
            SELECT s.*, u.first_name, u.last_name, i.name as industry_name
            FROM startups s
            JOIN users u ON s.user_id = u.id
            LEFT JOIN industries i ON s.industry_id = i.id
            WHERE s.is_featured = 1
            ORDER BY s.created_at DESC
            LIMIT ?
        ";
        
        return $this->db->fetchAll($sql, [$limit]);
    }

    public function searchStartups($query, $filters = [], $page = 1, $perPage = 20)
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        
        $sql = "
            SELECT s.*, u.first_name, u.last_name, i.name as industry_name
            FROM startups s
            JOIN users u ON s.user_id = u.id
            LEFT JOIN industries i ON s.industry_id = i.id
            WHERE 1=1
        ";
        
        // Full-text search
        if (!empty($query)) {
            $sql .= " AND MATCH(s.company_name, s.description) AGAINST(? IN NATURAL LANGUAGE MODE)";
            $params[] = $query;
        }
        
        // Apply filters
        if (!empty($filters['industry_id'])) {
            $sql .= " AND s.industry_id = ?";
            $params[] = $filters['industry_id'];
        }
        
        if (!empty($filters['stage'])) {
            $sql .= " AND s.stage = ?";
            $params[] = $filters['stage'];
        }
        
        if (!empty($filters['funding_type'])) {
            $sql .= " AND s.funding_type = ?";
            $params[] = $filters['funding_type'];
        }
        
        if (!empty($filters['location'])) {
            $sql .= " AND s.location LIKE ?";
            $params[] = '%' . $filters['location'] . '%';
        }
        
        if (!empty($filters['funding_min'])) {
            $sql .= " AND s.funding_goal >= ?";
            $params[] = $filters['funding_min'];
        }
        
        if (!empty($filters['funding_max'])) {
            $sql .= " AND s.funding_goal <= ?";
            $params[] = $filters['funding_max'];
        }
        
        // Get total count
        $countSql = str_replace('SELECT s.*, u.first_name, u.last_name, i.name as industry_name', 'SELECT COUNT(*) as total', $sql);
        $totalResult = $this->db->fetch($countSql, $params);
        $total = $totalResult['total'];
        
        // Add ordering and pagination
        $sql .= " ORDER BY s.created_at DESC LIMIT ? OFFSET ?";
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

    public function getStartupsForMatching($investorCriteria)
    {
        $sql = "
            SELECT s.*, u.first_name, u.last_name, i.name as industry_name
            FROM startups s
            JOIN users u ON s.user_id = u.id AND u.is_active = 1
            LEFT JOIN industries i ON s.industry_id = i.id
            WHERE 1=1
        ";
        
        $params = [];
        
        // Filter by industry if specified
        if (!empty($investorCriteria['preferred_industries'])) {
            $industries = json_decode($investorCriteria['preferred_industries'], true);
            if (!empty($industries)) {
                $placeholders = str_repeat('?,', count($industries) - 1) . '?';
                $sql .= " AND s.industry_id IN ({$placeholders})";
                $params = array_merge($params, $industries);
            }
        }
        
        // Filter by investment stages
        if (!empty($investorCriteria['investment_stages'])) {
            $stages = json_decode($investorCriteria['investment_stages'], true);
            if (!empty($stages)) {
                $placeholders = str_repeat('?,', count($stages) - 1) . '?';
                $sql .= " AND s.stage IN ({$placeholders})";
                $params = array_merge($params, $stages);
            }
        }
        
        // Filter by funding range
        if (!empty($investorCriteria['investment_range_min'])) {
            $sql .= " AND s.funding_goal >= ?";
            $params[] = $investorCriteria['investment_range_min'];
        }
        
        if (!empty($investorCriteria['investment_range_max'])) {
            $sql .= " AND s.funding_goal <= ?";
            $params[] = $investorCriteria['investment_range_max'];
        }
        
        $sql .= " ORDER BY s.created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }

    private function generateSlug($companyName, $excludeId = null)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $companyName)));
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->isSlugTaken($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    private function isSlugTaken($slug, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as count FROM startups WHERE slug = ?";
        $params = [$slug];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['count'] > 0;
    }

    public function getRecentStartups($limit = 10)
    {
        $sql = "
            SELECT s.*, u.first_name, u.last_name, i.name as industry_name
            FROM startups s
            JOIN users u ON s.user_id = u.id
            LEFT JOIN industries i ON s.industry_id = i.id
            ORDER BY s.created_at DESC
            LIMIT ?
        ";
        
        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * Get similar startups based on industry, excluding current startup
     */
    public function getSimilarStartups($industryId, $excludeStartupId = null, $limit = 5)
    {
        $sql = "
            SELECT s.*, u.first_name, u.last_name, i.name as industry_name
            FROM startups s
            JOIN users u ON s.user_id = u.id
            LEFT JOIN industries i ON s.industry_id = i.id
            WHERE s.industry_id = ?
        ";
        
        $params = [$industryId];
        
        if ($excludeStartupId) {
            $sql .= " AND s.id != ?";
            $params[] = $excludeStartupId;
        }
        
        $sql .= " ORDER BY s.created_at DESC LIMIT ?";
        $params[] = $limit;
        
        return $this->db->fetchAll($sql, $params);
    }
}
