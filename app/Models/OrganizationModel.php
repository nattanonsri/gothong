<?php

namespace App\Models;

use CodeIgniter\Model;

class OrganizationModel extends Model
{
    protected $table            = 'tb_organization';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['uuid', 'node_id', 'parend_id', 'name', 'level', 'sort', 'status'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

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

    public function calculateStats($organizations)
    {
        $stats = [
            'total' => count($organizations),
            'root_organizations' => 0,
            'sub_organizations' => 0,
            'active' => 0,
            'inactive' => 0
        ];

        foreach ($organizations as $organization) {
            // Count root vs sub organizations
            $parentId = $organization['parend_id'] ?? 0;
            if ($parentId == 0) {
                $stats['root_organizations']++;
            } else {
                $stats['sub_organizations']++;
            }

            // Count active vs inactive
            if ($organization['status'] == 'active') {
                $stats['active']++;
            } else {
                $stats['inactive']++;
            }
        }

        return $stats;
    }

    public function get_all_organizations()
    {
        $builder = $this->db->table($this->table . ' as o');
        $builder->select('o.node_id, o.name, o.parend_id, o.level, o.sort, o.status');
        $query = $builder->get();
        return $query->getResultArray();
    }
}
