<?php

namespace App\Controllers\Web;

use App\Models\CategoryEventModel;
use App\Models\GalleryUniquePlaceModel;
use App\Models\ReviewModel;
use App\Models\UniquePlaceModel;
use CodeIgniter\Files\File;
use CodeIgniter\RESTful\ResourcePresenter;

class UniquePlace extends ResourcePresenter
{
    protected $uniquePlaceModel;
    protected $uniquePlaceGalleryModel;
    protected $categoryEventModel;
    protected $reviewModel;
    protected $helpers = ['auth', 'url', 'filesystem'];

    public function __construct()
    {
        $this->uniquePlaceModel = new UniquePlaceModel();
        $this->uniquePlaceGalleryModel = new GalleryUniquePlaceModel();
        $this->reviewModel = new ReviewModel();
        $this->categoryEventModel = new CategoryEventModel();
    }

    /**
     * Present a view of resource objects
     *
     * @return mixed
     */
    public function index()
    {
        $contents = $this->uniquePlaceModel->get_list_up_api()->getResultArray();
        $data = [
            'title' => 'Unique Place',
            'data' => $contents,
        ];

        return view('web/list_unique_place', $data);
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
        $uniquePlace = $this->uniquePlaceModel->get_up_by_id_api($id)->getRowArray();
        if (empty($uniquePlace)) {
            return redirect()->to(substr(current_url(), 0, -strlen($id)));
        }

        $avg_rating = $this->reviewModel->get_rating('id_unique_place', $id)->getRowArray()['avg_rating'];
        $list_review = $this->reviewModel->get_review_object_api('id_unique_place', $id)->getResultArray();

        $list_gallery = $this->uniquePlaceGalleryModel->get_gallery_api($id)->getResultArray();
        $galleries = array();
        foreach ($list_gallery as $gallery) {
            $galleries[] = $gallery['url'];
        }


        $uniquePlace['avg_rating'] = $avg_rating;
        $uniquePlace['reviews'] = $list_review;
        $uniquePlace['gallery'] = $galleries;

        $data = [
            'title' => $uniquePlace['name'],
            'data' => $uniquePlace,
        ];

        if (url_is('*dashboard*')) {
            return view('dashboard/detail_unique_place', $data);
        }
        return view('web/detail_unique_place', $data);
    }

    /**
     * Present a view to present a new single resource object
     *
     * @return mixed
     */
    public function new()
    {
        $categories = $this->categoryEventModel->get_list_cat_api()->getResultArray();
        $data = [
            'title' => 'New Unique Place',
            'categories' => $categories
        ];
        return view('dashboard/unique_place_form', $data);
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
        $id = $this->uniquePlaceModel->get_new_id_api();
        $requestData = [
            'id_unique_place' => $id,
            'name' => $request['name'],
            'address' => $request['address'],
            'description' => $request['description'],
            'cp' => $request['contact_person'],
            'status' => $request['category'],
            'id_user' => $request['owner'],
            'lat' => $request['lat'],
            'lng' => $request['lng'],
        ];
        foreach ($requestData as $key => $value) {
            if(empty($value)) {
                unset($requestData[$key]);
            }
        }
        $geojson = $request['geo-json'];
        if (isset($request['video'])){
            $folder = $request['video'];
            $filepath = WRITEPATH . 'uploads/' . $folder;
            $filenames = get_filenames($filepath);
            $vidFile = new File($filepath . '/' . $filenames[0]);
            $vidFile->move(FCPATH . 'media/videos');
            delete_files($filepath);
            rmdir($filepath);
            $requestData['video_url'] = $vidFile->getFilename();
        }
        $addEV = $this->uniquePlaceModel->add_up_api($requestData, $geojson);

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
            $this->uniquePlaceGalleryModel->add_gallery_api($id, $gallery);
        }

        if ($addEV) {
            return redirect()->to(base_url('dashboard/uniquePlace') . '/' . $id);
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
        $uniquePlace = $this->uniquePlaceModel->get_up_by_id_api($id)->getRowArray();
        if (empty($uniquePlace)) {
            return redirect()->to('dashboard/uniquePlace');
        }

        $list_gallery = $this->uniquePlaceGalleryModel->get_gallery_api($id)->getResultArray();
        $galleries = array();
        foreach ($list_gallery as $gallery) {
            $galleries[] = $gallery['url'];
        }

        $categories = $this->categoryEventModel->get_list_cat_api()->getResultArray();

        $uniquePlace['gallery'] = $galleries;
        $data = [
            'title' => 'Edit Unique Place',
            'data' => $uniquePlace,
            'categories' => $categories
        ];
        return view('dashboard/unique_place_form', $data);
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
            'description' => $request['description'],
            'cp' => $request['contact_person'],
            'status' => $request['category'],
            'id_user' => $request['owner'],
            'lat' => $request['lat'],
            'lng' => $request['lng'],
        ];
        foreach ($requestData as $key => $value) {
            if(empty($value)) {
                unset($requestData[$key]);
            }
        }
        $geojson = $request['geo-json'];
        if (isset($request['video'])){
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
        $updateEV = $this->uniquePlaceModel->update_up_api($id, $requestData, $geojson);

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
            $this->uniquePlaceGalleryModel->update_gallery_api($id, $gallery);
        } else {
            $this->uniquePlaceGalleryModel->delete_gallery_api($id);
        }

        if ($updateEV) {
            return redirect()->to(base_url('dashboard/uniquePlace') . '/' . $id);
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
