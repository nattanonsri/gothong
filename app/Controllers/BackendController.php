<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\UserRoleModel;
use App\Models\OrganizationModel;
use App\Models\RoleModel;
use Ramsey\Uuid\Uuid;
use Config\Database;

class BackendController extends BaseController
{
    protected $db;
    protected $userModel, $userRoleModel, $organizationModel, $roleModel;
    protected $currentDate;
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->userRoleModel = new UserRoleModel();
        $this->organizationModel = new OrganizationModel();
        $this->roleModel = new RoleModel();
        $this->db =  Database::connect();
        $this->currentDate = date('Y-m-d');
    }

    public function index()
    {
        $session = session();
        if ($session->get('isLoggedIn')) {
            return redirect()->to(base_url('backend/dashboard'));
        } else {
            return redirect()->to(base_url('backend/login'));
        }
    }
    
    //login
    public function login()
    {
        $session = session();
        if ($session->get('isLoggedIn')) {
            return redirect()->to(base_url('backend/dashboard'));
        }

        return view('backend/login');
    }

    public function admin_Auth()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $rules = [
            'username' => ['label' => 'Username', 'rules' => 'required'],
            'password' => ['label' => 'Password', 'rules' => 'required'],
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 400,
                'message' => $this->validator->getErrors()
            ])->setStatusCode(400);
        }

        if (empty($username) || empty($password)) {
            return $this->response->setJSON([
                'status' => 400,
                'message' => 'กรุณากรอกชื่อผู้ใช้และรหัสผ่าน'
            ])->setStatusCode(400);
        }

        $user = $this->userModel->where('username', $username)->first();

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->response->setJSON([
                'status' => 400,
                'message' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง'
            ])->setStatusCode(400);
        }
        $role = $this->userRoleModel->where('user_id', $user['id'])->first();

        $session = session();
        $ses_data = [
            'user_id'    => $user['id'],
            'username'   => $user['username'],
            'role_id'    => $role['role_id'],
            'isLoggedIn' => TRUE
        ];

        if (function_exists('add_log')) {
            add_log($user['id'], 'login', 'backend/login', 'Login Success');
        }

        $session->set($ses_data);

        // ตรวจสอบสิทธิ์ของผู้ใช้และหาหน้าที่สามารถเข้าถึงได้
        $redirect = $this->getUserAccessiblePage($user['id']);

        return $this->response->setJSON(['status' => 200, 'redirect' => $redirect]);
    }

    public function profile()
    {
        $user = $this->userModel->find(USER_ID);
        
        // ดึงข้อมูล role ของผู้ใช้
        $userRole = $this->userRoleModel->where('user_id', USER_ID)->first();
        $role = null;
        if ($userRole) {
            $role = $this->roleModel->find($userRole['role_id']);
        }
        
        // ดึงข้อมูล organization ของผู้ใช้
        $organization = null;
        // if ($user['organization_id']) {
        //     $organization = $this->organizationModel->find($user['organization_id']);
        // }
        
        $data['user'] = $user;
        $data['role'] = $role;
        $data['organization'] = $organization;
        
        return view('backend/member/profile', $data);
    }

    public function edit_profile()
    {
        $user = $this->userModel->find(USER_ID);
        
        // ดึงข้อมูล organization ที่มี parend_id = 0 (องค์กรหลัก)
        $parentOrganizations = $this->organizationModel->where('parend_id', 0)->findAll();
        
        // ถ้าไม่มี organization หลักเลย ให้แสดงทั้งหมดที่เป็น parend_id ย่อยที่สุด
        if (empty($parentOrganizations)) {
            // หา parend_id ที่มีค่าสูงสุด (ย่อยที่สุด)
            $maxParentId = $this->organizationModel->selectMax('parend_id')->first();
            if ($maxParentId && $maxParentId['parend_id'] > 0) {
                $data['organizations'] = $this->organizationModel->where('parend_id', $maxParentId['parend_id'])->findAll();
            } else {
                $data['organizations'] = $this->organizationModel->findAll();
            }
        } else {
            $data['organizations'] = $parentOrganizations;
        }

        if (empty($user)) {
            return redirect()->to(base_url('backend/profile'))->with('error', 'ไม่พบข้อมูลผู้ใช้');
        }

        // ดึงข้อมูล role ของผู้ใช้
        $userRole = $this->userRoleModel->where('user_id', USER_ID)->first();
        $role = null;
        if ($userRole) {
            $role = $this->roleModel->find($userRole['role_id']);
        }
        
        // ดึงข้อมูล organization ของผู้ใช้
        $organization = null;
        // if ($user['organization_id']) {
        //     $organization = $this->organizationModel->find($user['organization_id']);
        // }

        $data['user'] = $user;
        $data['role'] = $role;
        $data['organization'] = $organization;

        return view('backend/member/edit_profile', $data);
    }

    public function update_profile()
    {
        $user = $this->userModel->find(USER_ID);

        if (empty($user)) {
            return $this->response->setJSON([
                'status' => 400,
                'message' => 'ไม่พบข้อมูลผู้ใช้'
            ])->setStatusCode(400);
        }

        $rules = [
            'first_name_th' => ['label' => 'ชื่อ (ไทย)', 'rules' => 'required|min_length[2]|max_length[100]'],
            'last_name_th' => ['label' => 'นามสกุล (ไทย)', 'rules' => 'required|min_length[2]|max_length[100]'],
            'first_name_en' => ['label' => 'ชื่อ (อังกฤษ)', 'rules' => 'required|min_length[2]|max_length[100]'],
            'last_name_en' => ['label' => 'นามสกุล (อังกฤษ)', 'rules' => 'required|min_length[2]|max_length[100]'],
            'email' => ['label' => 'อีเมล', 'rules' => 'required|valid_email|max_length[255]'],
            'phone' => ['label' => 'เบอร์โทรศัพท์', 'rules' => 'required|min_length[10]|max_length[15]'],
            'gender' => ['label' => 'เพศ', 'rules' => 'required|in_list[male,female,other]'],
            'birth_date' => ['label' => 'วันเกิด', 'rules' => 'required|valid_date[Y-m-d]'],
            // 'organization_id' => ['label' => 'องค์กร', 'rules' => 'required']
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 400,
                'message' => $this->validator->getErrors()
            ])->setStatusCode(400);
        }

        // ตรวจสอบ email ซ้ำ
        $existingUser = $this->userModel->where('email', $this->request->getPost('email'))
            ->where('id !=', USER_ID)
            ->first();

        if ($existingUser) {
            return $this->response->setJSON([
                'status' => 400,
                'message' => 'อีเมลนี้ถูกใช้งานแล้ว'
            ])->setStatusCode(400);
        }

        $updateData = [
            'perfix_th' => $this->request->getPost('perfix_th'),
            'first_name_th' => $this->request->getPost('first_name_th'),
            'last_name_th' => $this->request->getPost('last_name_th'),
            'perfix_en' => $this->request->getPost('perfix_en'),
            'first_name_en' => $this->request->getPost('first_name_en'),
            'last_name_en' => $this->request->getPost('last_name_en'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'gender' => $this->request->getPost('gender'),
            'birth_date' => $this->request->getPost('birth_date'),
            // 'organization_id' => $this->request->getPost('organization_id')
        ];

        // จัดการการอัปโหลดรูปภาพ
        $imageFile = $this->request->getFile('image_profile');
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            $newName = $imageFile->getRandomName();

            if(!is_dir(WRITEPATH . 'uploads/profiles/')) {
                mkdir(WRITEPATH . 'uploads/profiles/', 0777, true);
            }

            $imageFile->move(WRITEPATH . 'uploads/profiles/', $newName);
            $updateData['image_profile'] = $newName;

            // ลบรูปเก่าถ้ามี
            if ($user['image_profile'] && file_exists(WRITEPATH . 'uploads/profiles/' . $user['image_profile'])) {
                unlink(WRITEPATH . 'uploads/profiles/' . $user['image_profile']);
            }
        }

        if ($this->userModel->update(USER_ID, $updateData)) {
            if (function_exists('add_log')) {
                add_log(USER_ID, 'update', 'backend/profile/edit', 'Update Profile Success');
            }

            return $this->response->setJSON([
                'status' => 200,
                'message' => 'อัปเดตข้อมูลส่วนตัวสำเร็จ'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 500,
                'message' => 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล'
            ])->setStatusCode(500);
        }
    }

    public function get_images_profile($path) {

        $path = WRITEPATH . 'uploads/profiles/' . $path;

        $mimeType = mime_content_type($path);
        $response = $this->response->setContentType($mimeType);
        $response->setBody(file_get_contents($path));
        return $response;

       if(!file_exists($path)) { 
        return $this->response->setStatusCode(400)->setJSON([
            'status' => 400,
            'message' => 'ไม่พบรูปภาพ'
        ]);
       }

    }

    /**
     * ตรวจสอบสิทธิ์ของผู้ใช้และหาหน้าที่สามารถเข้าถึงได้
     * @param int $userId
     * @return string
     */
    private function getUserAccessiblePage($userId)
    {
        // ดึงสิทธิ์ทั้งหมดของผู้ใช้
        $builder = $this->db->table('tb_user_role as user_role');
        $builder->select('permissions.permission_name');
        $builder->join('role_permissions', 'user_role.role_id = role_permissions.role_id');
        $builder->join('permissions', 'role_permissions.permission_id = permissions.id');
        $builder->where('user_role.user_id', $userId);
        $query = $builder->get();
        $permissions = $query->getResultArray();
        
        $permissionNames = array_column($permissions, 'permission_name');
        
        // กำหนดลำดับความสำคัญของหน้าที่ผู้ใช้สามารถเข้าถึงได้
        $accessiblePages = [
            'view_dashboard' => base_url('backend/dashboard'),
            'view_record' => base_url('backend/record'),
            'view_reports' => base_url('backend/reports'),
            'view_settings' => base_url('backend/settings'),
            'view_payment' => base_url('backend/payment'),
            'view_category' => base_url('backend/category'),
            'view_organization' => base_url('backend/organization'),
            'view_totalIncome' => base_url('backend/totalIncome'),
            'view_totalExpenses' => base_url('backend/totalExpenses'),
            'view_admin' => base_url('backend/admin'),

            'view_profile' => base_url('backend/profile')
        ];
        
        // หาหน้าแรกที่ผู้ใช้มีสิทธิ์เข้าถึง
        foreach ($accessiblePages as $permission => $url) {
            if (in_array($permission, $permissionNames)) {
                return $url;
            }
        }
        
        // ถ้าไม่มีสิทธิ์เข้าถึงหน้าไหนเลย ให้ไปหน้า profile (ซึ่งควรมีสิทธิ์เสมอ)
        return base_url('backend/profile');
    }

    //logout
    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to(base_url('backend/login'));
    }
}
