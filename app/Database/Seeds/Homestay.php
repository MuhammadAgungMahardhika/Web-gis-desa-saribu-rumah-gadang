<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class Homestay extends Seeder
{
    public function run()
    {
        $rows = array_map('str_getcsv', file(WRITEPATH . 'seeds/' . 'homestay.csv'));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            $data = [
                'id' => $row[0],
                'name' => $row[1],
                'address' => $row[2],
                'checkin' => $row[3],
                'checkout' => $row[4],
                'cp' => $row[5],
                'status' => $row[6],
                'price' => $row[7],
                'description' => $row[8],
                'url' => $row[9],
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ];

            $this->db->table('homestay')->insert($data);
        }
    }
}
