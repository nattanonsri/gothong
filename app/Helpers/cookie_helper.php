<?php

if (!function_exists('setCookieUser')) {
    function setCookieUser()
    {
        // Load the cookie helper
        helper('cookie');

        $cookie = get_cookie('cookie_user_uuid');

        if (!empty($cookie)) {
            return $cookie;
        }

        // Ensure the Ramsey UUID library is correctly used
        $uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();


        // Define the expiration time (10 years in seconds)
        $expiryTime = 10 * 365 * 24 * 60 * 60;

        // Set the cookie
        set_cookie('cookie_user_uuid', $uuid, $expiryTime);

        // Retrieve and display the cookie value
        $cookieValue = get_cookie('cookie_user_uuid');

        return $cookieValue;
    }
}


if (!function_exists('updateCookie')) {
    function updateCookie($user_id = '', $cookie_uuid = '')
    {
        if (empty($user_id)) {
            return false;
        }

        $cookie = $cookie_uuid ? $cookie_uuid : setCookieUser();

        if (empty($cookie)) {
            return false;
        }

        $usersModel = new \App\Models\UsersModel();
        $user = $usersModel->where('id', $user_id)->first();

        if (empty($user)) {
            return false;
        }

        $userCookie = new \App\Models\UserCookieModel();
        $cookieUser = $userCookie->where('uuid', $cookie)->orderBy('id', 'DESC')->first();
        
        $userData = [
            "username" => $user['username'],
            "email" => $user['email'],
            "uuid" => $user['uuid'],
            "displayName" => (!empty($user['first_name']) ? $user['first_name'] . ' ' . ($user['last_name'] ?? '') : ''),
            "social" => $user['auth'] ?? '',
        ];

        if (empty($cookieUser) || empty($cookieUser['raw_data'])) {
            $userCookie->insert([
                'uuid' => $cookie,
                'raw_data' => json_encode([$userData])
            ]);
        } else {
            $user_raw_data = !empty($cookieUser['raw_data']) ? json_decode($cookieUser['raw_data'], true) : [];

            $userFound = false;
            foreach ($user_raw_data as $index => $existingUser) {
                if ($existingUser['uuid'] === $user['uuid']) {
                    $user_raw_data[$index] = $userData;
                    $userFound = true;
                    break;
                }
            }

            if (!$userFound) {
                $user_raw_data[] = $userData;
            }

            return $userCookie->update($cookieUser['id'], ['raw_data' => json_encode($user_raw_data)]);
        }
    }
}

if (!function_exists('updateCookieJsonProfile')) {
    function updateCookieJsonProfile($user_id = '')
    {
        if (!$user_id) {
            return false;
        }

        $cookie = setCookieUser();
        if (empty($cookie)) {
            return false;
        }
        $usersModel = new \App\Models\UsersModel();
        $user = $usersModel->where('id', $user_id)->first();

        $userCookie = new \App\Models\UserCookieModel();
        $cookieUser = $userCookie->where('uuid', $cookie)->first();

        // $userProfileModel = new \App\Models\UserProfileModel();
        // $profile = $userProfileModel->where('user_id', $user['user_id'])->first();

        if (empty($cookieUser)) {
            return false;
        }
        $user_raw_data = !empty($cookieUser['raw_data']) ? json_decode($cookieUser['raw_data'], true) : [];

        $new_raw_data = [];
        foreach ($user_raw_data as $existingUser) {
            $displayName = $existingUser['displayName'] ?? '';
            $avatarPicture = $existingUser['profile'] ?? '';

            if ($existingUser['uuid'] === $user['uuid']) {
                $displayName = $profile['display_name'] ?? '';
                $avatarPicture = $profile['avatar_picture'] ?? '';
            }

            $new_raw_data[] = [
                "username" => $existingUser['username'],
                "uuid" => $existingUser['uuid'],
                "displayName" => $displayName,
                "profile" => $avatarPicture,
                "social" => $existingUser['social'] ?? '',
            ];
        }

        $userCookie->update($cookieUser['id'], ['raw_data' => json_encode($new_raw_data)]);
    }
}
