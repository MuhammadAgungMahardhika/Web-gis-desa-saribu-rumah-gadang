<?php

namespace App\Controllers\Web;

use App\Models\AtractionFacilityModel;
use App\Models\HomestayUnitFacilityModel;
use CodeIgniter\RESTful\ResourcePresenter;

class HomestayUnitFacility extends ResourcePresenter
{
    protected $facilityHomestayUnitModel;

    protected $helpers = ['auth', 'url', 'filesystem'];

    public function __construct()
    {
        $this->facilityHomestayUnitModel = new HomestayUnitFacilityModel();
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
        $id = $this->facilityHomestayUnitModel->get_new_id_api();
        $data = [
            'title' => 'New Homestay Unit Facility',
            'id' => $id
        ];
        return view('dashboard/homestay_unit_facility_form', $data);
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
            'name' => $request['name'],
        ];
        $addFC = $this->facilityHomestayUnitModel->add_fc_api($requestData);
        if ($addFC) {
            return redirect()->to(base_url('dashboard/homestayUnitFacility'));
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
        $facility = $this->facilityHomestayUnitModel->get_fc_by_id_api($id)->getRowArray();
        $data = [
            'title' => 'Edit Homestay Unit Facility',
            'data' => $facility
        ];
        return view('dashboard/Homestay_unit_facility_form', $data);
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
            'name' => $request['name'],
        ];
        $updateFC = $this->facilityHomestayUnitModel->update_fc_api($id, $requestData);
        if ($updateFC) {
            return redirect()->to(base_url('dashboard/homestayUnitFacility'));
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
