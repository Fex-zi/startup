<?php

namespace Models;

class Industry extends BaseModel
{
    protected $table = 'industries';
    protected $fillable = ['name', 'slug', 'description', 'is_active'];

    public function getActiveIndustries()
    {
        return $this->where(['is_active' => true], null, null, 'name ASC');
    }

    public function findBySlug($slug)
    {
        return $this->findBy('slug', $slug);
    }

    public function getIndustryWithCounts($industryId)
    {
        $sql = "
            SELECT 
                i.*,
                (SELECT COUNT(*) FROM startups s WHERE s.industry_id = i.id) as startup_count,
                (SELECT COUNT(*) FROM investors inv WHERE JSON_CONTAINS(inv.preferred_industries, CAST(i.id AS JSON))) as investor_count
            FROM industries i
            WHERE i.id = ?
        ";
        
        return $this->db->fetch($sql, [$industryId]);
    }

    public function getIndustriesWithCounts()
    {
        $sql = "
            SELECT 
                i.*,
                (SELECT COUNT(*) FROM startups s WHERE s.industry_id = i.id) as startup_count,
                (SELECT COUNT(*) FROM investors inv WHERE JSON_CONTAINS(inv.preferred_industries, CAST(i.id AS JSON))) as investor_count
            FROM industries i
            WHERE i.is_active = 1
            ORDER BY i.name ASC
        ";
        
        return $this->db->fetchAll($sql);
    }

    public function getPopularIndustries($limit = 10)
    {
        $sql = "
            SELECT 
                i.*,
                (SELECT COUNT(*) FROM startups s WHERE s.industry_id = i.id) as startup_count,
                (SELECT COUNT(*) FROM investors inv WHERE JSON_CONTAINS(inv.preferred_industries, CAST(i.id AS JSON))) as investor_count,
                ((SELECT COUNT(*) FROM startups s WHERE s.industry_id = i.id) + 
                 (SELECT COUNT(*) FROM investors inv WHERE JSON_CONTAINS(inv.preferred_industries, CAST(i.id AS JSON)))) as total_count
            FROM industries i
            WHERE i.is_active = 1
            ORDER BY total_count DESC
            LIMIT ?
        ";
        
        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * FIXED: Get industry names by IDs for displaying preferred industries
     */
    public function getIndustryNamesByIds($industryIds)
    {
        if (empty($industryIds) || !is_array($industryIds)) {
            return [];
        }
        
        // Sanitize the IDs
        $sanitizedIds = array_filter($industryIds, 'is_numeric');
        if (empty($sanitizedIds)) {
            return [];
        }
        
        $placeholders = str_repeat('?,', count($sanitizedIds) - 1) . '?';
        $sql = "SELECT id, name FROM industries WHERE id IN ($placeholders) AND is_active = 1 ORDER BY name ASC";
        
        return $this->db->fetchAll($sql, $sanitizedIds);
    }

    /**
     * FIXED: Get single industry name by ID
     */
    public function getIndustryNameById($industryId)
    {
        if (!is_numeric($industryId)) {
            return null;
        }
        
        $industry = $this->find($industryId);
        return $industry ? $industry['name'] : null;
    }
}
