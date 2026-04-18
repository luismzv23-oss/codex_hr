<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddScheduleIdToShifts extends Migration
{
    public function up()
    {
        $fields = [
            'schedule_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'user_id',
            ],
        ];

        $this->forge->addColumn('shifts', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('shifts', 'schedule_id');
    }
}
