<?php

namespace App\Libraries;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

class JWTService
{

    protected $secretKey;
    protected $algorithm;

    public function __construct()
    {
        $this->secretKey = getenv('JWT_SECRET'); // Replace with your own secret key
        $this->algorithm = 'HS256';
    }

    public function generateToken($payload, $expire = 60)
    {
        $issuedAt = time();
        $expireAt = $issuedAt + $expire;

        if(empty($payload)){
            return false;
        }
        $token = [
            'iat'  => $issuedAt,      // Issued At: Time when the token was generated
            'exp'  => $expireAt,      // Expiration Time: Time when the token expires
            'data' => $payload        // Payload: Data to be encoded in the token
        ];

        return JWT::encode($token, $this->secretKey, $this->algorithm);
    }

    public function generateUrlSafeToken($payload, $expire = 60)
    {
        $issuedAt = time();
        $expireAt = $issuedAt + $expire;

        if(empty($payload)){
            return false;
        }
        $token = [
            'iat'  => $issuedAt,      // Issued At: Time when the token was generated
            'exp'  => $expireAt,      // Expiration Time: Time when the token expires
            'data' => $payload        // Payload: Data to be encoded in the token
        ];

        $jwt = JWT::encode($token, $this->secretKey, $this->algorithm);
        $urlSafeToken = str_replace(['+', '/', '='], ['-', '_', ''], $jwt);
        return  $urlSafeToken;

    }

    public function decodeUrlSafeToken($urlSafeToken)
    {
        if(empty($urlSafeToken)){
            return false;
        }
        try {
            //$token = base64_decode($urlSafeToken);
            $token = str_replace(['-', '_'], ['+', '/'], $urlSafeToken);
            $decodedToken = JWT::decode($token, new Key($this->secretKey, $this->algorithm));
            return (array) $decodedToken;
        } catch ( ExpiredException $e ) {
            log_message('alert','Error Expire token: ' . $e->getMessage());
            return false;
        } catch (Throwable $e) {
            log_message('alert','Error decoding token: ' . $e->getMessage());
            throw new RuntimeException("Error decoding token: " . $e->getMessage());
        }

    }

    public function decodeToken($token)
    {
        if(empty($token)){
            return false;
        }
        try {
            $decodedToken = JWT::decode($token, new Key($this->secretKey, $this->algorithm));
            return (array) $decodedToken;
        } catch ( ExpiredException $e ) {
            log_message('alert','Error Expire token: ' . $e->getMessage());
            return false;
        } catch (Throwable $e) {
            log_message('alert','Error decoding token: ' . $e->getMessage());
            throw new RuntimeException("Error decoding token: " . $e->getMessage());
        }

    }

    public function isValidCredentials($username, $password)
    {
        $storedUsername = getenv('AUTH_USER');
        $storedPassword = getenv('AUTH_PASS');

        return $username === $storedUsername && $password === $storedPassword;
    }

    public function isValidSellerCredentials($username, $password)
    {
        $storedUsername = getenv('AUTH_SELLER_USER');
        $storedPassword = getenv('AUTH_SELLER_PASS');

        return $username === $storedUsername && $password === $storedPassword;
    }

    public function encode(array $payload)
    {
        if (empty($payload)) {
            throw new InvalidArgumentException("Payload cannot be empty");
        }

        try {
            return JWT::encode($payload, $this->secretKey, $this->algorithm);
        } catch (Exception $e) {
            // Handle encoding exceptions here
            // Example: log the error or throw a custom exception
            throw new RuntimeException("Error encoding token: " . $e->getMessage());
        }
    }



    public function decode($token)
    {
        try {
            // Decode the JWT token
            $decodedToken = JWT::decode($token, new Key($this->secretKey, $this->algorithm));
            return (array) $decodedToken;
        } catch (UnexpectedValueException $e) {
            if ($e->getMessage() === 'kid empty, unable to lookup correct key') {
                // Handle "kid" empty issue
                throw new RuntimeException("Error: Key ID (kid) missing or incorrect");
            } else {
                // Handle other decoding errors
                throw new RuntimeException("Error decoding token: " . $e->getMessage());
            }
        } catch (Exception $e) {
            // Handle other exceptions
            throw new RuntimeException("Error decoding token: " . $e->getMessage());
        }
    }

    public function decodePayment($token, $secretKey)
    {
        try {
            // Decode the JWT token
            $decodedToken = JWT::decode($token, new Key($secretKey, 'HS256'));
            return (array) $decodedToken;
        } catch (UnexpectedValueException $e) {
            if ($e->getMessage() === 'kid empty, unable to lookup correct key') {
                // Handle "kid" empty issue
                throw new RuntimeException("Error: Key ID (kid) missing or incorrect");
            } else {
                // Handle other decoding errors
                throw new RuntimeException("Error decoding token: " . $e->getMessage());
            }
        } catch (Exception $e) {
            // Handle other exceptions
            throw new RuntimeException("Error decoding token: " . $e->getMessage());
        }
    }

    public function encodePayment(array $payload, $secretKey)
    {
        if (empty($payload)) {
            throw new ("Payload cannot be empty");
        }

        if (empty($secretKey)) {
            throw new InvalidArgumentException("Secret key cannot be empty");
        }

        try {
            return JWT::encode($payload, $secretKey, 'HS256');
        } catch (Exception $e) {
            // Handle encoding exceptions here
            // Example: log the error or throw a custom exception
            throw new RuntimeException("Error encoding token: " . $e->getMessage());
        }
    }
}
