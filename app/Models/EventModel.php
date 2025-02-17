<?php

namespace App\Models;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class EventModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'event';
    protected $primaryKey       = 'id_event';
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_event', 'name', 'event_start', 'event_end', 'description', 'ticket_price', 'cp', 'id_event_category', 'id_user', 'geom', 'video_url', 'committee', 'lat', 'lng'];

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
    public function get_list_ev_api() {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id_event as id,{$this->table}.name,{$this->table}.event_start as date_start,{$this->table}.event_end as date_end,{$this->table}.description,{$this->table}.ticket_price,{$this->table}.cp,{$this->table}.id_event_category,{$this->table}.id_user,{$this->table}.video_url";
        $vilGeom = "regional.id_regional = '1' AND ST_Contains(regional.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, event.lat, event.lng, event_category.category")
            ->from('regional')
            ->where($vilGeom)
            ->join('event_category', 'event.id_event_category = event_category.id_event_category')
            ->get();
        return $query;
    }

    public function list_by_owner_api($id = null) {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id_event,{$this->table}.name,{$this->table}.event_start,{$this->table}.event_end,{$this->table}.description,{$this->table}.ticket_price,{$this->table}.cp,{$this->table}.id_event_category,{$this->table}.id_user,{$this->table}.video_url";
        $vilGeom = "regional.id_regional = '1' AND ST_Contains(regional.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, event.lat, event.lng")
            ->from('regional')
            ->where($vilGeom)
            ->where('id_user', $id)
            ->get();
        return $query;
    }

    public function get_ev_by_id_api($id = null) {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id_event as id,{$this->table}.name,{$this->table}.event_start as date_start,{$this->table}.event_end as date_end,{$this->table}.description,{$this->table}.ticket_price,{$this->table}.cp as contact_person,{$this->table}.id_event_category as category_id,{$this->table}.id_user,{$this->table}.video_url";
        $geoJson = "ST_AsGeoJSON({$this->table}.geom) AS geoJson";
        $vilGeom = "regional.id_regional = '1' AND ST_Contains(regional.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, event.lat, event.lng, {$geoJson}, event_category.category")
            ->from('regional')
            ->where('event.id_event', $id)
            ->where($vilGeom)
            ->join('event_category', 'event.id_event_category = event_category.id_event_category')
            ->get();
        return $query;
    }

    public function get_ev_by_name_api($name = null) {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id_event as id,{$this->table}.name,{$this->table}.event_start as date_start,{$this->table}.event_end as date_end,{$this->table}.description,{$this->table}.ticket_price,{$this->table}.cp as contact_person,{$this->table}.id_event_category,{$this->table}.id_user,{$this->table}.video_url";
        $vilGeom = "regional.id_regional = '1' AND ST_Contains(regional.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, event.lat, event.lng")
            ->from('regional')
            ->like("{$this->table}.name", $name)
            ->where($vilGeom)
            ->get();
        return $query;
    }

    public function get_ev_by_radius_api($data = null) {
        $radius = (int)$data['radius'] / 1000;
        $lat = $data['lat'];
        $long = $data['long'];
        $jarak = "(6371 * acos(cos(radians({$lat})) * cos(radians({$this->table}.lat)) * cos(radians({$this->table}.lng) - radians({$long})) + sin(radians({$lat}))* sin(radians({$this->table}.lat))))";
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id_event as id,{$this->table}.name,{$this->table}.event_start as date_start,{$this->table}.event_end as date_end,{$this->table}.description,{$this->table}.ticket_price,{$this->table}.cp as contact_person,{$this->table}.id_event_category,{$this->table}.id_user,{$this->table}.video_url";
        $vilGeom = "regional.id_regional = '1' AND ST_Contains(regional.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, event.lat, event.lng, {$jarak} as jarak")
            ->from('regional')
            ->where($vilGeom)
            ->having(['jarak <=' => $radius])
            ->get();
        return $query;
    }

    public function get_ev_by_category_api($category = null) {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id_event as id,{$this->table}.name,{$this->table}.event_start as date_start,{$this->table}.event_end as date_end,{$this->table}.description,{$this->table}.ticket_price,{$this->table}.cp as contact_person,{$this->table}.id_event_category,{$this->table}.id_user,{$this->table}.video_url";
        $vilGeom = "regional.id_regional = '1' AND ST_Contains(regional.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, event.lat, event.lng")
            ->from('regional')
            ->where("{$this->table}.id_event_category", $category)
            ->where($vilGeom)
            ->get();
        return $query;
    }

    public function get_ev_by_date_api($date = null) {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id_event as id,{$this->table}.name,{$this->table}.event_start as date_start,{$this->table}.event_end as date_end,{$this->table}.description,{$this->table}.ticket_price,{$this->table}.cp as contact_person,{$this->table}.id_event_category,{$this->table}.id_user,{$this->table}.video_url";
        $vilGeom = "regional.id_regional = '1' AND ST_Contains(regional.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, event.lat, event.lng")
            ->from('regional')
            ->where('event_start <=', $date)
            ->where('event_end >=', $date)
            ->where($vilGeom)
            ->get();
        return $query;
    }

    public function get_ev_in_id_api($id = null) {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id_event as id,{$this->table}.name,{$this->table}.event_start as date_start,{$this->table}.event_end as date_end,{$this->table}.description,{$this->table}.ticket_price,{$this->table}.cp as contact_person,{$this->table}.id_event_category,{$this->table}.id_user,{$this->table}.video_url";
        $vilGeom = "regional.id_regional = '1' AND ST_Contains(regional.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, event.lat, event.lng")
            ->from('regional')
            ->whereIn('event.id_event', $id)
            ->where($vilGeom)
            ->get();
        return $query;
    }

    public function get_new_id_api() {
        $lastId = $this->db->table($this->table)->select('id_event')->orderBy('id_event', 'ASC')->get()->getLastRow('array');
        if($lastId !=null){
            $count = (int)substr($lastId['id_event'], 1);
            $id = sprintf('E%02d', $count + 1);

        }else{
            $count = 0;
            $id = sprintf('E%02d', $count + 1);
        }
        return $id;
    }

    public function add_ev_api($event = null, $geojson = null) {
        $event['created_at'] = Time::now();
        $event['updated_at'] = Time::now();
        $insert = $this->db->table($this->table)
            ->insert($event);
        $update = $this->db->table($this->table)
            ->set('geom', "ST_GeomFromGeoJSON('{$geojson}')", false)
            ->where('id_event', $event['id_event'])
            ->update();
        return $insert && $update;
    }

    public function update_ev_api($id = null, $event = null, $geojson = null) {
        $event['updated_at'] = Time::now();
        $query = $this->db->table($this->table)
            ->where('id_event', $id)
            ->update($event);
        $update = $this->db->table($this->table)
            ->set('geom', "ST_GeomFromGeoJSON('{$geojson}')", false)
            ->where('id_event', $id)
            ->update();
        return $query && $update;
    }
    
}
