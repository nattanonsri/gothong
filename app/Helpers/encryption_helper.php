<?php
if (!function_exists('encrypt')) {

    function encrypt(string $string)
    {
        $key = $_ENV['ENCRYPT_KEY'];
        if(empty($key)){
            return false;
        }
        $cipher = 'AES-256-CBC';
        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivLength);
        $encrypted = openssl_encrypt($string, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $encrypted, $key, true);
        return bin2hex($iv . $hmac . $encrypted);
    }
}


if (!function_exists('xorEncrypt')) {

    function xorEncrypt(string $string)
    {
        $key = $_ENV['ENCRYPT_KEY'];
        if (empty($key)) {
            return false;
        }
        $result = '';
        $keyLength = strlen($key);

        for ($i = 0; $i < strlen($string); $i++) {
            $result .= $string[$i] ^ $key[$i % $keyLength];
        }

        return base64_encode($result);
    }
}

if (!function_exists('xorDecrypt')) {

    function xorDecrypt(string $string)
    {
        $key = $_ENV['ENCRYPT_KEY'];
        if (empty($key)) {
            return false;
        }
        $string = base64_decode($string);
        $result = '';
        $keyLength = strlen($key);

        for ($i = 0; $i < strlen($string); $i++) {
            $result .= $string[$i] ^ $key[$i % $keyLength];
        }

        return $result;
    }
}

if (!function_exists('decrypt')) {
    function decrypt($string)
    {
        $key = $_ENV['ENCRYPT_KEY'];
        if(empty($key)){
            return false;
        }
        $cipher = 'AES-256-CBC';
        $string = hex2bin($string);
        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = substr($string, 0, $ivLength);
        $hmac = substr($string, $ivLength, $sha2len = 32);
        $encrypted = substr($string, $ivLength + $sha2len);
        $original = openssl_decrypt($encrypted, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $encrypted, $key, true);
        if (hash_equals($hmac, $calcmac)) {
            return $original;
        }
        return false;
    }
}
