<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UniquePlaceGallery extends Migration
{
    public function up()
    {
        $fields = [
            'id_unique_place_gallery' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'unique' => true,
            ],
            'id_unique_place' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
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
        $this->forge->addPrimaryKey('id_unique_place_gallery');
        $this->forge->addForeignKey('id_unique_place', 'unique_place', 'id_unique_place', 'CASCADE', 'CASCADE');
        $this->forge->createTable('unique_place_gallery');
        $this->db->enableForeignKeyChecks();
    }
    
    public function down()
    {
        $this->forge->dropTable('unique_place_gallery');
    }
}
