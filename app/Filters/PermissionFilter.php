<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class PermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $user_id = $session->get('user_id');

        if ($user_id) {
            $db = \Config\Database::connect();
            $builder = $db->table('tb_user_role as user_role');
            $builder->select('permissions.permission_name');
            $builder->join('role_permissions', 'user_role.role_id = role_permissions.role_id');
            $builder->join('permissions', 'role_permissions.permission_id = permissions.id');
            $builder->where('user_role.user_id', $user_id);
            $query = $builder->get();
            $permissions = $query->getResultArray();

            $permissionNames = array_column($permissions, 'permission_name');

            if (!in_array($arguments[0], $permissionNames)) {
                return redirect()->to('/access_denied');
            }
        } else {

            $user_login = !empty(uri_string()) ? 'backend/login?next='.urlencode(env('ROOT_URL').uri_string()) : 'backend/login';

            return redirect()->to($user_login);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}