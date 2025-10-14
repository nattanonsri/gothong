<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            "view_dashboard",
            "view_category",
            "view_record",
            "view_admin",
            "view_organization",
            "view_totalIncome",
            "view_totalExpenses",
            "view_reports",
            "view_settings",
            "view_payment",
            "view_profile"
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
