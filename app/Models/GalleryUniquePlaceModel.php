<?php

namespace App\Models;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class GalleryUniquePlaceModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'unique_place_gallery';
    protected $primaryKey       = 'id_unique_place_gallery';
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_unique_place_gallery', 'id_unique_place', 'url'];

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
        $lastId = $this->db->table($this->table)->select('id_unique_place_gallery')->orderBy('id_unique_place_gallery', 'ASC')->get()->getLastRow('array');
        if($lastId !=null){
            $count = (int)substr($lastId['id_unique_place_gallery'], 0);
            $id = sprintf('%03d', $count + 1);
        }else{
            $count = 0;
            $id = sprintf('%03d', $count + 1);
        }
      
        return $id;
    }

    public function get_gallery_api($unique_place_id = null) {
        $query = $this->db->table($this->table)
            ->select('url')
            ->orderBy('id_unique_place_gallery', 'ASC')
            ->where('id_unique_place', $unique_place_id)
            ->get();
        return $query;
    }

    public function add_gallery_api($id = null, $data = null) {
        $query = false;
        foreach ($data as $gallery) {
            $new_id = $this->get_new_id_api();
            $content = [
                'id_unique_place_gallery' => $new_id,
                'id_unique_place' => $id,
                'url' => $gallery,
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ];
            $query = $this->db->table($this->table)->insert($content);
        }
        return $query;
    }

    public function update_gallery_api($id = null, $data = null) {
        $queryDel = $this->delete_gallery_api($id);

        foreach ($data as $key => $value) {
            if(empty($value)) {
                unset($data[$key]);
            }
        }
        $queryIns = $this->add_gallery_api($id, $data);
        return $queryDel && $queryIns;
    }

    public function delete_gallery_api($id = null) {
        return $this->db->table($this->table)->delete(['id_unique_place' => $id]);
    }
}
