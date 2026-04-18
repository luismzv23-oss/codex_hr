<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateEmployeeEvaluationsTable extends Migration {
    public function up() {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'evaluation_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'user_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'total_score'   => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'comments'      => ['type' => 'TEXT', 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('employee_evaluations');
    }
    public function down() { $this->forge->dropTable('employee_evaluations'); }
}
