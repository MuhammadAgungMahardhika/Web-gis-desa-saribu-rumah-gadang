<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class WorshipPlaceGallery extends Migration
{
    public function up()
    {
        $fields = [
            'id_worship_place_gallery' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'unique' => true,
            ],
            'id_worship_place' => [
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
        $this->forge->addPrimaryKey('id_worship_place_gallery');
        $this->forge->addForeignKey('id_worship_place', 'worship_place', 'id_worship_place', 'CASCADE', 'CASCADE');
        $this->forge->createTable('worship_place_gallery');
        $this->db->enableForeignKeyChecks();
    }
    
    public function down()
    {
        $this->forge->dropTable('worship_place_gallery');
    }
}
