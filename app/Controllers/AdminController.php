<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RoleModel;
use App\Models\PermissionsModel;
use App\Models\RolePermissionModel;
use App\Models\UserModel;
use App\Models\UserRoleModel;
use Ramsey\Uuid\Uuid;

class AdminController extends BaseController
{
    protected UserModel $userModel;
    protected RoleModel $roleModel;
    protected PermissionsModel $permissionsModel;
    protected RolePermissionModel $rolePermissionModel;
    protected UserRoleModel $userRoleModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->permissionsModel = new PermissionsModel();
        $this->rolePermissionModel = new RolePermissionModel();
        $this->userRoleModel = new UserRoleModel();
    }

    public function goto_admin(): string
    {
        $data = [
            'admins' => $this->userModel->getAdminsWithRoles(),
            'roles' => $this->roleModel->findAll(),
        ];

        return view('backend/admins/manage_admin', $data);
    }

    public function addAdmin()
    {
        $data = [
            'roles' => $this->roleModel->findAll(),
        ];

        return view('backend/admins/add_admin', $data);
    }

    public function createAdmin()
    {
        // $request = $this->request->getJSON();
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $booth_id = $this->request->getPost('booth_id');
        $counter = $this->request->getPost('counter');
        $roles = $this->request->getPost('roles');

        $adminData = [
            'uuid' => Uuid::uuid4()->toString(),
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'booth_id' => $booth_id,
            'counter' => $counter ?? null,
        ];

        $adminId = $this->userModel->insert($adminData);

        if ($adminId && isset($roles)) {
            foreach ($roles as $roleId) {
                $this->userRoleModel->insert([
                    'admin_id' => $adminId,
                    'role_id' => html_entity_decode(htmlspecialchars(trim($roleId)))
                ]);
            }
        }

        return $this->response->setJSON(['success' => true, 'message' => 'สร้าง admin สำเร็จ']);
    }

    public function updateAdmin()
    {
        $username = $this->request->getPost('username');
        $booth_id = $this->request->getPost('booth_id');
        $counter = $this->request->getPost('counter');
        $password = $this->request->getPost('password');
        $id = $this->request->getPost('id');
        $roles = $this->request->getPost('roles');

        $adminData = [
            'username' => $username,
            'booth_id' => $booth_id,
            'counter' => $counter ?? null,
        ];

        if (isset($password) && !empty($password)) {
                $adminData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $adminData);
        if (isset($roles)) {
            $this->userRoleModel->where('admin_id', $id)->delete();
            foreach ($roles as $roleId) {
                $this->userRoleModel->insert([
                    'admin_id' => $id,
                    'role_id' => html_entity_decode(htmlspecialchars(trim($roleId)))
                ]);
            }
        }

        return $this->response->setJSON(['success' => true, 'message' => 'อัปเดต admin สำเร็จ']);
    }

    public function deleteAdmin()
    {
        $id = $this->request->getPost('id');
        $this->userRoleModel->where('admin_id', $id)->delete();
        $this->userModel->delete($id);
        return $this->response->setJSON(['success' => true, 'message' => 'ลบ admin สำเร็จ']);
    }

    public function getAdminRoles()
    {
        $adminId = $this->request->getGet('admin_id');
        $roles = $this->userRoleModel->getAdminRoles($adminId);

        return $this->response->setJSON(['roles' => $roles]);
    }

    public function addRoleToAdmin()
    {
        $request = $this->request->getJSON();

        $this->userRoleModel->insert([
            'admin_id' => $request->admin_id,
            'role_id' => html_entity_decode(htmlspecialchars(trim($request->role_id)))
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'เพิ่ม role สำเร็จ']);
    }

    public function removeRoleFromAdmin()
    {
        $request = $this->request->getJSON();

        $this->userRoleModel->where([
            'admin_id' => $request->admin_id,
            'role_id' => html_entity_decode(htmlspecialchars(trim($request->role_id)))
        ])->delete();

        return $this->response->setJSON(['success' => true, 'message' => 'ลบ role สำเร็จ']);
    }

    public function bulkAssignRoles()
    {
        $adminIds = $this->request->getPost('admin_ids');
        $roleIds = $this->request->getPost('role_ids');

        if (!isset($adminIds) || !isset($roleIds)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ข้อมูลไม่ครบถ้วน'
            ]);
        }
        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($adminIds as $adminId) {
            foreach ($roleIds as $roleId) {
                $existingRole = $this->userRoleModel->where([
                    'admin_id' => $adminId,
                    'role_id' => $roleId
                ])->first();

                if (!$existingRole) {
                    try {
                        $this->userRoleModel->insert([
                            'admin_id' => $adminId,
                            'role_id' => $roleId
                        ]);
                        $successCount++;
                    } catch (\Exception $e) {
                        $errorCount++;
                        $errors[] = "ไม่สามารถมอบหมาย role ID {$roleId} ให้ admin ID {$adminId}";
                    }
                }
            }
        }

        if ($errorCount > 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "มอบหมาย role สำเร็จ {$successCount} รายการ, เกิดข้อผิดพลาด {$errorCount} รายการ",
                'errors' => $errors
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => "มอบหมาย role สำเร็จ {$successCount} รายการ"
        ]);
    }

    // public function bulkRemoveRoles()
    // {
    //     $adminIds = $this->request->getPost('admin_ids');
    //     $roleIds = $this->request->getPost('role_ids');

    //     if (!isset($adminIds) || !isset($roleIds)) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'ข้อมูลไม่ครบถ้วน'
    //         ]);
    //     }
    //     $successCount = 0;

    //     foreach ($adminIds as $adminId) {
    //         foreach ($roleIds as $roleId) {
    //             $deleted = $this->adminRoleModel->where([
    //                 'admin_id' => $adminId,
    //                 'role_id' => $roleId
    //             ])->delete();

    //             if ($deleted) {
    //                 $successCount++;
    //             }
    //         }
    //     }

    //     return $this->response->setJSON([
    //         'success' => true,
    //         'message' => "ลบ role สำเร็จ {$successCount} รายการ"
    //     ]);
    // }

    public function createRole()
    {
        $name = $this->request->getPost('name');

        if (!isset($name) || empty($name)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'กรุณากรอกชื่อ Role'
            ]);
        }

        // Check if role already exists
        $existingRole = $this->roleModel->where('name', $name)->first();
        if ($existingRole) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Role นี้มีอยู่แล้ว'
            ]);
        }

        $roleId = $this->roleModel->insert([
            'name' => $name
        ]);

        if ($roleId) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'สร้าง Role สำเร็จ'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ไม่สามารถสร้าง Role ได้'
            ]);
        }
    }

    public function updateRole()
    {
        $id = $this->request->getPost('id');
        $name = $this->request->getPost('name');

        if (!isset($id) || !isset($name) || empty($name)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ข้อมูลไม่ครบถ้วน'
            ]);
        }

        // Check if role already exists (excluding current role)
        $existingRole = $this->roleModel->where('name', $name)
            ->where('id !=', $id)
            ->first();
        if ($existingRole) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Role นี้มีอยู่แล้ว'
            ]);
        }

        $updated = $this->roleModel->update($id, [
            'name' => $name
        ]);

        if ($updated) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'อัปเดต Role สำเร็จ'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ไม่สามารถอัปเดต Role ได้'
            ]);
        }
    }

    public function deleteRole()
    {
        $id = $this->request->getPost('id');

        if (!isset($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ไม่พบ ID ของ Role'
            ]);
        }

        // Check if role is being used by any admin
        $adminRoles = $this->userRoleModel->where('role_id', $id)->countAllResults();
        if ($adminRoles > 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ไม่สามารถลบ Role นี้ได้ เนื่องจากมีผู้ดูแลระบบใช้งานอยู่'
            ]);
        }

        $deleted = $this->roleModel->delete($id);

        if ($deleted) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'ลบ Role สำเร็จ'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ไม่สามารถลบ Role ได้'
            ]);
        }
    }

    // Permission Management Methods
    public function getAllPermissions()
    {
        $permissions = $this->permissionsModel->getPermissions();
        
        return $this->response->setJSON([
            'success' => true,
            'permissions' => $permissions
        ]);
    }

    public function getRolePermissions()
    {
        $roleId = $this->request->getGet('role_id');
        
        if (!isset($roleId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ไม่พบ Role ID'
            ]);
        }

        $rolePermissions = $this->rolePermissionModel->where('role_id', $roleId)->findAll();
        
        return $this->response->setJSON([
            'success' => true,
            'permissions' => $rolePermissions
        ]);
    }

    public function saveRolePermissions()
    {
        $roleId = $this->request->getPost('role_id');
        $permissionIds = $this->request->getPost('permission_ids');
        
        if (!isset($roleId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ไม่พบ Role ID'
            ]);
        }

        // Delete existing permissions for this role
        $this->rolePermissionModel->where('role_id', $roleId)->delete();
        
        // Insert new permissions
        if (isset($permissionIds) && is_array($permissionIds)) {
            foreach ($permissionIds as $permissionId) {
                $this->rolePermissionModel->insert([
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                    'status' => 1
                ]);
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'บันทึก Permission สำเร็จ'
        ]);
    }
}
