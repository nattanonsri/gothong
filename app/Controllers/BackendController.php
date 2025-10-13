<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\UserRoleModel;
use Ramsey\Uuid\Uuid;
use Config\Database;

class BackendController extends BaseController
{
    protected $db;
    protected $userModel, $userRoleModel;
    protected $currentDate;
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->userRoleModel = new UserRoleModel();
        $this->db =  Database::connect();
        $this->currentDate = date('Y-m-d');
    }

    public function index()
    {
        if (IS_LOGIN) {
            return redirect()->to(base_url('backend/dashboard'));
        } else {
            return redirect()->to(base_url('backend/login'));
        }
    }
    //login
    public function login()
    {
        if (IS_LOGIN) {
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

        $redirect = base_url('backend/dashboard');

        return $this->response->setJSON(['status' => 200, 'redirect' => $redirect]);
    }

    //logout
    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to(base_url('backend/login'));
    }
}
