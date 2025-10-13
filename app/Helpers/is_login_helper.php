<?php

use App\Libraries\JWTService;
use App\Models\SellerModel;
use App\Models\UsersModel;

if (!function_exists('is_login_redirect')) {

    function is_login_redirect($next_url = '')
    {
        if(session()->get('user_id')){
            if (!empty($next_url)) {
                $decoded_url = base64_decode($next_url, true);
                
                // Check if it's valid base64 and a valid URL
                if ($decoded_url !== false && filter_var($decoded_url, FILTER_VALIDATE_URL)) {
                    // Ensure it's an internal URL for security
                    $base_domain = parse_url(base_url(), PHP_URL_HOST);
                    $next_domain = parse_url($decoded_url, PHP_URL_HOST);
                    
                    if ($base_domain === $next_domain) {
                        return $decoded_url;
                    }
                }
                
                // If not base64 encoded, check if it's a relative path or valid URL
                if (filter_var($next_url, FILTER_VALIDATE_URL)) {
                    $base_domain = parse_url(base_url(), PHP_URL_HOST);
                    $next_domain = parse_url($next_url, PHP_URL_HOST);
                    
                    if ($base_domain === $next_domain) {
                        return $next_url;
                    }
                } elseif (strpos($next_url, '/') === 0) {
                    // Relative path starting with /
                    return base_url($next_url);
                } elseif (!empty($next_url)) {
                    // Relative path without leading /
                    return base_url($next_url);
                }
            }
            
            // Default fallback
            return base_url('big-bonus');
        }
        return base_url('register');
    }
}