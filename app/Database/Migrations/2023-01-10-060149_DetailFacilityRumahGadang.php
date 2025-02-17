<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DetailFacilityRumahGadang extends Migration
{
    public function up()
    {
        $fields = [
            'id_detail_facility_rumah_gadang' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'unique' => true,
            ],
            'id_rumah_gadang' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'id_facility_rumah_gadang' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
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
        $this->forge->addPrimaryKey('id_detail_facility_rumah_gadang');
        $this->forge->addForeignKey('id_rumah_gadang', 'rumah_gadang', 'id_rumah_gadang', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_facility_rumah_gadang', 'facility_rumah_gadang', 'id_facility_rumah_gadang', 'CASCADE', 'CASCADE');
        $this->forge->createTable('detail_facility_rumah_gadang');
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->forge->dropTable('detail_facility_rumah_gadang');
    }
}
