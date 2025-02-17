<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class Souvenir extends Seeder
{
    public function run()
    {
        $rows = array_map('str_getcsv', file(WRITEPATH . 'seeds/' . 'souvenir.csv'));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            $data = [
                'id_souvenir' => $row[0],
                'name' => $row[1],
                'cp' => $row[2],
                'open' => $row[3],
                'close' => $row[4],
                'description' => $row[6],
                'address' => $row[7],
                'lat' => $row[8],
                'lng' => $row[9],
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ];

            $this->db->table('souvenir')->insert($data);
            $this->db->table('souvenir')->set('geom', "ST_GeomFromGeoJSON('{$row[5]}')", false)->where('id_souvenir', $row[0])->update();
        }
    }
}
