<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DetailFacilityHomeStay extends Migration
{
    public function up()
    {
        $fields = [
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'id_homestay' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false
            ],
            'id_homestay_facility' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false
            ],
            'description' => [
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
        $this->forge->addForeignKey('id_homestay', 'homestay', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_homestay_facility', 'homestay_facility', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('detail_facility_homestay');
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->forge->dropTable('detail_facility_homestay');
    }
}
