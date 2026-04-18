<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ExpandUsersTableForHR extends Migration
{
    public function up()
    {
        $fields = [
            'department_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'document_id'],
            'employee_type' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true, 'after' => 'department_id'],
            'salary_base'   => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true, 'after' => 'employee_type'],
            'hire_date'     => ['type' => 'DATE', 'null' => true, 'after' => 'salary_base'],
        ];
        $this->forge->addColumn('users', $fields);
    }
    public function down() {
        $this->forge->dropColumn('users', ['department_id', 'employee_type', 'salary_base', 'hire_date']);
    }
}
