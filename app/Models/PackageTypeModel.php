<?php

namespace App\Models;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class PackageTypeModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'package_type';
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
    public function get_list_t_api()
    {
        $query = $this->db->table($this->table)
            ->select('*')
            ->get();
        return $query;
    }

    public function get_t_by_id_api($id = null)
    {
        $query = $this->db->table($this->table)
            ->select('*')
            ->where('id', $id)
            ->get();
        return $query;
    }

    public function get_new_id_api()
    {
        $lastId = $this->db->table($this->table)->select('id')->orderBy('id', 'ASC')->get()->getLastRow('array');
        if ($lastId != null) {
            $count = (int)substr($lastId['id'], 2);
            $id = sprintf('PT%02d', $count + 1);
        } else {
            $count = 0;
            $id = sprintf('PT%02d', $count + 1);
        }
        return $id;
    }

    public function add_t_api($type = null)
    {
        foreach ($type as $key => $value) {
            if (empty($value)) {
                unset($type[$key]);
            }
        }
        $type['created_at'] = Time::now();
        $type['updated_at'] = Time::now();
        $insert = $this->db->table($this->table)
            ->insert($type);
        return $insert;
    }

    public function update_t_api($id = null, $type = null)
    {
        foreach ($type as $key => $value) {
            if (empty($value)) {
                unset($type[$key]);
            }
        }
        $type['updated_at'] = Time::now();
        $query = $this->db->table($this->table)
            ->where('id', $id)
            ->update($type);
        return $query;
    }
}
