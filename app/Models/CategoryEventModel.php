<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryEventModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'event_category';
    protected $primaryKey       = 'id_event_category';
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_event_category', 'category'];

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
    public function get_list_cat_api() {
        $query = $this->db->table($this->table)
            ->select('id_event_category as id, category')
            ->get();
        return $query;
    }
}
