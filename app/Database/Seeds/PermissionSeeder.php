<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'view_dashboard',
            'view_record',
            'view_payment',
            'view_organization',
            'view_totalIncome',
            'view_totalExpenses',
            'view_category',
            'view_admin',
            'view_settings',
            'create_record',
            'update_record',
            'delete_record',
            'create_payment',
            'update_payment',
            'delete_payment',
            'create_organization',
            'update_organization',
            'delete_organization',
            'create_category',
            'update_category',
            'delete_category',
            'create_admin',
            'update_admin',
            'delete_admin',
            'manage_roles',
            'manage_permissions'
        ];

        $data = [];
        foreach ($permissions as $permission) {
            $data[] = [
                'permission_name' => $permission,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }

        $this->db->table('permissions')->insertBatch($data);
    }
}
