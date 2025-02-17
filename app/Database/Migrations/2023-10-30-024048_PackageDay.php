<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PackageDay extends Migration
{
    public function up()
    {
        $fields = [
            'day' => [
                'type' => 'VARCHAR',
                'constraint' => 5,
            ],
            'id_package' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false
            ],
            'description' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
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
        $this->forge->addPrimaryKey('day');
        $this->forge->addForeignKey('id_package', 'tourism_package', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('package_day');
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->forge->dropTable('package_day');
    }
}
