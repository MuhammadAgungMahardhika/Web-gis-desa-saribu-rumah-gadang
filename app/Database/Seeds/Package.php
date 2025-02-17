<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class Package extends Seeder
{
    public function run()
    {
        $rows = array_map('str_getcsv', file(WRITEPATH . 'seeds/' . 'package.csv'));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            $data = [
                'id' => $row[0],
                'id_homestay' => $row[1],
                'name' => $row[2],
                'price' => $row[3],
                'capacity' => $row[4],
                'cp' => $row[5],
                'description' => $row[6],
                'url' => $row[7],
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ];

            $this->db->table('tourism_package')->insert($data);
        }
    }
}
