<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class PackageDay extends Seeder
{
    public function run()
    {
        $rows = array_map('str_getcsv', file(WRITEPATH . 'seeds/' . 'package_day.csv'));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            $data = [
                'day' => $row[0],
                'id_package' => $row[1],
                'description' => $row[2],
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ];

            $this->db->table('package_day')->insert($data);
        }
    }
}
