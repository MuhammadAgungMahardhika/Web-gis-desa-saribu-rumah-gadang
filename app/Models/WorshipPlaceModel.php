<?php

namespace App\Models;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class WorshipPlaceModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'worship_place';
    protected $primaryKey       = 'id_worship_place';
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_worship_place', 'name', 'address', 'parking_area', 'building_area', 'capacity', 'geom', 'description', 'lat', 'lng', 'cp', 'open', 'close'];

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
    public function get_list_wp_api() {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id_worship_place as id,{$this->table}.name,{$this->table}.address,{$this->table}.parking_area,{$this->table}.building_area,{$this->table}.capacity,{$this->table}.description";
        $vilGeom = "regional.id_regional = '1' AND ST_Contains(regional.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, worship_place.lat, worship_place.lng")
            ->from('regional')
            ->where($vilGeom)
            ->get();
        return $query;
    }

    public function list_by_owner_api($id) {
        $query = $this->db->table($this->table)
            ->select('worship_place.*, CONCAT(account.first_name, " ", account.last_name) as owner_name')
            ->where('owner', $id)
            ->join('account', 'worship_place.owner = account.id')
            ->get();
        return $query;
    }

    public function get_wp_by_id_api($id = null) {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id_worship_place as id,{$this->table}.name,{$this->table}.address,{$this->table}.parking_area,{$this->table}.building_area,{$this->table}.capacity,{$this->table}.description";
        $vilGeom = "regional.id_regional = '1' AND ST_Contains(regional.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, worship_place.lat, worship_place.lng")
            ->from('regional')
            ->where('worship_place.id_worship_place', $id)
            ->where($vilGeom)
            ->get();
        return $query;
    }
    
    public function get_wp_by_radius_api($data = null) {
        $radius = (int)$data['radius'] / 1000;
        $lat = $data['lat'];
        $long = $data['long'];
        $jarak = "(6371 * acos(cos(radians({$lat})) * cos(radians({$this->table}.lat)) * cos(radians({$this->table}.lng) - radians({$long})) + sin(radians({$lat}))* sin(radians({$this->table}.lat))))";
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id_worship_place as id,{$this->table}.name,{$this->table}.address,{$this->table}.parking_area,{$this->table}.building_area,{$this->table}.capacity,{$this->table}.description";
        $vilGeom = "regional.id_regional = '1' AND ST_Contains(regional.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, worship_place.lat, worship_place.lng, {$jarak} as jarak")
            ->from('regional')
            ->where($vilGeom)
            ->having(['jarak <=' => $radius])
            ->get();
        return $query;
    }

    public function get_new_id_api() {
        $lastId = $this->db->table($this->table)->select('id_worship_place')->orderBy('id_worship_place', 'ASC')->get()->getLastRow('array');
        if($lastId !=null){
            $count = (int)substr($lastId['id_worship_place'], 1);
            $id = sprintf('W%01d', $count + 1);
        }else{
            $count = 0;
            $id = sprintf('W%01d', $count + 1);
        }
        return $id;
    }

    public function add_wp_api($worship_place = null) {
        foreach ($worship_place as $key => $value) {
            if(empty($value)) {
                unset($worship_place[$key]);
            }
        }
        $worship_place['created_at'] = Time::now();
        $worship_place['updated_at'] = Time::now();
        $query = $this->db->table($this->table)
            ->insert($worship_place);
        return $query;
    }

    public function update_wp_api($id = null, $worship_place = null) {
        foreach ($worship_place as $key => $value) {
            if(empty($value)) {
                unset($worship_place[$key]);
            }
        }
        $worship_place['updated_at'] = Time::now();
        $query = $this->db->table($this->table)
            ->where('id_worship_place', $id)
            ->update($worship_place);
        return $query;
    }
}
