<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class Comment extends Seeder
{
    public function run()
    {
        $rows = array_map('str_getcsv', file(WRITEPATH.'seeds/'. 'comment.csv'));
        $header = array_shift($rows);
    
        foreach ($rows as $row) {
            $data = [
                'id_comment' => $row[0],
                'status' => $row[1],
                'comment' => $row[5],
                'date' => $row[6],
                'rating' => $row[7],
                'id_user' => $row[8],
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ];
        
            if(!empty($row[2])){
                $data += array('id_rumah_gadang' => $row[2]);
            }
            if(!empty($row[3])){
                $data += array('id_event' => $row[3]);
            }
            if(!empty($row[4])){
                $data += array('id_unique_place' => $row[4]);
            }
        
            $this->db->table('comment')->insert($data);
        }
    }
}
