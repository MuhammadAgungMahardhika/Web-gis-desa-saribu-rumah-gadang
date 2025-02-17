<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class CulinaryPlace extends Seeder
{
    public function run()
    {
        $rows = array_map('str_getcsv', file(WRITEPATH . 'seeds/' . 'culinary_place.csv'));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            $data = [
                'id_culinary_place' => $row[0],
                'name' => $row[1],
                'address' => $row[2],
                'cp' => $row[3],
                'open' => $row[4],
                'close' => $row[5],
                'description' => $row[7],
                'lat' => $row[8],
                'lng' => $row[9],
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ];

            $this->db->table('culinary_place')->insert($data);
            $this->db->table('culinary_place')->set('geom', "ST_GeomFromGeoJSON('{$row[6]}')", false)->where('id_culinary_place', $row[0])->update();
        }
    }
}
