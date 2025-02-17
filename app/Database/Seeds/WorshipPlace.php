<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class WorshipPlace extends Seeder
{
    public function run()
    {
        $rows = array_map('str_getcsv', file(WRITEPATH . 'seeds/' . 'worship_place.csv'));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            $data = [
                'id_worship_place' => $row[0],
                'name' => $row[1],
                'address' => $row[2],
                'parking_area' => $row[4],
                'building_area' => $row[5],
                'capacity' => $row[6],
                'description' => $row[7],
                'lat' => $row[8],
                'lng' => $row[9],
                'cp' => $row[10],
                'open' => $row[11],
                'close' => $row[12],
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ];

            $this->db->table('worship_place')->insert($data);
            $this->db->table('worship_place')->set('geom', "ST_GeomFromGeoJSON('{$row[3]}')", false)->where('id_worship_place', $row[0])->update();
        }
    }
}
