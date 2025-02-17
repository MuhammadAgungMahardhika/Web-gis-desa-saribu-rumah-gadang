<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RecommendationPlace extends Migration
{
    public function up()
    {
        $fields = [
            'id_recommendation' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'unique' => true,
            ],
            'name' => [
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
        $this->forge->addPrimaryKey('id_recommendation');
        $this->forge->createTable('recommendation_place');
        $this->db->enableForeignKeyChecks();
    }
    
    public function down()
    {
        $this->forge->dropTable('recommendation_place');
    }
}
