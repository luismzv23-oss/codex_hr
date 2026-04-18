<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddQrTrackingToAttendance extends Migration
{
    public function up()
    {
        $this->forge->addColumn('attendance', [
            'checkin_method' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'manual',
                'after' => 'check_out',
            ],
            'qr_point_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'checkin_method',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('attendance', ['checkin_method', 'qr_point_id']);
    }
}
