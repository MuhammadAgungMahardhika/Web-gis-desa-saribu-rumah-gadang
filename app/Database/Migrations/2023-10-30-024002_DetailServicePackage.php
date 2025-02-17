<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DetailServicePackage extends Migration
{
    public function up()
    {
        $fields = [
            'id_detail_service_package' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'unique' => true,
            ],
            'id_service_package' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'id_package' => [
                'type' => 'VARCHAR',
                'constraint' => 50,

            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
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
        $this->forge->addPrimaryKey('id_detail_service_package');
        $this->forge->addForeignKey('id_service_package', 'service', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_package', 'tourism_package', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('detail_service_package');
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->forge->dropTable('detail_service_package');
    }
}
