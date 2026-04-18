<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreatePerformanceEvaluationsTable extends Migration {
    public function up() {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 150],
            'start_date' => ['type' => 'DATE'],
            'end_date'   => ['type' => 'DATE'],
            'status'     => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Abierta'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('performance_evaluations');
    }
    public function down() { $this->forge->dropTable('performance_evaluations'); }
}
