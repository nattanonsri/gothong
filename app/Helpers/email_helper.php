<?php

if (!function_exists('mask_email')) {
    function mask_email($email)
    {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }

        list($username, $domain) = explode('@', $email);
        $length = strlen($username);

        if ($length <= 2) {
            $masked = $username[0] . str_repeat('x', max(0, $length - 1));
        } else {
            $masked = $username[0] . str_repeat('x', $length - 2) . $username[$length - 1];
        }

        return $masked . '@' . $domain;
    }
}
