<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table      = 'tb_category';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['uuid', 'node_id', 'parent_id', 'name', 'level', 'sort', 'status'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = '';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function calculateStats($categories)
    {
        $stats = [
            'total' => count($categories),
            'root_categories' => 0,
            'sub_categories' => 0,
            'active' => 0,
            'inactive' => 0
        ];

        foreach ($categories as $category) {
            // Count root vs sub categories
            $parentId = $category['parent_id'] ?? 0;
            if ($parentId == 0) {
                $stats['root_categories']++;
            } else {
                $stats['sub_categories']++;
            }

            // Count active vs inactive
            if ($category['status'] == 'active') {
                $stats['active']++;
            } else {
                $stats['inactive']++;
            }
        }

        return $stats;
    }

    public function get_all_categories()
    {
        $builder = $this->db->table($this->table . ' as c');
        $builder->select('c.id, c.node_id, c.name, c.parent_id, c.level, c.sort, c.status');
        $query = $builder->get();
        return $query->getResultArray();
    }

    public function get_categories_hierarchical()
    {
        $categories = $this->where('status', 'active')
                          ->orderBy('sort', 'ASC')
                          ->orderBy('name', 'ASC')
                          ->findAll();

        $hierarchical = [];
        $children = [];

        // แยกหมวดหมู่หลักและหมวดหมู่ย่อย
        foreach ($categories as $category) {
            if ($category['parent_id'] == 0) {
                $hierarchical[] = $category;
            } else {
                $children[$category['parent_id']][] = $category;
            }
        }

        // รวมหมวดหมู่ย่อยเข้ากับหมวดหมู่หลัก
        foreach ($hierarchical as &$parent) {
            $parent['children'] = $children[$parent['node_id']] ?? [];
        }

        return $hierarchical;
    }
}
