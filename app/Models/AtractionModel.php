<?php

namespace App\Models;


use CodeIgniter\I18n\Time;
use CodeIgniter\Model;


class AtractionModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'atraction';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['id', 'name', 'address', 'open', 'close', 'cp', 'geom', 'price_ticket', 'description', 'video_url', 'lat', 'lng'];

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
    public function get_list_a_api()
    {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id ,{$this->table}.name,{$this->table}.address,{$this->table}.open,{$this->table}.close,{$this->table}.cp as contact_person,{$this->table}.price_ticket as ticket_price,{$this->table}.description,{$this->table}.video_url";
        $vilGeom = "regional.id_regional = '1' AND ST_Contains(regional.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, atraction.lat, atraction.lng")
            ->from('regional')
            ->where($vilGeom)
            ->get();
        return $query;
    }

    public function list_by_owner_api($id = null)
    {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id ,{$this->table}.name,{$this->table}.address,{$this->table}.open,{$this->table}.close,{$this->table}.cp as contact_person,{$this->table}.price_ticket as ticket_price,{$this->table}.description,{$this->table}.video_url";
        $vilGeom = "regional.id_regional = '1' AND ST_Contains(regional.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, atraction.lat, atraction.lng")
            ->from('regional')
            ->where($vilGeom)
            ->where('id_user', $id)
            ->get();
        return $query;
    }

    public function get_a_by_id_api($id = null)
    {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id ,{$this->table}.name,{$this->table}.address,{$this->table}.open,{$this->table}.close,{$this->table}.cp as contact_person,{$this->table}.price_ticket as ticket_price,{$this->table}.description,{$this->table}.video_url";
        $geoJson = "ST_AsGeoJSON({$this->table}.geom) AS geoJson";
        $vilGeom = "regional.id_regional = '1' AND ST_Contains(regional.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, atraction.lat, atraction.lng, {$geoJson}")
            ->from('regional')
            ->where('atraction.id', $id)
            ->where($vilGeom)
            ->get();
        return $query;
    }

    public function get_a_by_name_api($name = null)
    {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id ,{$this->table}.name,{$this->table}.address,{$this->table}.open,{$this->table}.close,{$this->table}.cp as contact_person,{$this->table}.price_ticket as ticket_price,{$this->table}.description,{$this->table}.video_url";
        $vilGeom = "regional.id_regional = '1' AND ST_Contains(regional.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, atraction.lat, atraction.lng")
            ->from('regional')
            ->like("{$this->table}.name", $name)
            ->where($vilGeom)
            ->get();
        return $query;
    }

    public function get_a_by_radius_api($data = null)
    {
        $radius = (int)$data['radius'] / 1000;
        $lat = $data['lat'];
        $long = $data['long'];
        $jarak = "(6371 * acos(cos(radians({$lat})) * cos(radians({$this->table}.lat)) * cos(radians({$this->table}.lng) - radians({$long})) + sin(radians({$lat}))* sin(radians({$this->table}.lat))))";
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id ,{$this->table}.name,{$this->table}.address,{$this->table}.open,{$this->table}.close,{$this->table}.cp as contact_person,{$this->table}.price_ticket as ticket_price,{$this->table}.description,{$this->table}.video_url";
        $vilGeom = "regional.id_regional = '1' AND ST_Contains(regional.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, atraction.lat, atraction.lng, {$jarak} as jarak")
            ->from('regional')
            ->where($vilGeom)
            ->having(['jarak <=' => $radius])
            ->get();
        return $query;
    }

    public function get_a_in_id_api($id = null)
    {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id ,{$this->table}.name,{$this->table}.address,{$this->table}.open,{$this->table}.close,{$this->table}.cp as contact_person,{$this->table}.price_ticket as ticket_price,{$this->table}.description,{$this->table}.video_url";
        $vilGeom = "regional.id_regional = '1' AND ST_Contains(regional.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, atraction.lat, atraction.lng")
            ->from('regional')
            ->whereIn('atraction.id', $id)
            ->where($vilGeom)
            ->get();
        return $query;
    }

    public function get_new_id_api()
    {
        $lastId = $this->db->table($this->table)->select('id')->orderBy('id', 'ASC')->get()->getLastRow('array');
        if ($lastId != null) {
            $count = (int)substr($lastId['id'], 1);
            $id = sprintf('A%02d', $count + 1);
        } else {
            $count = 0;
            $id = sprintf('A%02d', $count + 1);
        }

        return $id;
    }

    public function add_a_api($atraction = null, $geojson = null)
    {
        $atraction['created_at'] = Time::now();
        $atraction['updated_at'] = Time::now();
        $insert = $this->db->table($this->table)
            ->insert($atraction);
        $update = $this->db->table($this->table)
            ->set('geom', "ST_GeomFromGeoJSON('{$geojson}')", false)
            ->where('id', $atraction['id'])
            ->update();
        return $insert && $update;
    }

    public function update_a_api($id = null, $atraction = null, $geojson = null)
    {
        $atraction['updated_at'] = Time::now();
        $query = $this->db->table($this->table)
            ->where('id', $id)
            ->update($atraction);
        $update = $this->db->table($this->table)
            ->set('geom', "ST_GeomFromGeoJSON('{$geojson}')", false)
            ->where('id', $id)
            ->update();
        return $query && $update;
    }
}
