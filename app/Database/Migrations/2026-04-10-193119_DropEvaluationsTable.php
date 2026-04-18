<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class DropEvaluationsTable extends Migration {
    public function up() { $this->forge->dropTable('evaluations', true); }
    public function down() {}
}
