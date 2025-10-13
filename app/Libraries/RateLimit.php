<?php

namespace App\Libraries;

use CodeIgniter\Config\Services;

class RateLimit
{
    public function hit($key, $limit = 5, $interval = 60)
    {
        $cache = Services::cache();
        $sanitizedKey = $this->sanitizeCacheKey($key);
        $cacheKey = 'rate_limit_' . $sanitizedKey;

        // Check if the key exists in the cache
        if ($cache->get($cacheKey) === false) {
            // If the key doesn't exist, create it with an expiration time
            $cache->save($cacheKey, 1, $interval);
            return true; // Rate limit not exceeded
        } else {
            // If the key exists, increment its value
            $hits = $cache->increment($cacheKey);

            // If the limit is reached, return false
            if ($hits > $limit) {
                return false; // Rate limit exceeded
            }

            return true; // Rate limit not exceeded
        }
    }

    private function sanitizeCacheKey($key)
    {
        // Sanitize the cache key by replacing reserved characters
        return str_replace(['{', '}', '(', ')', '/', '\\', '@', ':'], '_', $key);
    }

}
