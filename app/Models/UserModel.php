<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'tb_user';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ["uuid","card_id","customer_id","perfix_th","first_name_th","last_name_th","perfix_en","first_name_en","last_name_en","username","password","email","phone","gender","birth_date","image_profile"];
    
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

    public function getAdminsWithRoles()
    {
        $builder = $this->select("
            {$this->table}.id,
            {$this->table}.username,
            {$this->table}.email,
            {$this->table}.phone,
            {$this->table}.gender,
            {$this->table}.birth_date,
            {$this->table}.image_profile,
            {$this->table}.created_at,
            {$this->table}.updated_at,
            GROUP_CONCAT(tb_role.name SEPARATOR ', ') as role_names,
            GROUP_CONCAT(tb_role.id SEPARATOR ',') as role_ids
                ")
            ->join("tb_user_role", "tb_user_role.user_id = {$this->table}.id", "left")
            ->join("tb_role", "tb_role.id = tb_user_role.role_id", "left")
            ->groupBy("{$this->table}.id");
        
        return $builder->findAll();
    }
}
