<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class Event extends Seeder
{
    public function run()
    {
        $rows = array_map('str_getcsv', file(WRITEPATH . 'seeds/' . 'event.csv'));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            $data = [
                'id_event' => $row[0],
                'id_event_category' => $row[1],
                'id_user' => $row[2],
                'name' => $row[3],
                'event_start' => $row[4],
                'event_end' => $row[5],
                'ticket_price' => $row[6],
                'description' => $row[7],
                'cp' => $row[8],
                'lat' => $row[10],
                'lng' => $row[11],
                'video_url' => $row[12],
                'committee' => $row[13],
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ];

            $this->db->table('event')->insert($data);
            $this->db->table('event')->set('geom', "ST_GeomFromGeoJSON('{$row[9]}')", false)->where('id_event', $row[0])->update();
        }
    }
}
