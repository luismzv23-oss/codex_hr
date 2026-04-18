<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateObjectivesTable extends Migration {
    public function up() {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'title'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'description' => ['type' => 'TEXT', 'null' => true],
            'type'        => ['type' => 'VARCHAR', 'constraint' => 50],
            'weight'      => ['type' => 'DECIMAL', 'constraint' => '5,2'],
            'start_date'  => ['type' => 'DATE'],
            'end_date'    => ['type' => 'DATE'],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('objectives');
    }
    public function down() { $this->forge->dropTable('objectives'); }
}
