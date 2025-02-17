<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TourismPackage extends Migration
{
    public function up()
    {
        $fields = [
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 3,
            ],
            'id_package_type' => [
                'type' => 'VARCHAR',
                'constraint' => 3,
                'null' => true
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false
            ],
            'price' => [
                'type' => 'INT',
                'default' => 0,
                'null' => true,
            ],
            'capacity' => [
                'type' => 'INT',
                'default' => 0,
                'null' => true,
            ],
            'cp' => [
                'type' => 'VARCHAR',
                'constraint' => 13,
                'null' => true,
            ],
            'description' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'costum' => [
                'type' => 'BOOLEAN',
                'null' => false,
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
        $this->forge->addForeignKey('id_package_type', 'package_type', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tourism_package');
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->forge->dropTable('tourism_package');
    }
}
