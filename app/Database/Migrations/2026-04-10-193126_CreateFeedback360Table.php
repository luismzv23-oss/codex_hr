<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateFeedback360Table extends Migration {
    public function up() {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'evaluation_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'evaluatee_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'evaluator_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'rater_type'    => ['type' => 'VARCHAR', 'constraint' => 50],
            'score'         => ['type' => 'DECIMAL', 'constraint' => '5,2'],
            'comment'       => ['type' => 'TEXT', 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('feedback_360');
    }
    public function down() { $this->forge->dropTable('feedback_360'); }
}
