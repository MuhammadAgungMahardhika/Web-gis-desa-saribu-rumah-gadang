<?php

namespace App\Models;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class ReviewModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'comment';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['id', 'status', 'id_rumah_gadang', 'id_event', 'id_unique_place', 'comment', 'date', 'rating', 'id_user'];

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
    public function get_new_id_api() {
        $lastId = $this->db->table($this->table)->select('id_comment')->orderBy('id_comment', 'ASC')->get()->getLastRow('array');
        if($lastId != null){
            $count = (int)substr($lastId['id_comment'], 1);
            $id = sprintf('W%03d', $count + 1);

        }else{
            $count = 0;
            $id = sprintf('W%03d', $count + 1);
        }
        return $id;
    }

    public function add_review_api($review = null) {
        foreach ($review as $key => $value) {
            if(empty($value)) {
                unset($review[$key]);
            }
        }
        $status = 0;
        foreach ($review as $key => $value) {
            if ($key == 'id_rumah_gadang') {
                $status = 1;
            } elseif ($key == 'id_event') {
                $status = 2;
            } elseif ($key == 'id_unique_place') {
                $status = 3;
            }
        }
        $review['status'] = $status;
        $review['created_at'] = Time::now();
        $review['updated_at'] = Time::now();
        $query = $this->db->table($this->table)
            ->insert($review);
        return $query;
    }

    public function get_review_object_api($object = null, $id = null) {
        $query = $this->db->table($this->table)
            ->select('comment.*, users.first_name, users.last_name')
            ->where($object, $id)
            ->join('users', 'comment.id_user = users.id')
            ->orderBy('date', 'DESC')
            ->get();
        return $query;
    }
    
    public function get_rating($object = null, $id = null) {
        $query = $this->db->table($this->table)
            ->select('ceil(avg(rating)) as avg_rating')
            ->where($object, $id)
            ->get();
        return $query;
    }
    
    public function get_object_by_rating_api($object = null, $rating = null) {
        $query = $this->db->table($this->table)
            ->select("ceil(avg(rating)) as avg_rating, {$object}")
            ->where("{$object} IS NOT NULL")
            ->groupBy($object)
            ->having("avg_rating = {$rating}")
            ->get();
        return $query;
    }
}
