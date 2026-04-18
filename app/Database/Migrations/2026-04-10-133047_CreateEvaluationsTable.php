<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEvaluationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'evaluatee_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'evaluator_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'score'        => ['type' => 'INT', 'constraint' => 11],
            'feedback'     => ['type' => 'TEXT'],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('evaluations');
    }

    public function down()
    {
        $this->forge->dropTable('evaluations');
    }
}
