<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDocumentIdToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'document_id' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
                'unique' => true,
                'after' => 'name'
            ],
        ];
        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'document_id');
    }
}
