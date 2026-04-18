<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddManagementFieldsToShifts extends Migration
{
    public function up()
    {
        $fields = [
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'default' => 'pending',
                'after' => 'end_time',
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'after' => 'status',
            ],
            'approved_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'is_active',
            ],
        ];

        $this->forge->addColumn('shifts', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('shifts', ['status', 'is_active', 'approved_at']);
    }
}
