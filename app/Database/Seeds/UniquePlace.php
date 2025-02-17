<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class UniquePlace extends Seeder
{
    public function run()
    {
        $rows = array_map('str_getcsv', file(WRITEPATH.'seeds/'. 'unique_place.csv'));
        $header = array_shift($rows);
    
        foreach ($rows as $row) {
            $data = [
                'id_unique_place' => $row[0],
                'id_user' => $row[1],
                'name' => $row[2],
                'address' => $row[3],
                'status' => $row[5],
                'cp' => $row[6],
                'description' => $row[7],
                'video_url' => $row[8],
                'lat' => $row[9],
                'lng' => $row[10],
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ];
        
            $this->db->table('unique_place')->insert($data);
            $this->db->table('unique_place')->set('geom', $row[4], false)->where('id_unique_place', $row[0])->update();
        }
    }
}
