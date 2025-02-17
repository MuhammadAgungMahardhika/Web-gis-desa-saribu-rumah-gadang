<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DetailPackage extends Migration
{
    public function up()
    {
        $fields = [
            'activity' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'id_day' => [
                'type' => 'VARCHAR',
                'constraint' => 5,
                'null' => false
            ],
            'id_package' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false
            ],
            'id_object' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'activity_type' => [
                'type' => 'VARCHAR',
                'constraint' => 1,
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
        $this->forge->addPrimaryKey('activity');
        $this->forge->addForeignKey('id_day', 'package_day', 'day', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_package', 'tourism_package', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('detail_package');
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->forge->dropTable('detail_package');
    }
}
