<?php

namespace App\Controllers\Api;

use App\Models\HomestayModel;
use App\Models\RumahGadangModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\GalleryRumahGadangModel;

class Homestay extends ResourceController
{
    use ResponseTrait;

    protected $homestayModel;
    protected $rumahGadangModel;
    protected $galleryRumahGadangModel;
    public function __construct()
    {
        $this->homestayModel = new HomestayModel();
        $this->rumahGadangModel = new  RumahGadangModel();
        $this->galleryRumahGadangModel = new GalleryRumahGadangModel();
    }

    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $homeStay = array();
        $contents = $this->homestayModel->get_list_hm_api()->getResult();
        foreach ($contents as $content) {
            $list_gallery = $this->galleryRumahGadangModel->get_gallery_api($content->id_rumah_gadang)->getResultArray();
            $galleries = array();

            foreach ($list_gallery as $gallery) {
                $galleries[] = $gallery['url'];
            }

            $content->gallery = $galleries;
            $homeStay[] = $content;
        }
        $response = [
            'data' => $homeStay,
            'status' => 200,
            'message' => [
                "Success get list of homestay"
            ]
        ];
        return $this->respond($response);
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        $rumahgadang = $this->rumahGadangModel->get_rg_by_id_homestay_api($id)->getRowArray();

        $response = [
            'data' => $rumahgadang,
            'status' => 200,
            'message' => [
                "Success display detail information of Souvenir Place"
            ]
        ];
        return $this->respond($response);
    }

    /**
     * Return a new resource object, with default properties
     *
     * @return mixed
     */
    public function new()
    {
        //
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
    {
        $request = $this->request->getPost();
        $id = $this->homestayModel->get_new_id_api();
        $requestData = [
            'id' => $id,
            'name' => $request['homestay'],
            'description' => $request['description'],
        ];
        $addFC = $this->homestayModel->add_fc_api($requestData);
        if ($addFC) {
            $response = [
                'status' => 201,
                'message' => [
                    "Success create new homestay"
                ]
            ];
            return $this->respondCreated($response);
        } else {
            $response = [
                'status' => 400,
                'message' => [
                    "Fail create new homestay",
                ]
            ];
            return $this->respond($response, 400);
        }
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($id = null)
    {
        //
    }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     */
    public function update($id = null)
    {
        $request = $this->request->getRawInput();
        $requestData = [
            'homestay' => $request['homestay'],
        ];
        $updateFC = $this->homestayModel->update_fc_api($id, $requestData);
        if ($updateFC) {
            $response = [
                'status' => 200,
                'message' => [
                    "Success update homestay"
                ]
            ];
            return $this->respondCreated($response);
        } else {
            $response = [
                'status' => 400,
                'message' => [
                    "Fail update homestay",
                ]
            ];
            return $this->respond($response, 400);
        }
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        $deleteFC = $this->homestayModel->delete(['id' => $id]);
        if ($deleteFC) {
            $response = [
                'status' => 200,
                'message' => [
                    "Success delete homestay"
                ]
            ];
            return $this->respondDeleted($response);
        } else {
            $response = [
                'status' => 404,
                'message' => [
                    "homestay not found"
                ]
            ];
            return $this->failNotFound($response);
        }
    }
}
