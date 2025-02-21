<?php

namespace App\Controllers\Web;

use App\Database\Migrations\DetailFacilityHomeStay;
use App\Models\DetailFacilityHomestayModel;
use App\Models\DetailFacilityRumahGadangModel;
use App\Models\FacilityRumahGadangModel;
use App\Models\GalleryHomestayModel;
use App\Models\GalleryRumahGadangModel;
use App\Models\HomestayModel;
use App\Models\ReservationModel;
use App\Models\ReviewModel;
use App\Models\RumahGadangModel;
use CodeIgniter\Files\File;
use CodeIgniter\RESTful\ResourcePresenter;

class RumahGadang extends ResourcePresenter
{
    protected $rumahGadangModel;
    protected $homestayModel;
    protected $reservationModel;
    protected $galleryHomestayModel;
    protected $galleryRumahGadangModel;
    protected $detailFacilityRumahGadangModel;
    protected $detailFacilityHomestayModel;
    protected $reviewModel;
    protected $facilityRumahGadangModel;

    protected $helpers = ['auth', 'url', 'filesystem'];

    public function __construct()
    {
        $this->rumahGadangModel = new RumahGadangModel();
        $this->homestayModel  = new HomestayModel();
        $this->reservationModel = new ReservationModel();
        $this->galleryHomestayModel = new GalleryHomestayModel();
        $this->galleryRumahGadangModel = new GalleryRumahGadangModel();
        $this->detailFacilityRumahGadangModel = new DetailFacilityRumahGadangModel();
        $this->detailFacilityHomestayModel = new DetailFacilityHomestayModel();
        $this->reviewModel = new ReviewModel();
        $this->facilityRumahGadangModel = new FacilityRumahGadangModel();
    }

    /**
     * Present a view of resource objects
     *
     * @return mixed
     */
    public function index()
    {
        $contents = $this->rumahGadangModel->get_list_rg_api()->getResultArray();
        $data = [
            'title' => 'Rumah Gadang',
            'data' => $contents,
        ];

        return view('web/list_rumah_gadang', $data);
    }
    public function index2()
    {
        $contents = $this->rumahGadangModel->get_list_hm_api()->getResultArray();
        $data = [
            'title' => 'Homestay',
            'data' => $contents,
        ];

        return view('web/list_homestay', $data);
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
        $rumahGadang = $this->rumahGadangModel->get_rg_by_id_api($id)->getRowArray();
        if (empty($rumahGadang)) {
            return redirect()->to(substr(current_url(), 0, -strlen($id)));
        }
        // homestay 

        $homestayId = $rumahGadang['id_homestay'];
        $galleryHomestay = $this->galleryHomestayModel->get_gallery_api($homestayId)->getResultArray();
        if ($homestayId != null) {
            $homestayData =  $this->homestayModel->get_hm_by_id_api($homestayId)->getRowArray();
            $homestayRating = $this->reservationModel->getAvgHRating($homestayId)->getRowArray();
            $homestayFacility = $this->detailFacilityHomestayModel->get_facility_by_a_api($homestayId)->getResultArray();
            $rumahGadang['homestayData'] = $homestayData;
            $rumahGadang['homestayData']['avg_homestay_rating'] = $homestayRating;
            $rumahGadang['homestayData']['homestay_facility'] = $homestayFacility;
            $rumahGadang['homestayGalleries'] = $galleryHomestay;
        }

        $avg_rating = $this->reviewModel->get_rating('id_rumah_gadang', $id)->getRowArray()['avg_rating'];

        $list_facility = $this->detailFacilityRumahGadangModel->get_facility_by_rg_api($id)->getResultArray();
        $facilities = array();
        foreach ($list_facility as $facility) {
            $facilities[] = $facility['facility'];
        }

        $list_review = $this->reviewModel->get_review_object_api('id_rumah_gadang', $id)->getResultArray();

        $list_gallery = $this->galleryRumahGadangModel->get_gallery_api($id)->getResultArray();
        $galleries = array();
        foreach ($list_gallery as $gallery) {
            $galleries[] = $gallery['url'];
        }


        $rumahGadang['avg_rating'] = $avg_rating;
        $rumahGadang['facilities'] = $facilities;
        $rumahGadang['reviews'] = $list_review;
        $rumahGadang['gallery'] = $galleries;

        $data = [
            'title' => $rumahGadang['name'],
            'data' => $rumahGadang,
        ];

        if (url_is('*dashboard*')) {
            return view('dashboard/detail_rumah_gadang', $data);
        }
        return view('web/detail_rumah_gadang', $data);
    }

    /**
     * Present a view to present a new single resource object
     *
     * @return mixed
     */
    public function new()
    {
        $facilities = $this->facilityRumahGadangModel->get_list_fc_api()->getResultArray();
        $homestayData = $this->homestayModel->get_list_hm_api_new()->getResultArray();
        $data = [
            'title' => 'New Rumah Gadang',
            'facilities' => $facilities,
            'homestayData' => $homestayData
        ];
        // dd($data); 
        return view('dashboard/rumah_gadang_form', $data);
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
        $id = $this->rumahGadangModel->get_new_id_api();
        $requestData = [
            'id_rumah_gadang' => $id,
            'name' => $request['name'],
            'address' => $request['address'],
            'open' => $request['open'],
            'close' => $request['close'],
            'price_ticket' => empty($request['ticket_price']) ? "0" : $request['ticket_price'],
            'cp' => $request['contact_person'],
            'id_homestay' => $request['id_homestay'],
            // 'id_user' => $request['owner'],
            'description' => $request['description'],
            'lat' => $request['lat'],
            'lng' => $request['lng'],
        ];
        foreach ($requestData as $key => $value) {
            if (empty($value)) {
                unset($requestData[$key]);
            }
        }
        $geojson = $request['geo-json'];
        if (isset($request['video'])) {
            $folder = $request['video'];
            $filepath = WRITEPATH . 'uploads/' . $folder;
            $filenames = get_filenames($filepath);
            $vidFile = new File($filepath . '/' . $filenames[0]);
            $vidFile->move(FCPATH . 'media/videos');
            delete_files($filepath);
            rmdir($filepath);
            $requestData['video_url'] = $vidFile->getFilename();
        }
        $addRG = $this->rumahGadangModel->add_rg_api($requestData, $geojson);

        $addFacilities = true;
        if (isset($request['facilities'])) {
            $facilities = $request['facilities'];
            $addFacilities = $this->detailFacilityRumahGadangModel->add_facility_api($id, $facilities);
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
            $this->galleryRumahGadangModel->add_gallery_api($id, $gallery);
        }

        if ($addRG && $addFacilities) {
            return redirect()->to(base_url('dashboard/rumahGadang') . '/' . $id);
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
        $facilities = $this->facilityRumahGadangModel->get_list_fc_api()->getResultArray();
        $homestayData = $this->homestayModel->get_list_hm_api_new()->getResultArray();
        $rumahGadang = $this->rumahGadangModel->get_rg_by_id_api($id)->getRowArray();
        if (empty($rumahGadang)) {
            return redirect()->to('dashboard/rumahGadang');
        }

        $list_facility = $this->detailFacilityRumahGadangModel->get_facility_by_rg_api($id)->getResultArray();
        $selectedFac = array();
        foreach ($list_facility as $facility) {
            $selectedFac[] = $facility['facility'];
        }

        $list_gallery = $this->galleryRumahGadangModel->get_gallery_api($id)->getResultArray();
        $galleries = array();
        foreach ($list_gallery as $gallery) {
            $galleries[] = $gallery['url'];
        }

        $rumahGadang['facilities'] = $selectedFac;
        $rumahGadang['gallery'] = $galleries;
        $data = [
            'title' => 'Edit Rumah Gadang',
            'data' => $rumahGadang,
            'facilities' => $facilities,
            'homestayData' => $homestayData
        ];
        return view('dashboard/rumah_gadang_form', $data);
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
            'address' => $request['address'],
            'open' => $request['open'],
            'close' => $request['close'],
            'price_ticket' => empty($request['ticket_price']) ? '0' : $request['ticket_price'],
            'cp' => $request['contact_person'],
            'id_homestay' => $request['id_homestay'] == 'null' ? null : $request['id_homestay'],
            // 'id_user' => $request['owner'],
            'description' => $request['description'],
            'lat' => $request['lat'],
            'lng' => $request['lng'],
        ];
        // foreach ($requestData as $key => $value) {
        //     if (empty($value)) {
        //         unset($requestData[$key]);
        //     }
        // }
        // dd($requestData);
        $geojson = $request['geo-json'];
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
        $updateRG = $this->rumahGadangModel->update_rg_api($id, $requestData, $geojson);

        $updateFacilities = true;
        if (isset($request['facilities'])) {
            $facilities = $request['facilities'];
            $updateFacilities = $this->detailFacilityRumahGadangModel->update_facility_api($id, $facilities);
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
            $this->galleryRumahGadangModel->update_gallery_api($id, $gallery);
        } else {
            $this->galleryRumahGadangModel->delete_gallery_api($id);
        }

        if ($updateRG && $updateFacilities) {
            return redirect()->to(base_url('dashboard/rumahGadang') . '/' . $id);
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

    public function recommendation()
    {
        $contents = $this->rumahGadangModel->get_recommendation_api()->getResultArray();
        for ($index = 0; $index < count($contents); $index++) {
            $list_gallery = $this->galleryRumahGadangModel->get_gallery_api($contents[$index]['id_rumah_gadang'])->getResultArray();
            $galleries = array();
            foreach ($list_gallery as $gallery) {
                $galleries[] = $gallery['url'];
            }
            $contents[$index]['gallery'] = $galleries;
        }
        $data = [
            'title' => 'Home',
            'data' => $contents,
        ];

        return view('web/recommendation', $data);
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

    public function detail($id = null)
    {
        $rumahGadang = $this->rumahGadangModel->get_rg_by_id_api($id)->getRowArray();

        if (empty($rumahGadang)) {
            return redirect()->to(substr(current_url(), 0, -strlen($id)));
        }

        $avg_rating = $this->reviewModel->get_rating('id_rumah_gadang', $id)->getRowArray()['avg_rating'];


        $list_facility = $this->detailFacilityRumahGadangModel->get_facility_by_rg_api($id)->getResultArray();
        $facilities = array();
        foreach ($list_facility as $facility) {
            $facilities[] = $facility['facility'];
        }

        $list_review = $this->reviewModel->get_review_object_api('id_rumah_gadang', $id)->getResultArray();

        $list_gallery = $this->galleryRumahGadangModel->get_gallery_api($id)->getResultArray();
        $galleries = array();
        foreach ($list_gallery as $gallery) {
            $galleries[] = $gallery['url'];
        }


        $rumahGadang['avg_rating'] = $avg_rating;
        $rumahGadang['facilities'] = $facilities;
        $rumahGadang['reviews'] = $list_review;
        $rumahGadang['gallery'] = $galleries;

        $data = [
            'title' => $rumahGadang['name'],
            'data' => $rumahGadang,
        ];

        if (url_is('*dashboard*')) {
            return view('dashboard/detail_rumah_gadang', $data);
        }
        return view('maps/detail_rumah_gadang', $data);
    }
}
