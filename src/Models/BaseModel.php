<?php

namespace Models;

use Core\Database;

abstract class BaseModel
{
    public $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function findBy($column, $value)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ?";
        return $this->db->fetch($sql, [$value]);
    }

    public function all()
    {
        $sql = "SELECT * FROM {$this->table}";
        return $this->db->fetchAll($sql);
    }

    public function create($data)
    {
        $filteredData = $this->filterFillable($data);
        
        if (empty($filteredData)) {
            throw new \Exception("No valid data provided for creation");
        }

        $columns = implode(', ', array_keys($filteredData));
        $placeholders = ':' . implode(', :', array_keys($filteredData));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        
        return $this->db->insert($sql, $filteredData);
    }

    public function update($id, $data)
    {
        $filteredData = $this->filterFillable($data);
        
        if (empty($filteredData)) {
            throw new \Exception("No valid data provided for update");
        }

        $setClause = implode(' = ?, ', array_keys($filteredData)) . ' = ?';
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = ?";
        
        $params = array_values($filteredData);
        $params[] = $id;
        
        return $this->db->update($sql, $params);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->delete($sql, [$id]);
    }

    public function where($conditions = [], $limit = null, $offset = null, $orderBy = null)
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];

        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $column => $value) {
                $whereClause[] = "{$column} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }

        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }

        if ($limit) {
            $sql .= " LIMIT {$limit}";
            if ($offset) {
                $sql .= " OFFSET {$offset}";
            }
        }

        return $this->db->fetchAll($sql, $params);
    }

    protected function filterFillable($data)
    {
        if (empty($this->fillable)) {
            return $data;
        }

        return array_intersect_key($data, array_flip($this->fillable));
    }

    protected function hideFields($data)
    {
        if (empty($this->hidden) || empty($data)) {
            return $data;
        }

        if (isset($data[0])) {
            // Multiple records
            return array_map(function($record) {
                return array_diff_key($record, array_flip($this->hidden));
            }, $data);
        } else {
            // Single record
            return array_diff_key($data, array_flip($this->hidden));
        }
    }

    public function paginate($page = 1, $perPage = 20, $conditions = [])
    {
        $offset = ($page - 1) * $perPage;
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $column => $value) {
                $whereClause[] = "{$column} = ?";
                $params[] = $value;
            }
            $countSql .= " WHERE " . implode(' AND ', $whereClause);
        }
        
        $totalResult = $this->db->fetch($countSql, $params);
        $total = $totalResult['total'];
        
        // Get records
        $records = $this->where($conditions, $perPage, $offset);
        
        return [
            'data' => $records,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ];
    }
}
