<?php

namespace App\Controllers\Web;

use App\Models\DetailFacilityhomestayModel;
use App\Models\GalleryHomestayModel;
use App\Models\HomestayFacilityModel;
use App\Models\HomestayModel;
use CodeIgniter\RESTful\ResourcePresenter;
use CodeIgniter\Files\File;


class Homestay extends ResourcePresenter
{
    protected $HomestayModel;
    protected $homestayFacilityModel;
    protected $detailFacilityHomestayModel;
    protected $homestayGalleryModel;
    protected $helpers = ['auth', 'url', 'filesystem'];

    public function __construct()
    {
        $this->HomestayModel = new HomestayModel();
        $this->homestayFacilityModel = new HomestayFacilityModel();
        $this->detailFacilityHomestayModel = new DetailFacilityhomestayModel();
        $this->homestayGalleryModel = new GalleryHomestayModel();
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
        $id = $this->HomestayModel->get_new_id_api();
        $facilities = $this->homestayFacilityModel->get_list_fc_api()->getResultArray();
        $data = [
            'title' => 'New Homestay',
            'id' => $id,
            'facilities' => $facilities,
        ];

        return view('dashboard/homestay_form', $data);
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
        $id = $this->HomestayModel->get_new_id_api();
        $requestData = [
            'id' => $id,
            'name' => $request['name'],
            'address' => $request['address'],
            'checkin' => $request['checkin'],
            'checkout' => $request['checkout'],
            'cp' => $request['contact_person'],
            'description' => $request['description'],
            'status' => $request['status']
        ];


        foreach ($requestData as $key => $value) {
            if (empty($value)) {
                unset($requestData[$key]);
            }
        }
        if (isset($request['video'])) {
            $folder = $request['video'];
            $filepath = WRITEPATH . 'uploads/' . $folder;
            $filenames = get_filenames($filepath);
            $vidFile = new File($filepath . '/' . $filenames[0]);
            $vidFile->move(FCPATH . 'media/videos');
            delete_files($filepath);
            rmdir($filepath);
            $requestData['url'] = $vidFile->getFilename();
        }

        $addAt = $this->HomestayModel->add_hm_api($requestData);

        $addFacilities = true;
        if (isset($request['facilities'])) {
            $facilities = $request['facilities'];
            $addFacilities = $this->detailFacilityHomestayModel->add_facility_api($id, $facilities);
        }

        if (isset($request['gallery'])) {
            $folders = $request['gallery'];
            $gallery = array();
            foreach ($folders as $folder) {
                $filepath = WRITEPATH . 'uploads/' . $folder;
                $filenames = get_filenames($filepath);
                $fileImg = new File($filepath . '/' . $filenames[0]);
                $fileImg->move(FCPATH . 'media/photos/homestay');
                delete_files($filepath);
                rmdir($filepath);
                $gallery[] = $fileImg->getFilename();
            }
            $this->homestayGalleryModel->add_gallery_api($id, $gallery);
        }

        if ($addAt && $addFacilities) {
            return redirect()->to(base_url('dashboard/homestay'));
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
        $facilities = $this->homestayFacilityModel->get_list_fc_api()->getResultArray();
        $homestay = $this->HomestayModel->get_hm_by_id_api($id)->getRowArray();
        if (empty($homestay)) {
            return redirect()->to('dashboard/homestay');
        }

        $list_facility = $this->detailFacilityHomestayModel->get_facility_by_a_api($id)->getResultArray();
        $selectedFac = array();
        foreach ($list_facility as $facility) {
            $selectedFac[] = $facility['name'];
        }

        $list_gallery = $this->homestayGalleryModel->get_gallery_api($id)->getResultArray();
        $galleries = array();
        foreach ($list_gallery as $gallery) {
            $galleries[] = $gallery['url'];
        }

        $homestay['facilities'] = $selectedFac;
        $homestay['gallery'] = $galleries;
        $data = [
            'title' => 'Edit Homestay',
            'data' => $homestay,
            'facilities' => $facilities,
        ];
        return view('dashboard/homestay_form', $data);
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
        // dd($request['status']);
        $requestData = [
            'id' => $id,
            'name' => $request['name'],
            'address' => $request['address'],
            'checkin' => $request['checkin'],
            'checkout' => $request['checkout'],
            'cp' => $request['contact_person'],
            'description' => $request['description'],
            'status' => $request['status'],
        ];

        foreach ($requestData as $key => $value) {
            if (empty($value)) {
                unset($requestData[$key]);
            }
        }
        if (isset($request['video'])) {
            $folder = $request['video'];
            $filepath = WRITEPATH . 'uploads/' . $folder;
            $filenames = get_filenames($filepath);
            $vidFile = new File($filepath . '/' . $filenames[0]);
            $vidFile->move(FCPATH . 'media/videos');
            delete_files($filepath);
            rmdir($filepath);
            $requestData['video_url'] = $vidFile->getFilename();
        } else {
            $requestData['video_url'] = null;
        }
        $updateRG = $this->HomestayModel->update_hm_api($id, $requestData);

        $updateFacilities = true;
        if (isset($request['facilities'])) {
            $facilities = $request['facilities'];
            $updateFacilities = $this->detailFacilityHomestayModel->update_facility_api($id, $facilities);
        }

        if (isset($request['gallery'])) {
            $folders = $request['gallery'];
            $gallery = array();
            foreach ($folders as $folder) {
                $filepath = WRITEPATH . 'uploads/' . $folder;
                $filenames = get_filenames($filepath);
                $fileImg = new File($filepath . '/' . $filenames[0]);
                $fileImg->move(FCPATH . 'media/photos/homestay');
                delete_files($filepath);
                rmdir($filepath);
                $gallery[] = $fileImg->getFilename();
            }
            $this->homestayGalleryModel->update_gallery_api($id, $gallery);
        } else {
            $this->homestayGalleryModel->delete_gallery_api($id);
        }

        if ($updateRG && $updateFacilities) {
            return redirect()->to(base_url('dashboard/homestay'));
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
