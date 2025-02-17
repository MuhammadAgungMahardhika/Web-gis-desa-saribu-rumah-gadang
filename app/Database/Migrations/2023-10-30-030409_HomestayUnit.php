<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class HomestayUnit extends Migration
{
    public function up()
    {
        $fields = [
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'id_homestay' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'id_unit_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'price' => [
                'type' => 'INT',
                'default' => 0
            ],
            'capacity' => [
                'type' => 'INT',
                'default' => 0
            ],
            'description' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ];

        $this->db->disableForeignKeyChecks();
        $this->forge->addField($fields);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('id_homestay', 'homestay', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_unit_type', 'homestay_unit_type', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('homestay_unit');
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->forge->dropTable('homestay_unit');
    }
}
