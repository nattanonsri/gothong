<?php
namespace App\Libraries;

use MongoDB\Driver\Manager;
use MongoDB\Driver\Exception\ConnectionException;

class MongoDB {
    private $conn;

    public function __construct() {
        $host = $_ENV['MONGO_HOST'];
        $port = $_ENV['MONGO_PORT'];
        $username = $_ENV['MONGO_UNAME'];
        $password = $_ENV['MONGO_PASS'];
        $authRequired = $_ENV['MONGO_AUTH'];
        $exUri = $_ENV['MONGO_EX_URL'];
        $database = $_ENV['MONGO_DB'];
        $is_localhost = $_ENV['IS_LOCALHOST'];

        try {
            if ($is_localhost === 'Y') {
                $dsn = 'mongodb://' . $host . ':' . $port . '/?' . $exUri;
            } else {
                $dsn = "mongodb://{$host}:{$port}/{$database}";
            }

            $options = [];

            if ($is_localhost === 'N' && $authRequired === 'true') {
                $options = [
                    'username' => $username,
                    'password' => $password
                ];
            }

            if($_ENV['MONGO_REPLICASET'] === 'Y'){
                $dsn = "mongodb://{$host}/?$exUri";
            }
            $this->conn = new Manager($dsn, $options);

        } catch (ConnectionException $ex) {
            log_message('alert', 'Couldn\'t connect to MongoDB: ' . $ex->getMessage());
        }
    }   

    public function getConn() {
        return $this->conn;
    }
}

