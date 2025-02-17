<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class Atraction extends Seeder
{
    public function run()
    {

        $rows = array_map('str_getcsv', file(WRITEPATH . 'seeds/' . 'atraction.csv'));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            $data = [
                'id' => $row[0],
                'name' => $row[1],
                'address' => $row[2],
                'open' => $row[3],
                'close' => $row[4],
                'cp' => $row[5],
                'description' => $row[7],
                'video_url' => $row[8],
                'lat' => $row[9],
                'lng' => $row[10],
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ];

            $this->db->table('atraction')->insert($data);
            $this->db->table('atraction')->set('geom', "ST_GeomFromGeoJSON('{$row[6]}')", false)->where('id', $row[0])->update();
        }
    }
}
