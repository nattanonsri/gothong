<?php

use Google\Service\CloudAsset\Asset;

if (!function_exists('asset_url')) {
    function asset_url($uri = '')
    {
        // Assuming your asset base URL is set in the App Config
        $baseUrl = config('App')->baseURL.'';

        return rtrim($baseUrl, '/') . '/' . ltrim($uri, '/');
    }
}
