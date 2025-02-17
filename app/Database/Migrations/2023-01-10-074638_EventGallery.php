<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EventGallery extends Migration
{
    public function up()
    {
        $fields = [
            'id_event_gallery' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'unique' => true,
            ],
            'id_event' => [
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
        $this->forge->addPrimaryKey('id_event_gallery');
        $this->forge->addForeignKey('id_event', 'event', 'id_event', 'CASCADE', 'CASCADE');
        $this->forge->createTable('event_gallery');
        $this->db->enableForeignKeyChecks();
    }
    
    public function down()
    {
        $this->forge->dropTable('event_gallery');
    }
}
