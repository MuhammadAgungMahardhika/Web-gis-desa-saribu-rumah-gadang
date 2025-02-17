<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Comment extends Migration
{
    public function up()
    {
        $fields = [
            'id_comment' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'unique' => true,
            ],
            'id_rumah_gadang' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'id_event' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'id_unique_place' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'id_user' => [
                'type' => 'int',
                'constraint' => 11,
                'unsigned' => true
            ],
            'comment' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'rating' => [
                'type' => 'INT',
                'null' => true,
            ],
            'date' => [
                'type' => 'DATETIME',
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 5,
                'default' => 0,
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
        $this->forge->addPrimaryKey('id_comment');
        $this->forge->addForeignKey('id_rumah_gadang', 'rumah_gadang', 'id_rumah_gadang', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_event', 'event', 'id_event', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_unique_place', 'unique_place', 'id_unique_place', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_user', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('comment');
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->forge->dropTable('comment');
    }
}
