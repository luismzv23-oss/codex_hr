<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddShiftTrackingToAttendance extends Migration
{
    public function up()
    {
        $fields = [
            'shift_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'user_id',
            ],
            'late_minutes' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'check_out',
            ],
            'late_status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'late_minutes',
            ],
        ];

        $this->forge->addColumn('attendance', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('attendance', ['shift_id', 'late_minutes', 'late_status']);
    }
}
