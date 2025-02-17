<?php

namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\I18n\Time;
use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;

class UniquePlaceModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'unique_place';
    protected $primaryKey       = 'id_unique_place';
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_unique_place', 'id_user', 'name', 'address', 'geom', 'status', 'cp', 'description', 'video_url', 'lat', 'lng'];

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
    public function get_list_up_api()
    {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id_unique_place as id,{$this->table}.id_user,{$this->table}.name,{$this->table}.address,{$this->table}.status,{$this->table}.cp as contact_person,{$this->table}.description,{$this->table}.video_url";
        $vilGeom = "regional.id_regional = '1' AND ST_Contains(regional.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, unique_place.lat, unique_place.lng")
            ->from('regional')
            ->where($vilGeom)
            ->get();
        return $query;
    }

    public function list_by_owner_api($id = null)
    {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id_unique_place as id,{$this->table}.id_user,{$this->table}.name,{$this->table}.address,{$this->table}.status,{$this->table}.cp as contact_person,{$this->table}.description,{$this->table}.video_url";
        $vilGeom = "regional.id_regional = '1' AND ST_Contains(regional.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, unique_place.lat, unique_place.lng")
            ->from('regional')
            ->where($vilGeom)
            ->where('id_user', $id)
            ->get();
        return $query;
    }

    public function get_up_by_id_api($id = null)
    {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id_unique_place as id,{$this->table}.id_user,{$this->table}.name,{$this->table}.address,{$this->table}.status,{$this->table}.cp as contact_person,{$this->table}.description,{$this->table}.video_url";
        $geoJson = "ST_AsGeoJSON({$this->table}.geom) AS geoJson";
        $vilGeom = "regional.id_regional = '1' AND ST_Contains(regional.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, unique_place.lat, unique_place.lng, {$geoJson}")
            ->from('regional')
            ->where('unique_place.id_unique_place', $id)
            ->where($vilGeom)
            ->get();
        return $query;
    }

    public function get_up_by_name_api($name = null)
    {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id_unique_place as id,{$this->table}.id_user,{$this->table}.name,{$this->table}.address,{$this->table}.status,{$this->table}.cp as contact_person,{$this->table}.description,{$this->table}.video_url";
        $vilGeom = "regional.id_regional = '1' AND ST_Contains(regional.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, unique_place.lat, unique_place.lng")
            ->from('regional')
            ->like("{$this->table}.name", $name)
            ->where($vilGeom)
            ->get();
        return $query;
    }

    public function get_up_by_radius_api($data = null)
    {
        $radius = (int)$data['radius'] / 1000;
        $lat = $data['lat'];
        $long = $data['long'];
        $jarak = "(6371 * acos(cos(radians({$lat})) * cos(radians({$this->table}.lat)) * cos(radians({$this->table}.lng) - radians({$long})) + sin(radians({$lat}))* sin(radians({$this->table}.lat))))";
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id_unique_place as id,{$this->table}.id_user,{$this->table}.name,{$this->table}.address,{$this->table}.status,{$this->table}.cp as contact_person,{$this->table}.description,{$this->table}.video_url";
        $vilGeom = "regional.id_regional = '1' AND ST_Contains(regional.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, unique_place.lat, unique_place.lng, {$jarak} as jarak")
            ->from('regional')
            ->where($vilGeom)
            ->having(['jarak <=' => $radius])
            ->get();
        return $query;
    }

    public function get_up_in_id_api($id = null)
    {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id_unique_place as id,{$this->table}.id_user,{$this->table}.name,{$this->table}.address,{$this->table}.status,{$this->table}.cp as contact_person,{$this->table}.description,{$this->table}.video_url";
        $vilGeom = "regional.id_regional = '1' AND ST_Contains(regional.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, unique_place.lat, unique_place.lng")
            ->from('regional')
            ->whereIn('unique_place.id_unique_place', $id)
            ->where($vilGeom)
            ->get();
        return $query;
    }

    public function get_new_id_api()
    {
        $lastId = $this->db->table($this->table)->select('id_unique_place')->orderBy('id_unique_place', 'ASC')->get()->getLastRow('array');
        if ($lastId != null) {
            $count = (int)substr($lastId['id_unique_place'], 1);
            $id = sprintf('U%02d', $count + 1);
        } else {
            $count = 0;
            $id = sprintf('U%02d', $count + 1);
        }

        return $id;
    }

    public function add_up_api($unique_place = null, $geojson = null)
    {
        $unique_place['created_at'] = Time::now();
        $unique_place['updated_at'] = Time::now();
        $insert = $this->db->table($this->table)
            ->insert($unique_place);
        $update = $this->db->table($this->table)
            ->set('geom', "ST_GeomFromGeoJSON('{$geojson}')", false)
            ->where('id_unique_place', $unique_place['id_unique_place'])
            ->update();
        return $insert && $update;
    }

    public function update_up_api($id = null, $unique_place = null, $geojson = null)
    {
        $unique_place['updated_at'] = Time::now();
        $query = $this->db->table($this->table)
            ->where('id_unique_place', $id)
            ->update($unique_place);
        $update = $this->db->table($this->table)
            ->set('geom', "ST_GeomFromGeoJSON('{$geojson}')", false)
            ->where('id_unique_place', $id)
            ->update();
        return $query && $update;
    }
}
