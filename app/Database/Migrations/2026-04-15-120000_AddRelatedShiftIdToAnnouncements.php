<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRelatedShiftIdToAnnouncements extends Migration
{
    public function up()
    {
        $this->forge->addColumn('announcements', [
            'related_shift_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'recipient_role',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('announcements', 'related_shift_id');
    }
}
