<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SouvenirGallery extends Migration
{
    public function up()
    {
        $fields = [
            'id_souvenir_gallery' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'unique' => true,
            ],
            'id_souvenir' => [
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
        $this->forge->addPrimaryKey('id_souvenir_gallery');
        $this->forge->addForeignKey('id_souvenir', 'souvenir', 'id_souvenir', 'CASCADE', 'CASCADE');
        $this->forge->createTable('souvenir_gallery');
        $this->db->enableForeignKeyChecks();
    }
    
    public function down()
    {
        $this->forge->dropTable('souvenir_gallery');
    }
}
