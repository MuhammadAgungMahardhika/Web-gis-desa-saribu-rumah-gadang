<?php

namespace App\Models;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class PackageModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'tourism_package';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = [];

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
    public function get_list_tp_api($withCostume = null)
    {

        $columns = "{$this->table}.id as id,{$this->table}.id_package_type,{$this->table}.name,{$this->table}.price,{$this->table}.capacity,{$this->table}.cp as contact_person,{$this->table}.description,{$this->table}.url";

        if ($withCostume == null) {
            $query = $this->db->table($this->table)
                ->select("{$columns}")
                ->where('tourism_package.costum !=', '1')
                ->get();
        } else {
            $query = $this->db->table($this->table)
                ->select("{$columns}")
                ->get();
        }
        return $query;
    }

    public function get_list_tp_api_by_id($id)
    {
        $columns = "{$this->table}.id as id,{$this->table}.id_package_type,{$this->table}.name,{$this->table}.price,{$this->table}.capacity,{$this->table}.cp as contact_person,{$this->table}.description,{$this->table}.url";

        // Menjalankan query untuk mengambil data package
        $query = $this->db->table($this->table)
            ->select("{$columns}")
            ->join('detail_package', 'tourism_package.id = detail_package.id_package')
            ->where('tourism_package.id', $id)
            ->get();

        // Ambil hasil query dengan getResult(), yang mengembalikan objek
        $result = $query->getResult(); // Ini mengembalikan objek, bukan array

        // Memeriksa apakah ada hasil
        if (count($result) > 0) {
            $package = (array) $result[0]; // Mengubah objek pertama menjadi array

            // Ambil data detail_package terpisah dan format sebagai array
            $detail_query = $this->db->table('detail_package')
                ->where('id_package', $id)
                ->get();

            // Mengambil semua detail_package dan mengkonversinya ke array
            $details = $detail_query->getResultArray(); // getResultArray() tersedia di sini

            // Menambahkan data detail_package ke dalam package
            foreach ($details as &$detail) {
                // Ambil data objek terkait berdasarkan id_object
                $id_object = $detail['id_object'];

                // Ambil data berdasarkan id_object, misalnya dari tabel atraction
                $object_query = $this->db->table('atraction')
                    ->select('id,name, lat, lng')
                    ->where('id', $id_object)
                    ->get();

                // Cek apakah ada data objek
                $object = $object_query->getRowArray(); // Mengambil data objek pertama dalam bentuk array

                if ($object) {
                    // Ganti id_object dengan data objek (name, lat, lng)
                    $detail['object'] = [
                        'id' => $object['id'],
                        'name' => $object['name'],
                        'lat' => $object['lat'],
                        'lng' => $object['lng']
                    ];
                }

                // Hapus id_object dari detail karena sudah diganti dengan data objek
                unset($detail['id_object']);
            }

            // Menambahkan detail_package yang sudah dimodifikasi ke dalam package
            $package['detail_package'] = $details;

            return $package; // Kembalikan data dengan format yang diinginkan
        }

        return null; // Jika tidak ada data ditemukan
    }



    public function list_by_owner_api($id = null)
    {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id_tourism_package as id,{$this->table}.id_user,{$this->table}.name,{$this->table}.address,{$this->table}.status,{$this->table}.cp as contact_person,{$this->table}.description,{$this->table}.video_url";
        $vilGeom = "regional.id_regional = '1' AND ST_Contains(regional.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, tourism_package.lat, tourism_package.lng")
            ->from('regional')
            ->where($vilGeom)
            ->where('id_user', $id)
            ->get();
        return $query;
    }

    public function get_tp_by_id_api($id = null)
    {
        $columns = "*";
        $query = $this->db->table($this->table)
            ->select("{$columns}")
            ->where('tourism_package.id', $id)
            ->get();
        return $query;
    }



    public function get_new_id_api()
    {
        $lastId = $this->db->table($this->table)->select('id')->orderBy('id', 'ASC')->get()->getLastRow('array');
        if ($lastId != null) {
            $count = (int)substr($lastId['id'], 1);
            $id = sprintf('P%02d', $count + 1);
        } else {
            $count = 0;
            $id = sprintf('P%02d', $count + 1);
        }

        return $id;
    }


    public function add_tp_api($tourism_package = null)
    {
        foreach ($tourism_package as $key => $value) {
            if (empty($value)) {
                unset($tourism_package[$key]);
            }
        }
        $tourism_package['created_at'] = Time::now();
        $tourism_package['updated_at'] = Time::now();
        $insert = $this->db->table($this->table)
            ->insert($tourism_package);
        return $insert;
    }

    public function update_tp_api($id = null, $tourism_package = null)
    {
        foreach ($tourism_package as $key => $value) {
            if (empty($value)) {
                unset($tourism_package[$key]);
            }
        }
        $tourism_package['updated_at'] = Time::now();

        $query = $this->db->table($this->table)
            ->where('id', $id)
            ->update($tourism_package);
        return $query;
    }
}
