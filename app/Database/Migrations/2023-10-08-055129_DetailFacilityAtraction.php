<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DetailFacilityAtraction extends Migration
{
    public function up()
    {
        $fields = [
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'unique' => true,
            ],
            'id_atraction' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'id_atraction_facility' => [
                'type' => 'VARCHAR',
                'constraint' => 50,

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
        $this->forge->addForeignKey('id_atraction', 'atraction', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_atraction_facility', 'atraction_facility', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('detail_facility_atraction');
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->forge->dropTable('detail_facility_atraction');
    }
}
