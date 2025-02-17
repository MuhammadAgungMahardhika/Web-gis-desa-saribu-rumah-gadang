<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CulinaryPlaceGallery extends Migration
{
    public function up()
    {
        $fields = [
            'id_culinary_place_gallery' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'unique' => true,
            ],
            'id_culinary_place' => [
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
        $this->forge->addPrimaryKey('id_culinary_place_gallery');
        $this->forge->addForeignKey('id_culinary_place', 'culinary_place', 'id_culinary_place', 'CASCADE', 'CASCADE');
        $this->forge->createTable('culinary_place_gallery');
        $this->db->enableForeignKeyChecks();
    }
    
    public function down()
    {
        $this->forge->dropTable('culinary_place_gallery');
    }
}
