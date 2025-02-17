<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class RecommendationPlace extends Seeder
{
    public function run()
    {
        $rows = array_map('str_getcsv', file(WRITEPATH.'seeds/'. 'recommendation_place.csv'));
        $header = array_shift($rows);
    
        foreach ($rows as $row) {
            $data = [
                'id_recommendation' => $row[0],
                'name' => $row[1],
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ];
        
            $this->db->table('recommendation_place')->insert($data);
        }
    }
}
