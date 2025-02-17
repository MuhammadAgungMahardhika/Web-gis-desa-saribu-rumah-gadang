<?php

namespace App\Controllers\Web;

use App\Models\AtractionFacilityModel;
use App\Models\GalleryAtractionModel;
use App\Models\ReviewModel;
use App\Models\AtractionModel;
use App\Models\DetailFacilityAtractionModel;
use CodeIgniter\Files\File;
use CodeIgniter\RESTful\ResourcePresenter;

class Atraction extends ResourcePresenter
{
    protected $atractionModel;
    protected $facilitiesModel;
    protected $detailFacilityAtractionModel;
    protected $atractionGalleryModel;
    protected $categoryEventModel;
    protected $reviewModel;
    protected $helpers = ['auth', 'url', 'filesystem'];

    public function __construct()
    {
        $this->atractionModel = new AtractionModel();
        $this->facilitiesModel = new AtractionFacilityModel();
        $this->detailFacilityAtractionModel = new DetailFacilityAtractionModel();
        $this->atractionGalleryModel = new GalleryAtractionModel();
        $this->reviewModel = new ReviewModel();
    }

    /**
     * Present a view of resource objects
     *
     * @return mixed
     */
    public function index()
    {
        $contents = $this->atractionModel->get_list_up_api()->getResultArray();
        $data = [
            'title' => 'atraction ',
            'data' => $contents,
        ];

        return view('web/list_atraction_', $data);
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
        $atraction = $this->atractionModel->get_a_by_id_api($id)->getRowArray();
        if (empty($atraction)) {
            return redirect()->to(substr(current_url(), 0, -strlen($id)));
        }

        // $avg_rating = $this->reviewModel->get_rating('id_rumah_gadang', $id)->getRowArray()['avg_rating'];

        $list_facility = $this->detailFacilityAtractionModel->get_facility_by_a_api($id)->getResultArray();
        $facilities = array();
        foreach ($list_facility as $facility) {
            $facilities[] = $facility['name'];
        }

        // $list_review = $this->reviewModel->get_review_object_api('id_rumah_gadang', $id)->getResultArray();

        $list_gallery = $this->atractionGalleryModel->get_gallery_api($id)->getResultArray();
        $galleries = array();
        foreach ($list_gallery as $gallery) {
            $galleries[] = $gallery['url'];
        }


        // $atraction['avg_rating'] = $avg_rating;
        // $atraction['reviews'] = $list_review;
        $atraction['facilities'] = $facilities;
        $atraction['gallery'] = $galleries;

        $data = [
            'title' => $atraction['name'],
            'data' => $atraction,
        ];

        if (url_is('*dashboard*')) {
            return view('dashboard/detail_atraction', $data);
        }
        return view('web/detail_atraction', $data);
    }

    /**
     * Present a view to present a new single resource object
     *
     * @return mixed
     */
    public function new()
    {
        $facilities = $this->facilitiesModel->get_list_fc_api()->getResultArray();
        $data = [
            'title' => 'New Atraction',
            'facilities' => $facilities,
        ];

        return view('dashboard/atraction_form', $data);
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
        $id = $this->atractionModel->get_new_id_api();
        $requestData = [
            'id' => $id,
            'name' => $request['name'],
            'address' => $request['address'],
            'open' => $request['open'],
            'close' => $request['close'],
            'price_ticket' => empty($request['ticket_price']) ? "0" : $request['ticket_price'],
            'cp' => $request['contact_person'],
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
        $addAt = $this->atractionModel->add_a_api($requestData, $geojson);

        $addFacilities = true;
        if (isset($request['facilities'])) {
            $facilities = $request['facilities'];
            $addFacilities = $this->detailFacilityAtractionModel->add_facility_api($id, $facilities);
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
            $this->atractionGalleryModel->add_gallery_api($id, $gallery);
        }

        if ($addAt && $addFacilities) {
            return redirect()->to(base_url('dashboard/atraction') . '/' . $id);
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
        $facilities = $this->facilitiesModel->get_list_fc_api()->getResultArray();
        $atraction = $this->atractionModel->get_a_by_id_api($id)->getRowArray();
        if (empty($atraction)) {
            return redirect()->to('dashboard/atraction');
        }

        $list_facility = $this->detailFacilityAtractionModel->get_facility_by_a_api($id)->getResultArray();
        $selectedFac = array();
        foreach ($list_facility as $facility) {
            $selectedFac[] = $facility['name'];
        }

        $list_gallery = $this->atractionGalleryModel->get_gallery_api($id)->getResultArray();
        $galleries = array();
        foreach ($list_gallery as $gallery) {
            $galleries[] = $gallery['url'];
        }

        $atraction['facilities'] = $selectedFac;
        $atraction['gallery'] = $galleries;
        $data = [
            'title' => 'Edit Atraction',
            'data' => $atraction,
            'facilities' => $facilities,
        ];
        return view('dashboard/atraction_form', $data);
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
        } else {
            $requestData['video_url'] = null;
        }
        $updateRG = $this->atractionModel->update_a_api($id, $requestData, $geojson);

        $updateFacilities = true;
        if (isset($request['facilities'])) {
            $facilities = $request['facilities'];
            $updateFacilities = $this->detailFacilityAtractionModel->update_facility_api($id, $facilities);
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
            $this->atractionGalleryModel->update_gallery_api($id, $gallery);
        } else {
            $this->atractionGalleryModel->delete_gallery_api($id);
        }

        if ($updateRG && $updateFacilities) {
            return redirect()->to(base_url('dashboard/atraction') . '/' . $id);
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
