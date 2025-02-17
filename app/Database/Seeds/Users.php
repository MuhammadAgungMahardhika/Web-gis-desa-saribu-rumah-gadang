<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;
use Myth\Auth\Password;

class Users extends Seeder
{
    public function run()
    {
        $rows = array_map('str_getcsv', file(WRITEPATH.'seeds/'. 'users.csv'));
        $header = array_shift($rows);
    
        foreach ($rows as $row) {
            $data = [
                'username' => $row[0],
                'first_name' => $row[1],
                'last_name' => $row[2],
                'email' => $row[3],
                'address' => $row[4],
                'password_hash' => Password::hash($row[5]),
                'reset_hash' => null,
                'reset_at' => null,
                'reset_expires' => null,
                'active' => 1,
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ];
        
            $this->db->table('users')->insert($data);
        }
    }
}
