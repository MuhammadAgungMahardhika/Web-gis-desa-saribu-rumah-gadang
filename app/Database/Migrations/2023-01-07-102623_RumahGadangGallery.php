<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RumahGadangGallery extends Migration
{
    public function up()
    {
        $fields = [
            'id_rumah_gadang_gallery' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'unique' => true,
            ],
            'id_rumah_gadang' => [
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
        $this->forge->addPrimaryKey('id_rumah_gadang_gallery');
        $this->forge->addForeignKey('id_rumah_gadang', 'rumah_gadang', 'id_rumah_gadang', 'CASCADE', 'CASCADE');
        $this->forge->createTable('rumah_gadang_gallery');
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->forge->dropTable('rumah_gadang_gallery');
    }
}
