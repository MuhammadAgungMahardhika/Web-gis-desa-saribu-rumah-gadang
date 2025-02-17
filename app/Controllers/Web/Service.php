<?php

namespace App\Controllers\Web;

use App\Models\serviceModel;
use CodeIgniter\RESTful\ResourcePresenter;

class Service extends ResourcePresenter
{
    protected $serviceModel;

    protected $helpers = ['auth', 'url', 'filesystem'];

    public function __construct()
    {
        $this->serviceModel = new serviceModel();
    }
    /**
     * Present a view of resource objects
     *
     * @return mixed
     */
    public function index()
    {
        //
    }

    /**
     * Present a view to present a specific resource object
     *
     * @param mixed $id
     *
     * @return mixed
     */
    public function show($id = null)
    {
        //
    }

    /**
     * Present a view to present a new single resource object
     *
     * @return mixed
     */
    public function new()
    {
        $id = $this->serviceModel->get_new_id_api();
        $data = [
            'title' => 'New service',
            'id' => $id
        ];
        return view('dashboard/service_form', $data);
    }

    /**
     * Process the creation/insertion of a new resource object.
     * This should be a POST.
     *
     * @return mixed
     */
    public function create()
    {
        $request = $this->request->getPost();
        $requestData = [
            'id' => $request['id'],
            'name' => $request['service'],
        ];
        $addFC = $this->serviceModel->add_s_api($requestData);
        if ($addFC) {
            return redirect()->to(base_url('dashboard/service'));
        } else {
            return redirect()->back()->withInput();
        }
    }

    /**
     * Present a view to edit the properties of a specific resource object
     *
     * @param mixed $id
     *
     * @return mixed
     */
    public function edit($id = null)
    {
        $service = $this->serviceModel->get_s_by_id_api($id)->getRowArray();

        $data = [
            'title' => 'Edit service',
            'data' => $service
        ];

        return view('dashboard/service_form', $data);
    }

    /**
     * Process the updating, full or partial, of a specific resource object.
     * This should be a POST.
     *
     * @param mixed $id
     *
     * @return mixed
     */
    public function update($id = null)
    {
        $request = $this->request->getPost();
        $requestData = [
            'name' => $request['service'],
        ];
        $updateFC = $this->serviceModel->update_s_api($id, $requestData);
        if ($updateFC) {
            return redirect()->to(base_url('dashboard/service'));
        } else {
            return redirect()->back()->withInput();
        }
    }

    /**
     * Present a view to confirm the deletion of a specific resource object
     *
     * @param mixed $id
     *
     * @return mixed
     */
    public function remove($id = null)
    {
        //
    }

    /**
     * Process the deletion of a specific resource object
     *
     * @param mixed $id
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        //
    }
}
