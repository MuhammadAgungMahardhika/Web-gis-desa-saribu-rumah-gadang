<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class HomestayUnitGallery extends Migration
{
    public function up()
    {
        $fields = [
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'id_homestay_unit' => [
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
        $this->forge->addForeignKey('id_homestay_unit', 'homestay_unit', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('homestay_unit_gallery');
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->forge->dropTable('homestay_unit_gallery');
    }
}
