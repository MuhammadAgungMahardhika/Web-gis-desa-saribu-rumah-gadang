<?php

namespace App\Models;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class GalleryWorshipPlaceModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'worship_place_gallery';
    protected $primaryKey       = 'id_worship_place_gallery';
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_worship_place_gallery', 'id_worship_place', 'url'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // API
    public function get_new_id_api() {
        $lastId = $this->db->table($this->table)->select('id_worship_place_gallery')->orderBy('id_worship_place_gallery', 'ASC')->get()->getLastRow('array');
        if($lastId !=null){
            $count = (int)substr($lastId['id_worship_place_gallery'], 0);
            $id = sprintf('%03d', $count + 1);
        }else{
            $count = 0;
            $id = sprintf('%03d', $count + 1);
        }
        
        return $id;
    }

    public function get_gallery_api($worship_place_id = null) {
        $query = $this->db->table($this->table)
            ->select('url')
            ->where('id_worship_place', $worship_place_id)
            ->get();
        return $query;
    }

    public function add_gallery_api($id = null, $data = null) {
        $query = false;
        foreach ($data as $gallery) {
            $new_id = $this->get_new_id_api();
            $content = [
                'id_worship_place_gallery' => $new_id,
                'id_worship_place' => $id,
                'url' => $gallery,
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ];
            $query = $this->db->table($this->table)->insert($content);
        }
        return $query;
    }

    public function update_gallery_api($id = null, $data = null) {
        $queryDel = $this->db->table($this->table)->delete(['id_worship_place' => $id]);
        $queryIns = $this->add_gallery_api($id, $data);
        return $queryDel && $queryIns;
    }
}
