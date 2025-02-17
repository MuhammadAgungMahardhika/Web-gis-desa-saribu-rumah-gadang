<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Reservation extends Migration
{
    public function up()
    {
        $fields = [
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'id_user' => [
                'type' => 'int',
                'constraint' => 11,
                'unsigned' => true
            ],
            'id_package' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ],
            'id_homestay' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ],
            'id_reservation_status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'request_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'number_people' => [
                'type' => 'INT',
                'default' => 0,
                'null' => true,
            ],
            'check_in' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'total_price' => [
                'type' => 'INT',
                'default' => 0,
                'null' => true,
            ],
            'deposit' => [
                'type' => 'INT',
                'default' => 0,
                'null' => true,
            ],
            'proof_of_deposit' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'deposit_date' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'proof_of_payment' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'payment_date' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'comment' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'review' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'rating' => [
                'type' => 'INT',
                'null' => true,
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
        $this->forge->addForeignKey('id_user', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_package', 'tourism_package', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('id_homestay', 'tourism_package', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('id_reservation_status', 'reservation_status', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('reservation');
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->forge->dropTable('reservation');
    }
}
