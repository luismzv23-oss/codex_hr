<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddParentIdToDepartments extends Migration
{
    public function up()
    {
        $fields = [
            'parent_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'id'
            ]
        ];
        $this->forge->addColumn('departments', $fields);
    }

    public function down() { $this->forge->dropColumn('departments', 'parent_id'); }
}
