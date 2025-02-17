<?php

namespace App\Models;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class DetailServicePackageModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'detail_service_package';
    protected $primaryKey       = 'id_detail_service_package';
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_detail_service_package', 'id_service_package', 'id_package', 'status'];

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
    public function get_service_by_package_api($package_id = null)
    {
        $query = $this->db->table($this->table)
            ->select('service.id, service.name')
            ->where('status', 'include')
            ->where('id_package', $package_id)
            ->join('service', 'detail_service_package.id_service_package = service.id')
            ->get();
        return $query;
    }

    public function get_service_by_package_api_exclude($package_id = null)
    {
        $query = $this->db->table($this->table)
            ->select('service.id, service.name')
            ->where('id_package', $package_id)
            ->where('status', 'exclude')
            ->join('service', 'detail_service_package.id_service_package = service.id')
            ->get();
        return $query;
    }

    public function get_service_by_fc_api($service_id = null)
    {
        $query = $this->db->table($this->table)
            ->select('*')
            ->where('id_service_package', $service_id)
            ->get();
        return $query;
    }

    public function get_new_id_api()
    {
        $lastId = $this->db->table($this->table)->select('id_detail_service_package')->orderBy('id_detail_service_package', 'ASC')->get()->getLastRow('array');
        if ($lastId != null) {
            $count = (int)substr($lastId['id_detail_service_package'], 0);
            $id = sprintf('%03d', $count + 1);
        } else {
            $count = 0;
            $id = sprintf('%03d', $count + 1);
        }

        return $id;
    }

    public function add_service_api($id = null, $data = null, $status = null)
    {
        $query = false;
        foreach ($data as $service) {
            $new_id = $this->get_new_id_api();
            $content = [
                'id_detail_service_package' => $new_id,
                'id_package' => $id,
                'id_service_package' => $service,
                'status' => $status,
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ];
            $query = $this->db->table($this->table)->insert($content);
        }
        return $query;
    }

    public function update_service_api($id = null, $data = null, $status = null)
    {
        foreach ($data as $service) {
            $queryDel = $this->db->table($this->table)->where('id_service_package', $service)->delete(['id_package' => $id]);
        }
        $queryIns = $this->add_service_api($id, $data, $status);
        return $queryDel && $queryIns;
    }
    public function delete_service_api($id_package)
    {
        $queryDel = $this->db->table($this->table)->delete(['id_package' => $id_package]);
        return $queryDel;
    }
}
