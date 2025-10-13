<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRecallFieldsToQueue extends Migration
{
    public function up()
    {
        $this->forge->addColumn('queue', [
            'recall_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'จำนวนครั้งที่เรียกคิวซ้ำ'
            ],
            'last_recall_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'เวลาที่เรียกคิวซ้ำล่าสุด'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('queue', ['recall_count', 'last_recall_at']);
    }
}
