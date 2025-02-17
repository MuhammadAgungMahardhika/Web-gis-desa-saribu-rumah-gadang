<?php

namespace App\Models;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class SouvenirPlaceModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'souvenir';
    protected $primaryKey       = 'id_souvenir';
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_souvenir', 'name', 'address', 'cp', 'geom', 'open', 'close', 'description', 'lat', 'lng'];

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
    public function get_list_sp_api()
    {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id_souvenir as id,{$this->table}.name,{$this->table}.address,{$this->table}.cp as contact_person,{$this->table}.open,{$this->table}.close,{$this->table}.description";
        $vilGeom = "regional.id_regional = '1' AND ST_Contains(regional.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, souvenir.lat, souvenir.lng")
            ->from('regional')
            ->where($vilGeom)
            ->get();
        return $query;
    }

    public function list_by_owner_api($id = null)
    {
        $query = $this->db->table($this->table)
            ->select('souvenir.*, CONCAT(account.first_name, " ", account.last_name) as owner_name')
            ->where('owner', $id)
            ->join('account', 'souvenir.owner = account.id')
            ->get();
        return $query;
    }

    public function get_sp_by_id_api($id = null)
    {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id_souvenir as id,{$this->table}.name,{$this->table}.address,{$this->table}.cp as contact_person,{$this->table}.open,{$this->table}.close,{$this->table}.description";
        $vilGeom = "regional.id_regional = '1' AND ST_Contains(regional.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, souvenir.lat, souvenir.lng")
            ->from('regional')
            ->where($vilGeom)
            ->where('souvenir.id_souvenir', $id)
            ->get();
        return $query;
    }

    public function get_sp_by_radius_api($data = null)
    {
        $radius = (int)$data['radius'] / 1000;
        $lat = $data['lat'];
        $long = $data['long'];
        $jarak = "(6371 * acos(cos(radians({$lat})) * cos(radians({$this->table}.lat)) * cos(radians({$this->table}.lng) - radians({$long})) + sin(radians({$lat}))* sin(radians({$this->table}.lat))))";
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id_souvenir as id,{$this->table}.name,{$this->table}.address,{$this->table}.cp as contact_person,{$this->table}.open,{$this->table}.close,{$this->table}.description";
        $vilGeom = "regional.id_regional = '1' AND ST_Contains(regional.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, souvenir.lat, souvenir.lng, {$jarak} as jarak")
            ->from('regional')
            ->where($vilGeom)
            ->having(['jarak <=' => $radius])
            ->get();
        return $query;
    }

    public function get_sp_in_id_api($id = null)
    {
        $query = $this->db->table($this->table)
            ->select('souvenir.*, CONCAT(account.first_name, " ", account.last_name) as owner_name')
            ->whereIn('souvenir.id_souvenir', $id)
            ->join('account', 'souvenir.owner = account.id')
            ->get();
        return $query;
    }

    public function get_new_id_api()
    {
        $lastId = $this->db->table($this->table)->select('id_souvenir')->orderBy('id_souvenir', 'ASC')->get()->getLastRow('array');
        if ($lastId != null) {
            $count = (int)substr($lastId['id_souvenir'], 1);
            $id = sprintf('S%01d', $count + 1);
        } else {
            $count = 0;
            $id = sprintf('S%01d', $count + 1);
        }

        return $id;
    }

    public function add_sp_api($souvenir_place = null)
    {
        foreach ($souvenir_place as $key => $value) {
            if (empty($value)) {
                unset($souvenir_place[$key]);
            }
        }
        $souvenir_place['created_at'] = Time::now();
        $souvenir_place['updated_at'] = Time::now();
        $query = $this->db->table($this->table)
            ->insert($souvenir_place);
        return $query;
    }

    public function update_sp_api($id = null, $souvenir_place = null)
    {
        foreach ($souvenir_place as $key => $value) {
            if (empty($value)) {
                unset($souvenir_place[$key]);
            }
        }
        $souvenir_place['updated_at'] = Time::now();
        $query = $this->db->table($this->table)
            ->where('id_souvenir', $id)
            ->update($souvenir_place);
        return $query;
    }
}
