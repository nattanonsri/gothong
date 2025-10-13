<?php

use App\Libraries\JWTService;
use App\Models\AddressModel;
use App\Models\EventModel;



if (!function_exists('isAllowedUrl')) {

    function isAllowedUrl($url)
    {
        $url = filter_var($url, FILTER_SANITIZE_URL);
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        $whitelist = config('AuthorizedDomains')->domains;
        $parsedUrl = parse_url($url);
        $urlHost = strtolower($parsedUrl['host']);
        $urlPath = isset($parsedUrl['path']) ? rtrim(strtolower($parsedUrl['path']), '/') : '';
        foreach ($whitelist as $allowedUrl) {
            $parsedAllowedUrl = parse_url($allowedUrl);
            $allowedUrlHost = strtolower($parsedAllowedUrl['host']);
            $allowedUrlPath = isset($parsedAllowedUrl['path']) ? rtrim(strtolower($parsedAllowedUrl['path']), '/') : '';

            $urlBaseDomain = getBaseDomain($urlHost);
            $allowedUrlBaseDomain = getBaseDomain($allowedUrlHost);

            if ($urlBaseDomain === $allowedUrlBaseDomain && strpos($urlPath, $allowedUrlPath) === 0) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('getBaseDomain')) {
    function getBaseDomain($host)
    {
        $hostParts = explode('.', $host);
        $numParts = count($hostParts);

        if ($numParts >= 2) {
            return $hostParts[$numParts - 2] . '.' . $hostParts[$numParts - 1];
        }

        return $host;
    }
}

if (!function_exists('generate_random_string')) {
    function generate_random_string($length = 16)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characterCount = strlen($characters);
        $randomString = '';

        $bytes = random_bytes($length);
        for ($i = 0; $i < $length; $i++) {
            $index = ord($bytes[$i]) % $characterCount;
            $randomString .= $characters[$index];
        }

        return $randomString;
    }
}
if (!function_exists('generate_random_number')) {
    function generate_random_number($length = 16)
    {
        $characters = '0123456789';
        $characterCount = strlen($characters);
        $randomString = '';

        $bytes = random_bytes($length);
        for ($i = 0; $i < $length; $i++) {
            $index = ord($bytes[$i]) % $characterCount;
            $randomString .= $characters[$index];
        }

        return $randomString;
    }
}


if (!function_exists('custom_pagination_links')) {
    function custom_pagination_links($currentPage, $perPage, $total, $class_link)
    {

        $output = '';
        $totalPages = ceil($total / $perPage);
        if ($totalPages > 1) {
            $output .= '<nav class="py-2" aria-label="Page navigation"><ul class="pagination justify-content-end">';

            // Previous page link
            if ($currentPage > 1) {
                $output .= '<li class="page-item mx-1"><a class="text-muted page-link rounded-3 ' . $class_link . '" href="#" data-page="' . ($currentPage - 1) . '"><i class="fa-solid fa-chevron-left"></i></a></li>';
            } else {
                $output .= '<li class="page-item disabled mx-1"><span class="text-muted page-link rounded-3 ' . $class_link . '" disabled><i class="fa-solid fa-chevron-left"></i></span></li>';
            }

            // Pagination links
            $visiblePages = 5; // Adjust the number of visible pages as needed
            $startPage = max(1, $currentPage - floor($visiblePages / 2));
            $endPage = min($totalPages, $startPage + $visiblePages - 1);

            for ($i = $startPage; $i <= $endPage; $i++) {
                $output .= '<li class="page-item mx-1 ' . ($currentPage == $i ? 'active' : '') . '"><a class="page-link border-0 rounded-3 ' . $class_link . '" href="#" data-page="' . $i . '">' . $i . '</a></li>';
            }

            // Next page link
            if ($currentPage < $totalPages) {
                $output .= '<li class="page-item mx-1"><a class="text-muted page-link rounded-3 ' . $class_link . '" href="#" data-page="' . ($currentPage + 1) . '"><i class="fa-solid fa-chevron-right"></i></a></li>';
            } else {
                $output .= '<li class="page-item disabled mx-1"><span class="text-muted page-link rounded-3 ' . $class_link . '" disabled><i class="fa-solid fa-chevron-right"></i></span></li>';
            }

            $output .= '</ul>';

            // Add the select dropdown for page navigation
            $output .= '<div class="d-flex justify-content-end">';
            $output .= '<select class="form-select select2 w-auto" id="page-select">';
            for ($i = 1; $i <= $totalPages; $i++) {
                $selected = ($i == $currentPage) ? ' selected' : '';
                $output .= '<option value="' . $i . '"' . $selected . '>หน้าที่ ' . $i . '</option>';
            }
            $output .= '</select>';
            $output .= '</div>';

            $output .= '</nav>';
        }
        return $output;
    }
}


if (!function_exists('curlRequest')) {

    function curlRequest($method, $url = '', $headers = '', $payload = '')
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => $headers
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl); // Get the error, if any

        curl_close($curl);

        // Save to the database
        add_log(USER_ID, 'curl', $url, $response);

        // Return the response or error
        if ($error) {
            return $error;
        } else {
            return $response;
        }
    }
}


if (!function_exists('is_current_url')) {
    function is_current_url($pattern)
    {
        $currentUrl = current_url(true);
        return (bool) preg_match('#' . $pattern . '#', $currentUrl->getPath());
    }
}

if (! function_exists('append_query_param')) {
    function append_query_param($url, $paramName, $paramValue)
    {
        // Parse the original URL
        $parsedUrl = parse_url($url);

        // Check if the URL is valid (i.e., parse_url was successful)
        if ($parsedUrl === false) {
            // Handle the case when parsing fails
            return $url; // or throw an exception, log an error, etc.
        }

        // Prepare the query parameter
        $queryParam = urlencode($paramName) . '=' . urlencode($paramValue);

        // Append the query parameter to the existing query
        if (isset($parsedUrl['query'])) {
            $parsedUrl['query'] .= '&' . $queryParam;
        } else {
            $parsedUrl['query'] = $queryParam;
        }

        // Reconstruct the new URL
        $newUrl = '';

        // Check if 'scheme' key exists before appending it
        if (isset($parsedUrl['scheme'])) {
            $newUrl .= $parsedUrl['scheme'] . '://';
        }

        if (isset($parsedUrl['host'])) {
            $newUrl .= $parsedUrl['host'];
        }

        if (isset($parsedUrl['path'])) {
            $newUrl .= $parsedUrl['path'];
        }

        if (isset($parsedUrl['query'])) {
            $newUrl .= '?' . $parsedUrl['query'];
        }

        if (isset($parsedUrl['fragment'])) {
            $newUrl .= '#' . $parsedUrl['fragment'];
        }

        return $newUrl;
    }
}

if (!function_exists('get_client_ip')) {
    function get_client_ip()
    {
        $ipaddress = '';

        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }

        // Handle comma-separated list of IP addresses
        if (strpos($ipaddress, ',') !== false) {
            $ipList = explode(',', $ipaddress);
            $ipaddress = trim($ipList[0]);
        }

        return esc($ipaddress);
    }
}


if (!function_exists('validateDate')) {
    function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}

if (!function_exists('partial_hide')) {
    function partial_hide($input, $visibleLength = 4, $hiddenChar = 'X')
    {
        $visiblePart = mb_substr($input, 0, $visibleLength, 'UTF-8');
        $hiddenPart = str_repeat($hiddenChar, max(0, mb_strlen($input, 'UTF-8') - $visibleLength));

        return $visiblePart . $hiddenPart;
    }
}
if (!function_exists('add_log')) {
    function add_log($user_id, $action, $url = null, $payload = [])
    {
        $logModel = new \App\Models\LogSQLModel();
        $request = service('request');
        $agent = $request->getUserAgent();


        $data = [
            'endpoint'   => $url ?? $request->getUri()->getPath(),
            'method'     => $action ?? $request->getMethod(),
            'user_id'    => !empty($user_id) ? $user_id : 0,
            'payload'    => json_encode($payload ?? []),
            'ip'         => function_exists('get_client_ip') ? get_client_ip() : $request->getIPAddress(),
            'browser'    => $agent->getBrowser(),
            'os'         => $agent->getPlatform(),
            'user_agent' => $agent->getAgentString(),
            'created_at' => CURRENT_DATE,
        ];

        $logModel->insert($data);
    }
}

// if (!function_exists('isFieldDuplicate')) {
//     function isFieldDuplicate($model, $field, $value, $user_id = null)
//     {
//         $query = $model->where($field, $value);

//         if ($user_id !== null) {
//             $query->where('id !=', $user_id);
//         }
//         return  $query->first();
//     }
// }
// if (!function_exists('add_log_admin')) {
//     function add_log_admin($req)
//     {
//         $admin_id = $req['admin_id'] ?? 0;
//         $user_id = $req['user_id'] ?? 0;
//         $name = $req['name'] ?? '';
//         $url = $req['url'] ?? '';
//         $result = $req['result'] ?? [];

//         $logModel = new \App\Models\LogAdminModel();
//         $request = [];
//         $agent = [];
//         if (!is_cli()) {
//             $request = service('request');
//             $agent = $request->getUserAgent();
//         }

//         $data = [
//             'admin_id' => $admin_id,
//             'user_id' => $user_id,
//             'name' => $name,
//             'endpoint' => $url,
//             'result' => json_encode($result),
//             'ip' => get_client_ip() ?? !empty($request->getIPAddress()) ? $request->getIPAddress() : '',
//             'browser' => !empty($agent->getBrowser()) ? $agent->getBrowser() : '',
//             'os' => !empty($agent->getPlatform()) ? $agent->getPlatform() : '',
//             'user_agent' => !empty($agent->getAgentString()) ? $agent->getAgentString() : '',
//             'created_at' => CURRENT_DATE,
//             'updated_at' => CURRENT_DATE,
//         ];

//         $logModel->insert($data);
//     }
// }

if (!function_exists('profile_background_body')) {
    function profile_background_body(): string
    {
        return 'background-color:#E6E6E6;';
    }
}

if (!function_exists('select_gender')) {
    function select_gender(): array
    {
        return [
            1 => [
                'key' => 'male',
                'name' => 'ชาย',
            ],
            2 => [
                'key' => 'female',
                'name' => 'หญิง',
            ],
            3 => [
                'key' => 'lgbtq',
                'name' => 'LGBTQ+',
            ],
            4 => [
                'key' => 'not-specified',
                'name' => 'ไม่ระบุ',
            ],
        ];
    }
}


if (!function_exists('has_permission')) {
    function has_permission($permission)
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

            return in_array($permission, $permissionNames);
        }

        return false;
    }
}

if (!function_exists('convertToThaiDate')) {
    function convertToThaiDate($date)
    {
        if (empty($date)) {
            return '';
        }

        $thai_month_arr = [
            "มกราคม",
            "กุมภาพันธ์",
            "มีนาคม",
            "เมษายน",
            "พฤษภาคม",
            "มิถุนายน",
            "กรกฎาคม",
            "สิงหาคม",
            "กันยายน",
            "ตุลาคม",
            "พฤศจิกายน",
            "ธันวาคม"
        ];

        $date_time = new \DateTime($date);
        $thai_year = $date_time->format('Y') + 543;
        $month = $thai_month_arr[$date_time->format('n') - 1];
        $day = $date_time->format('j');
        $time = $date_time->format('H:i:s');

        return "$day $month พ.ศ. $thai_year เวลา $time น.";
    }
}

if (!function_exists('FormatDateThai')) {
    function FormatDateThai($date)
    {
        if (empty($date)) {
            return '';
        }

        $thai_month_arr = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];

        $date_time = new \DateTime($date);
        $thai_year = $date_time->format('Y') + 543;
        $month = $thai_month_arr[$date_time->format('n') - 1];
        $day = $date_time->format('j');
        $time = $date_time->format('H:i');

        return "$day $month $thai_year เวลา $time น.";
    }
}



function generate_random_string_with_prefix(string $prefix, int $length, int $strength = 4): string
{
    if ($length <= strlen($prefix)) {
        throw new InvalidArgumentException("ความยาวต้องมากกว่าความยาวของคำนำหน้า");
    }

    if ($strength < 1 || $strength > 5) {
        throw new InvalidArgumentException("ค่า strength ต้องอยู่ระหว่าง 1 ถึง 5");
    }

    $randomLength = $length - strlen($prefix);

    // เลือกชุดตัวอักษรตามระดับความแข็งแกร่ง
    $characters = match ($strength) {
        1 => '0123456789',
        2 => 'abcdefghijklmnopqrstuvwxyz',
        3 => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
        4 => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
        5 => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=[]{}|;:,.<>?',
        default => throw new InvalidArgumentException("ค่า strength ไม่ถูกต้อง"),
    };

    $bytes = random_bytes($randomLength);
    $position = 0;
    $result = $prefix;

    while ($position < $randomLength) {
        $random = ord($bytes[$position++]);
        $index = $random % strlen($characters);
        $result .= $characters[$index];
    }

    return $result;
}

