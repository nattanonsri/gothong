<?php

namespace App\Models;

use App\Libraries\MongoDB;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Query;
use MongoDB\Driver\Exception\Exception;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use CodeIgniter\Config\Services;

class LogMongoModel
{
    private $mongodb;
    private $database;
    private $baseCollection = 'tb_log';

    public function __construct()
    {
        $this->mongodb = new MongoDB();
        $this->database = $_ENV['MONGO_DB'];
        $this->baseCollection = $this->baseCollection . '_' . date('Y');
    }

    public function insertLog(array $logData): array
    {
        try {

            $bulk = new BulkWrite;
            $document = [
                '_id' => new ObjectId(),
                'user_id' => $logData['user_id'] ?? 0,
                'method' => $logData['method'] ?? null,
                'endpoint' => $logData['endpoint'] ?? null,
                'response' => $logData['response'] ?? null,
                'error' => $logData['error'] ?? null,
                'ip' => $logData['ip'],
                'browser' => $logData['browser'],
                'os' => $logData['os'],
                'user_agent' => $logData['user_agent'],
                'request_data' => $logData['request_data'],
                'status_code' => $logData['status_code'],
                'execution_time' => $logData['execution_time'],
                'memory_usage' => $logData['memory_usage'],
                'created_by' => $logData['created_by'],
                'updated_by' => $logData['updated_by'],
                'created_at' => new UTCDateTime(),
                'updated_at' => new UTCDateTime(),
            ];

            $bulk->insert($document);
            $result = $this->mongodb->getConn()->executeBulkWrite($this->database . '.' . $this->baseCollection, $bulk);


            if ($result->getInsertedCount() > 0) {
                return ['status' => 200, 'message' => 'Document inserted successfully', 'inserted_count' => $result->getInsertedCount()];
            } else {
                return ['status' => 400, 'message' => 'Failed to insert document'];
            }
        } catch (\Exception $e) {
            log_message('error', 'MongoDB Insert Error :' . $e->getMessage());
            return ['status' => 500, 'message' => $e->getMessage()];
        }
    }
}
