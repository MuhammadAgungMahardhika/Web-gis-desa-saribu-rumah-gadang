<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\I18n\Time;

class HomestayModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'homestay';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['id', 'name', 'address', 'checkin', 'checkout', 'cp', 'price', 'status', 'description', 'url'];

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
    public function get_list_hm_api()
    {
        $columns = "{$this->table}.id,{$this->table}.name,{$this->table}.address,{$this->table}.checkin,{$this->table}.checkout,{$this->table}.cp as contact_person,{$this->table}.status,{$this->table}.price as ticket_price,{$this->table}.description,{$this->table}.url as video_url";
        $query = $this->db->table($this->table)
            ->select("{$columns}")
            ->get();
        return $query;
    }
    public function get_list_hm_api_new()
    {
        $columns = "{$this->table}.id,{$this->table}.name,{$this->table}.address,{$this->table}.checkin,{$this->table}.checkout,{$this->table}.cp as contact_person,{$this->table}.status,{$this->table}.price as ticket_price,{$this->table}.description,{$this->table}.url as video_url";
        $query = $this->db->table($this->table)
            ->select("{$columns}")
            ->where('rumah_gadang.id_homestay =', null)
            ->join('rumah_gadang', 'rumah_gadang.id_homestay = homestay.id', 'left outer')
            ->get();
        return $query;
    }

    public function get_hm_by_id_api($id = null)
    {
        $columns = "{$this->table}.id,{$this->table}.name,{$this->table}.address,{$this->table}.checkin,{$this->table}.checkout,{$this->table}.cp as contact_person,{$this->table}.status,{$this->table}.price as ticket_price,{$this->table}.description,{$this->table}.url as video_url";
        $query = $this->db->table($this->table)
            ->select("{$columns}")
            ->where('id', $id)
            ->get();
        return $query;
    }

    public function get_new_id_api()
    {
        $lastId = $this->db->table($this->table)->select('id')->orderBy('id', 'ASC')->get()->getLastRow('array');
        if ($lastId != null) {
            $count = (int)substr($lastId['id'], 1);
            $id = sprintf('H%02d', $count + 1);
        } else {
            $count = 0;
            $id = sprintf('H%02d', $count + 1);
        }
        return $id;
    }

    public function add_hm_api($homestay = null)
    {
        foreach ($homestay as $key => $value) {
            if (empty($value)) {
                unset($homestay[$key]);
            }
        }
        $homestay['created_at'] = Time::now();
        $homestay['updated_at'] = Time::now();
        $insert = $this->db->table($this->table)
            ->insert($homestay);
        return $insert;
    }

    public function update_hm_api($id = null, $homestay = null)
    {
        foreach ($homestay as $key => $value) {
            if (empty($value)) {
                unset($homestay[$key]);
            }
        }

        $homestay['updated_at'] = Time::now();
        $query = $this->db->table($this->table)
            ->where('id', $id)
            ->update($homestay);
        return $query;
    }
}
