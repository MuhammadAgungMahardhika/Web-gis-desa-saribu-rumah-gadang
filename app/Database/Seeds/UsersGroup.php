<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersGroup extends Seeder
{
    public function run()
    {
        $rows = array_map('str_getcsv', file(WRITEPATH.'seeds/'. 'auth_groups_users.csv'));
        $header = array_shift($rows);
    
        foreach ($rows as $row) {
            $data = [
                'group_id' => $row[0],
                'user_id' => $row[1],
            ];
        
            $this->db->table('auth_groups_users')->insert($data);
        }
    }
}
