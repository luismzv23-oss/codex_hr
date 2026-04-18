<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNotificationFieldsToAnnouncements extends Migration
{
    public function up()
    {
        $fields = [
            'recipient_user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'created_by',
            ],
            'recipient_role' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'recipient_user_id',
            ],
            'category' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'general',
                'after' => 'content',
            ],
        ];

        $this->forge->addColumn('announcements', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('announcements', ['recipient_user_id', 'recipient_role', 'category']);
    }
}
