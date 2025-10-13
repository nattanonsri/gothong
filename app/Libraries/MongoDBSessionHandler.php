<?php

namespace App\Libraries;

use CodeIgniter\Session\Handlers\BaseHandler;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Query;
use MongoDB\Driver\BulkWrite;
use CodeIgniter\Log\Logger;

class MongoDBSessionHandler extends BaseHandler
{
    protected $collection;
    protected $manager;

    public function __construct()
    {

        try {

            $authRequired = $_ENV['MONGO_AUTH'];
            $username = $_ENV['MONGO_UNAME'];
            $password = $_ENV['MONGO_PASS'];
            $is_localhost = $_ENV['IS_LOCALHOST'];

            $dbName = $_ENV['session.mongodbname'];
            $collectionName = $_ENV['session.mongocollection'];

            $options = [];

            if ($is_localhost === 'N' && $authRequired === 'true') {
                $options = [
                    'username' => $username,
                    'password' => $password
                ];
            }

            if ($is_localhost === 'Y') {
                $dsn = $_ENV['session.mongoUri'];
            }else{
                $dsn = $_ENV['session.mongoUri']."/{$dbName}";
            }

            if($_ENV['MONGO_REPLICASET'] === 'Y'){
                $dsn = $_ENV['session.mongoUri'];
            }

            $this->manager = new Manager($dsn, $options);
            $this->collection = $dbName . '.' . $collectionName;

        } catch (\MongoDB\Driver\Exception\Exception $ex) {

            log_message('error', 'Couldn\'t connect to MongoDB: ' . $ex->getMessage());
            throw new \RuntimeException('Failed to connect to MongoDB');
        }
    }

    public function open($savePath, $sessionName): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read($sessionId): string
    {
        $query = new Query(['_id' => $sessionId]);
        $cursor = $this->manager->executeQuery($this->collection, $query);

        foreach ($cursor as $document) {
            return $document->data;
        }

        return '';
    }

    public function write($sessionId, $data): bool
    {
        $bulkWrite = new BulkWrite();
        $bulkWrite->update(
            ['_id' => $sessionId],
            ['$set' => ['data' => $data]],
            ['upsert' => true]
        );

        $this->manager->executeBulkWrite($this->collection, $bulkWrite);

        return true;
    }

    public function destroy($sessionId): bool
    {
        $bulkWrite = new BulkWrite();
        $bulkWrite->delete(['_id' => $sessionId]);

        $this->manager->executeBulkWrite($this->collection, $bulkWrite);

        return true;
    }

    public function gc($maxLifetime): int|false
    {
        $bulkWrite = new BulkWrite();
        $bulkWrite->delete(['last_activity' => ['$lt' => time() - $maxLifetime]]);

        $result = $this->manager->executeBulkWrite($this->collection, $bulkWrite);

        return $result->getDeletedCount();
    }

}
