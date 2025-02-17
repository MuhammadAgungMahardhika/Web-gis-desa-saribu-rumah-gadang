<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DetailReservationHomestayUnit extends Migration
{
    public function up()
    {
        $fields = [
            'id' => [
                'type' => 'DATE',
                'null' => false
            ],
            'id_homestay_unit' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false
            ],
            'id_reservation' => [
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
        $this->forge->addForeignKey('id_homestay_unit', 'homestay_unit', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_reservation', 'reservation', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('detail_reservation_homestay_unit');
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->forge->dropTable('detail_reservation_homestay_unit');
    }
}
