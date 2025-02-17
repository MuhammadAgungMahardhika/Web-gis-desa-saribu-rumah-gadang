<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class RumahGadang extends Seeder
{
    public function run()
    {

        $rows = array_map('str_getcsv', file(WRITEPATH . 'seeds/' . 'rumah_gadang.csv'));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            $data = [
                'id_rumah_gadang' => $row[0],
                'id_user' => $row[1],
                'id_recommendation' => $row[2],
                // 'id_homestay' => $row[3],
                'name' => $row[4],
                'address' => $row[5],
                'open' => $row[6],
                'close' => $row[7],
                'cp' => $row[8],
                'price_ticket' => $row[10],
                'description' => $row[11],
                'video_url' => $row[12],
                'lat' => $row[13],
                'lng' => $row[14],
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ];

            $this->db->table('rumah_gadang')->insert($data);
            $this->db->table('rumah_gadang')->set('geom', "ST_GeomFromGeoJSON('{$row[9]}')", false)->where('id_rumah_gadang', $row[0])->update();
        }
    }
}
