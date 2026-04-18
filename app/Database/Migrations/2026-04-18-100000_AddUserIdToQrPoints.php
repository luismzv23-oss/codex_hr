<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserIdToQrPoints extends Migration
{
    public function up()
    {
        $this->forge->addColumn('qr_points', [
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'id',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('qr_points', 'user_id');
    }
}
