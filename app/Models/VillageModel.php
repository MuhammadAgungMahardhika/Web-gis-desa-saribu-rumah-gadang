<?php

namespace App\Models;

use CodeIgniter\Model;

class VillageModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'regional';
    protected $primaryKey       = 'id_regional';
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_regional', 'name', 'district', 'geom'];

    // Dates
    protected $useTimestamps = false;

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // API
    public function get_sumpur_api() {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $query = $this->db->table($this->table)
            ->select("id_regional, name")
            ->where('id_regional', '1')
            ->get();
        return $query;
    }
    
    public function get_desa_wisata_api() {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $query = $this->db->table($this->table)
            ->select("id_regional, name")
            ->where('id_regional', '2')
            ->get();
        return $query;
    }
    
    public function get_geoJson_api($id = null) {
        $geoJson = "ST_AsGeoJSON({$this->table}.geom) AS geoJson";
        $query = $this->db->table($this->table)
            ->select("{$geoJson}")
            ->where('id_regional', $id)
            ->get();
        return $query;
    }
}
