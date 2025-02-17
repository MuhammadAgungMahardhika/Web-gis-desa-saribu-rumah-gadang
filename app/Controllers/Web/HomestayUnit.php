<?php

namespace App\Controllers\Web;

use App\Models\DetailFacilityHomestayUnitModel;
use App\Models\GalleryHomestayUnitModel;
use App\Models\HomestayModel;
use App\Models\HomestayUnitFacilityModel;
use App\Models\HomestayUnitModel;
use App\Models\HomestayUnitTypeModel;
use CodeIgniter\RESTful\ResourcePresenter;
use CodeIgniter\Files\File;


class HomestayUnit extends ResourcePresenter
{
    protected $homestayModel;
    protected $HomestayUnitModel;
    protected $homestayUnitFacilityModel;
    protected $homestayUnitTypeModel;
    protected $detailFacilityHomestayUnitModel;
    protected $homestayUnitGalleryModel;
    protected $helpers = ['auth', 'url', 'filesystem'];

    public function __construct()
    {
        $this->homestayModel = new HomestayModel();
        $this->HomestayUnitModel = new HomestayUnitModel();
        $this->homestayUnitFacilityModel = new HomestayUnitFacilityModel();
        $this->homestayUnitTypeModel = new HomestayUnitTypeModel();
        $this->detailFacilityHomestayUnitModel = new DetailFacilityHomestayUnitModel();
        $this->homestayUnitGalleryModel = new GalleryHomestayUnitModel();
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
        $id = $this->HomestayUnitModel->get_new_id_api();
        $homestayData = $this->homestayModel->get_list_hm_api()->getResultArray();
        $facilities = $this->homestayUnitFacilityModel->get_list_fc_api()->getResultArray();
        $unitType = $this->homestayUnitTypeModel->get_list_fc_api()->getResultArray();
        $data = [
            'title' => 'New Homestay Unit',
            'id' => $id,
            'homestayData' => $homestayData,
            'facilities' => $facilities,
            'unitTypeData' => $unitType
        ];

        return view('dashboard/homestay_unit_form', $data);
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
        $id = $this->HomestayUnitModel->get_new_id_api();

        $requestData = [
            'id' => $id,
            'id_homestay' => $request['id_homestay'],
            'id_unit_type' => $request['id_unit_type'],
            'name' => $request['name'],
            'description' => $request['description']
        ];

        foreach ($requestData as $key => $value) {
            if (empty($value)) {
                unset($requestData[$key]);
            }
        }

        $addAt = $this->HomestayUnitModel->add_hm_api($requestData);

        $addFacilities = true;
        if (isset($request['facilities'])) {
            $facilities = $request['facilities'];
            $addFacilities = $this->detailFacilityHomestayUnitModel->add_facility_api($id, $facilities);
        }

        if (isset($request['gallery'])) {
            $folders = $request['gallery'];
            $gallery = array();
            foreach ($folders as $folder) {
                $filepath = WRITEPATH . 'uploads/' . $folder;
                $filenames = get_filenames($filepath);
                $fileImg = new File($filepath . '/' . $filenames[0]);
                $fileImg->move(FCPATH . 'media/photos');
                delete_files($filepath);
                rmdir($filepath);
                $gallery[] = $fileImg->getFilename();
            }
            $this->homestayUnitGalleryModel->add_gallery_api($id, $gallery);
        }

        if ($addAt && $addFacilities) {
            return redirect()->to(base_url('dashboard/homestayUnit'));
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
        $facilities = $this->homestayUnitFacilityModel->get_list_fc_api()->getResultArray();
        $homestay = $this->HomestayUnitModel->get_hm_by_id_api($id)->getRowArray();
        $homestayData = $this->homestayModel->get_list_hm_api()->getResultArray();
        $unitType = $this->homestayUnitTypeModel->get_list_fc_api()->getResultArray();

        if (empty($homestay)) {
            return redirect()->to('dashboard/homestay');
        }

        $list_facility = $this->detailFacilityHomestayUnitModel->get_facility_by_a_api($id)->getResultArray();
        $selectedFac = array();
        foreach ($list_facility as $facility) {
            $selectedFac[] = $facility['name'];
        }

        $list_gallery = $this->homestayUnitGalleryModel->get_gallery_api($id)->getResultArray();
        $galleries = array();
        foreach ($list_gallery as $gallery) {
            $galleries[] = $gallery['url'];
        }

        $homestay['facilities'] = $selectedFac;
        $homestay['gallery'] = $galleries;

        $data = [
            'title' => 'Edit Homestay Unit',
            'data' => $homestay,
            'facilities' => $facilities,
            'homestayData' => $homestayData,
            'unitTypeData' => $unitType
        ];
        return view('dashboard/homestay_unit_form', $data);
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
            'id_homestay' => $request['id_homestay'],
            'id_unit_type' => $request['id_unit_type'],
            'name' => $request['name'],
            'price' => $request['price'],
            'description' => $request['description']
        ];
        foreach ($requestData as $key => $value) {
            if (empty($value)) {
                unset($requestData[$key]);
            }
        }
        $updateRG = $this->HomestayUnitModel->update_hm_api($id, $requestData);

        $updateFacilities = true;
        if (isset($request['facilities'])) {
            $facilities = $request['facilities'];
            $updateFacilities = $this->detailFacilityHomestayUnitModel->update_facility_api($id, $facilities);
        }

        if (isset($request['gallery'])) {
            $folders = $request['gallery'];
            $gallery = array();
            foreach ($folders as $folder) {
                $filepath = WRITEPATH . 'uploads/' . $folder;
                $filenames = get_filenames($filepath);
                $fileImg = new File($filepath . '/' . $filenames[0]);
                $fileImg->move(FCPATH . 'media/photos');
                delete_files($filepath);
                rmdir($filepath);
                $gallery[] = $fileImg->getFilename();
            }
            $this->homestayUnitGalleryModel->update_gallery_api($id, $gallery);
        } else {
            $this->homestayUnitGalleryModel->delete_gallery_api($id);
        }

        if ($updateRG && $updateFacilities) {
            return redirect()->to(base_url('dashboard/homestayUnit'));
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
