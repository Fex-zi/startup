<?php

namespace Services;

use Exception;
use Models\Startup;
use Models\Investor;
use Models\Industry;

class SearchService
{
    private $startup;
    private $investor;
    private $industry;

    public function __construct()
    {
        $this->startup = new Startup();
        $this->investor = new Investor();
        $this->industry = new Industry();
    }

    /**
     * Search startups with filters and pagination
     */
    public function searchStartups($filters = [])
    {
        $page = max(1, (int)($filters['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        $params = [];
        
        $sql = "
            SELECT s.*, u.first_name, u.last_name, i.name as industry_name,
                   CASE 
                       WHEN s.is_featured = 1 THEN 1 
                       ELSE 0 
                   END as featured_priority
            FROM startups s
            JOIN users u ON s.user_id = u.id AND u.is_active = 1
            LEFT JOIN industries i ON s.industry_id = i.id
            WHERE 1=1
        ";

        // Text search with relevance scoring
        if (!empty($filters['search'])) {
            $searchTerm = trim($filters['search']);
            if (strlen($searchTerm) >= 2) {
                $sql .= " AND (
                    s.company_name LIKE ? 
                    OR s.description LIKE ?
                    OR u.first_name LIKE ?
                    OR u.last_name LIKE ?
                )";
                $likeTerm = '%' . $searchTerm . '%';
                $params[] = $likeTerm;
                $params[] = $likeTerm;
                $params[] = $likeTerm;
                $params[] = $likeTerm;
            }
        }

        // Industry filter
        if (!empty($filters['industry']) && is_numeric($filters['industry'])) {
            $sql .= " AND s.industry_id = ?";
            $params[] = (int)$filters['industry'];
        }

        // Stage filter
        if (!empty($filters['stage'])) {
            $validStages = ['idea', 'prototype', 'mvp', 'early_revenue', 'growth'];
            if (in_array($filters['stage'], $validStages)) {
                $sql .= " AND s.stage = ?";
                $params[] = $filters['stage'];
            }
        }

        // Location filter
        if (!empty($filters['location'])) {
            $sql .= " AND s.location LIKE ?";
            $params[] = '%' . trim($filters['location']) . '%';
        }

        // Funding range filters
        if (!empty($filters['funding_min']) && is_numeric($filters['funding_min'])) {
            $sql .= " AND s.funding_goal >= ?";
            $params[] = (float)$filters['funding_min'];
        }

        if (!empty($filters['funding_max']) && is_numeric($filters['funding_max'])) {
            $sql .= " AND s.funding_goal <= ?";
            $params[] = (float)$filters['funding_max'];
        }

        // Funding type filter
        if (!empty($filters['funding_type'])) {
            $validTypes = ['seed', 'series_a', 'series_b', 'debt', 'grant'];
            if (in_array($filters['funding_type'], $validTypes)) {
                $sql .= " AND s.funding_type = ?";
                $params[] = $filters['funding_type'];
            }
        }

        // Get total count for pagination
        $countSql = str_replace(
            'SELECT s.*, u.first_name, u.last_name, i.name as industry_name, CASE WHEN s.is_featured = 1 THEN 1 ELSE 0 END as featured_priority', 
            'SELECT COUNT(*) as total', 
            $sql
        );
        
        try {
            $totalResult = $this->startup->db->fetch($countSql, $params);
            $total = $totalResult['total'] ?? 0;

            // Add ordering and pagination
            $sql .= " ORDER BY featured_priority DESC, s.created_at DESC LIMIT ? OFFSET ?";
            $params[] = $perPage;
            $params[] = $offset;

            $results = $this->startup->db->fetchAll($sql, $params);

            return [
                'data' => $results ?: [],
                'pagination' => [
                    'total' => $total,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'last_page' => max(1, ceil($total / $perPage)),
                    'from' => $total > 0 ? $offset + 1 : 0,
                    'to' => min($offset + $perPage, $total)
                ]
            ];
        } catch (Exception $e) {
            error_log("Search startups database error: " . $e->getMessage());
            return [
                'data' => [],
                'pagination' => [
                    'total' => 0,
                    'per_page' => $perPage,
                    'current_page' => 1,
                    'last_page' => 1,
                    'from' => 0,
                    'to' => 0
                ]
            ];
        }
    }

    /**
     * Search investors with filters and pagination
     */
    public function searchInvestors($filters = [])
    {
        $page = max(1, (int)($filters['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        $params = [];
        
        $sql = "
            SELECT i.*, u.first_name, u.last_name
            FROM investors i
            JOIN users u ON i.user_id = u.id AND u.is_active = 1
            WHERE 1=1
        ";

        // Text search
        if (!empty($filters['search'])) {
            $searchTerm = trim($filters['search']);
            if (strlen($searchTerm) >= 2) {
                $sql .= " AND (
                    i.company_name LIKE ? 
                    OR i.bio LIKE ? 
                    OR u.first_name LIKE ? 
                    OR u.last_name LIKE ?
                )";
                $likeTerm = '%' . $searchTerm . '%';
                $params[] = $likeTerm;
                $params[] = $likeTerm;
                $params[] = $likeTerm;
                $params[] = $likeTerm;
            }
        }

        // Investor type filter
        if (!empty($filters['investor_type'])) {
            $validTypes = ['angel', 'vc_firm', 'corporate', 'family_office'];
            if (in_array($filters['investor_type'], $validTypes)) {
                $sql .= " AND i.investor_type = ?";
                $params[] = $filters['investor_type'];
            }
        }

        // Location filter
        if (!empty($filters['location'])) {
            $sql .= " AND i.location LIKE ?";
            $params[] = '%' . trim($filters['location']) . '%';
        }

        // Investment range filters
        if (!empty($filters['investment_min']) && is_numeric($filters['investment_min'])) {
            $sql .= " AND i.investment_range_max >= ?";
            $params[] = (float)$filters['investment_min'];
        }

        if (!empty($filters['investment_max']) && is_numeric($filters['investment_max'])) {
            $sql .= " AND i.investment_range_min <= ?";
            $params[] = (float)$filters['investment_max'];
        }

        // Industry preference filter
        if (!empty($filters['industry']) && is_numeric($filters['industry'])) {
            // Use a more compatible JSON search approach
            $sql .= " AND (i.preferred_industries LIKE ? OR i.preferred_industries IS NULL)";
            $params[] = '%"' . (int)$filters['industry'] . '"%';
        }

        // Only show actively investing (optional filter)
        if (!isset($filters['include_inactive'])) {
            $sql .= " AND (i.availability_status = 'actively_investing' OR i.availability_status IS NULL)";
        }

        try {
            // Get total count for pagination
            $countSql = str_replace(
                'SELECT i.*, u.first_name, u.last_name', 
                'SELECT COUNT(*) as total', 
                $sql
            );
            $totalResult = $this->investor->db->fetch($countSql, $params);
            $total = $totalResult['total'] ?? 0;

            // Add ordering and pagination
            $sql .= " ORDER BY i.created_at DESC LIMIT ? OFFSET ?";
            $params[] = $perPage;
            $params[] = $offset;

            $results = $this->investor->db->fetchAll($sql, $params);

            return [
                'data' => $results ?: [],
                'pagination' => [
                    'total' => $total,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'last_page' => max(1, ceil($total / $perPage)),
                    'from' => $total > 0 ? $offset + 1 : 0,
                    'to' => min($offset + $perPage, $total)
                ]
            ];
        } catch (Exception $e) {
            error_log("Search investors database error: " . $e->getMessage());
            return [
                'data' => [],
                'pagination' => [
                    'total' => 0,
                    'per_page' => $perPage,
                    'current_page' => 1,
                    'last_page' => 1,
                    'from' => 0,
                    'to' => 0
                ]
            ];
        }
    }

    /**
     * Get search suggestions for autocomplete
     */
    public function getSearchSuggestions($query, $type = 'startups')
    {
        if (strlen($query) < 2) {
            return [];
        }

        $suggestions = [];
        $searchTerm = '%' . trim($query) . '%';

        try {
            if ($type === 'startups') {
                // Company name suggestions
                $sql = "
                    SELECT DISTINCT company_name as suggestion, 'company' as type
                    FROM startups 
                    WHERE company_name LIKE ? 
                    LIMIT 5
                ";
                $companies = $this->startup->db->fetchAll($sql, [$searchTerm]);
                $suggestions = array_merge($suggestions, $companies ?: []);

                // Location suggestions
                $sql = "
                    SELECT DISTINCT location as suggestion, 'location' as type
                    FROM startups 
                    WHERE location LIKE ? AND location IS NOT NULL AND location != ''
                    LIMIT 3
                ";
                $locations = $this->startup->db->fetchAll($sql, [$searchTerm]);
                $suggestions = array_merge($suggestions, $locations ?: []);

            } else {
                // Investor company name suggestions
                $sql = "
                    SELECT DISTINCT company_name as suggestion, 'company' as type
                    FROM investors 
                    WHERE company_name LIKE ? AND company_name IS NOT NULL AND company_name != ''
                    LIMIT 5
                ";
                $companies = $this->investor->db->fetchAll($sql, [$searchTerm]);
                $suggestions = array_merge($suggestions, $companies ?: []);

                // Investor name suggestions
                $sql = "
                    SELECT DISTINCT CONCAT(u.first_name, ' ', u.last_name) as suggestion, 'person' as type
                    FROM investors i
                    JOIN users u ON i.user_id = u.id
                    WHERE (u.first_name LIKE ? OR u.last_name LIKE ?)
                    LIMIT 5
                ";
                $people = $this->investor->db->fetchAll($sql, [$searchTerm, $searchTerm]);
                $suggestions = array_merge($suggestions, $people ?: []);
            }

            return array_slice($suggestions, 0, 10);
        } catch (Exception $e) {
            error_log("Search suggestions error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get popular search terms and trends
     */
    public function getSearchTrends()
    {
        try {
            // Get popular industries
            $popularIndustries = $this->industry->getActiveIndustries();

            // Get trending funding stages
            $sql = "
                SELECT stage, COUNT(*) as count
                FROM startups
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY stage
                ORDER BY count DESC
                LIMIT 5
            ";
            $trendingStages = $this->startup->db->fetchAll($sql) ?: [];

            // Get active locations
            $sql = "
                SELECT location, COUNT(*) as count
                FROM startups
                WHERE location IS NOT NULL AND location != ''
                AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY location
                ORDER BY count DESC
                LIMIT 5
            ";
            $activeLocations = $this->startup->db->fetchAll($sql) ?: [];

            return [
                'popular_industries' => $popularIndustries,
                'trending_stages' => $trendingStages,
                'active_locations' => $activeLocations
            ];
        } catch (Exception $e) {
            error_log("Search trends error: " . $e->getMessage());
            return [
                'popular_industries' => [],
                'trending_stages' => [],
                'active_locations' => []
            ];
        }
    }
}