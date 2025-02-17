<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TourismPackageGallery extends Migration
{
    public function up()
    {
        $fields = [
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'unique' => true,
            ],
            'id_package' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false
            ],
            'url' => [
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
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('id_package', 'tourism_package', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tourism_package_gallery');
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->forge->dropTable('tourism_package_gallery');
    }
}
