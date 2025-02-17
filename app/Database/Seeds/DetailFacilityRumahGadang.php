<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class DetailFacilityRumahGadang extends Seeder
{
    public function run()
    {
        $rows = array_map('str_getcsv', file(WRITEPATH.'seeds/'. 'detail_facility_rumah_gadang.csv'));
        $header = array_shift($rows);
    
        foreach ($rows as $row) {
            $data = [
                'id_detail_facility_rumah_gadang' => $row[0],
                'id_rumah_gadang' => $row[1],
                'id_facility_rumah_gadang' => $row[2],
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ];
        
            $this->db->table('detail_facility_rumah_gadang')->insert($data);
        }
    }
}
