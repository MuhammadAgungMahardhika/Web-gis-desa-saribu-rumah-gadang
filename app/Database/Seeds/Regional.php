<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class Regional extends Seeder
{
    public function run()
    {
        $rows = array_map('str_getcsv', file(WRITEPATH.'seeds/'. 'regional.csv'));
        $header = array_shift($rows);
    
        foreach ($rows as $row) {
            $data = [
                'id_regional' => $row[0],
                'name' => $row[1],
                'district' => $row[2],
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ];
        
            $this->db->table('regional')->insert($data);
            $this->db->table('regional')->set('geom', $row[3], false)->where('id_regional', $row[0])->update();
        }
    }
}
