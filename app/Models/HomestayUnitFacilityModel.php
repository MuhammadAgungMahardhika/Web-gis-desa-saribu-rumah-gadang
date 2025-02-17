<?php

namespace App\Models;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class HomestayUnitFacilityModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'homestay_unit_facility';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['id', 'name'];

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
    public function get_list_fc_api()
    {
        $query = $this->db->table($this->table)
            ->select('id,name')
            ->get();
        return $query;
    }

    public function get_fc_by_id_api($id = null)
    {
        $query = $this->db->table($this->table)
            ->select('id, name')
            ->where('id', $id)
            ->get();
        return $query;
    }

    public function get_new_id_api()
    {
        $lastId = $this->db->table($this->table)->select('id')->orderBy('id', 'ASC')->get()->getLastRow('array');
        if ($lastId != null) {
            $count = (int)substr($lastId['id'], 2);
            $id = sprintf('UF%02d', $count + 1);
        } else {
            $count = 0;
            $id = sprintf('UF%02d', $count + 1);
        }
        return $id;
    }

    public function add_fc_api($facility = null)
    {
        foreach ($facility as $key => $value) {
            if (empty($value)) {
                unset($facility[$key]);
            }
        }
        $facility['created_at'] = Time::now();
        $facility['updated_at'] = Time::now();
        $insert = $this->db->table($this->table)
            ->insert($facility);
        return $insert;
    }

    public function update_fc_api($id = null, $facility = null)
    {
        foreach ($facility as $key => $value) {
            if (empty($value)) {
                unset($facility[$key]);
            }
        }
        $facility['updated_at'] = Time::now();
        $query = $this->db->table($this->table)
            ->where('id', $id)
            ->update($facility);
        return $query;
    }
}
