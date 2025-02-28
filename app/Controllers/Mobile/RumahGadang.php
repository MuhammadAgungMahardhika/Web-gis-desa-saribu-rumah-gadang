<?php

namespace App\Controllers\Mobile;


use App\Models\RumahGadangModel;
use CodeIgniter\RESTful\ResourcePresenter;

class RumahGadang extends ResourcePresenter
{
    protected $rumahGadangModel;
    protected $helpers = ['auth', 'url', 'filesystem'];

    public function __construct()
    {
        $this->rumahGadangModel = new RumahGadangModel();
    }


    public function maps()
    {
        $contents = $this->rumahGadangModel->get_list_rg_api()->getResultArray();
        $data = [
            'title' => 'Rumah Gadang',
            'data' => $contents,
        ];

        return view('maps/rumah_gadang', $data);
    }
}
