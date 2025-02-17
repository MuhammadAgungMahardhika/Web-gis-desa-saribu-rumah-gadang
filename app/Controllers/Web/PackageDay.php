<?php

namespace App\Controllers\Web;

use App\Models\PackageDayModel;
use App\Models\PackageModel;
use CodeIgniter\RESTful\ResourcePresenter;

class PackageDay extends ResourcePresenter
{
    protected $packageDayModel;
    protected $packageModel;

    protected $helpers = ['auth', 'url', 'filesystem'];

    public function __construct()
    {
        $this->packageDayModel = new PackageDayModel();
        $this->packageModel = new PackageModel();
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

        $id = $this->packageDayModel->get_new_id_api();
        $packageData = $this->packageModel->get_list_tp_api()->getResultArray();

        $data = [
            'title' => 'New package_day',
            'id' => $id,
            'packageData' => $packageData
        ];
        return view('dashboard/package_day_form', $data);
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
            'day' => $request['id'],
            'id_package' => $request['id_package'],
            'description' => $request['description'],
        ];
        $addPd = $this->packageDayModel->add_pd_api($requestData);

        if ($addPd) {
            return redirect()->to(base_url('dashboard/packageDay'));
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
        $package_day = $this->packageDayModel->get_pd_by_id_api($id)->getRowArray();
        $data = [
            'title' => 'Edit package day',
            'data' => $package_day
        ];
        return view('dashboard/package_day_form', $data);
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
            'id_package' => $request['id_package'],
            'description' => $request['description'],
        ];

        $updatePd = $this->packageDayModel->update_pd_api($id, $requestData);
        dd($updatePd);
        // if ($updateFC) {
        //     return redirect()->to(base_url('dashboard/package_day'));
        // } else {
        //     return redirect()->back()->withInput();
        // }
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
