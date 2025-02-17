<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DetailFacilityHomestayUnit extends Migration
{
    public function up()
    {
        $fields = [
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'id_homestay_unit' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false
            ],
            'id_homestay_unit_facility' => [
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
        $this->forge->addForeignKey('id_homestay_unit', 'homestay_unit', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_homestay_unit_facility', 'homestay_unit_facility', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('detail_homestay_unit_facility');
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->forge->dropTable('detail_homestay_unit_facility');
    }
}
