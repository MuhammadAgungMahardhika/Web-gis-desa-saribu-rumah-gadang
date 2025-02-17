<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class DetailPackage extends Seeder
{
    public function run()
    {
        $rows = array_map('str_getcsv', file(WRITEPATH . 'seeds/' . 'detail_package.csv'));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            $data = [
                'activity' => $row[0],
                'id_day' => $row[1],
                'id_package' => $row[2],
                'id_object' => $row[3],
                'activity_type' => $row[4],
                'description' => $row[5],
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ];

            $this->db->table('detail_package')->insert($data);
        }
    }
}
