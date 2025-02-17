<?php

namespace App\Controllers\Api;

use App\Models\GalleryUniquePlaceModel;
use App\Models\ReviewModel;
use App\Models\UniquePlaceModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class UniquePlace extends ResourceController
{
    use ResponseTrait;
    protected $uniquePlaceModel;
    protected $uniquePlaceGalleryModel;
    protected $reviewModel;

    public function __construct()
    {
        $this->uniquePlaceModel = new UniquePlaceModel();
        $this->uniquePlaceGalleryModel = new GalleryUniquePlaceModel();
        $this->reviewModel = new ReviewModel();
    }

    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        //
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        $uniquePlace = $this->uniquePlaceModel->get_up_by_id_api($id)->getRowArray();

        $list_gallery = $this->uniquePlaceGalleryModel->get_gallery_api($id)->getResultArray();
        $galleries = array();
        foreach ($list_gallery as $gallery) {
            $galleries[] = $gallery['url'];
        }

        $list_review = $this->reviewModel->get_review_object_api('id_unique_place', $id)->getResultArray();
        $avg_rating = $this->reviewModel->get_rating('id_unique_place', $id)->getRowArray()['avg_rating'];

        $uniquePlace['gallery'] = $galleries;
        $uniquePlace['avg_rating'] = $avg_rating;
        $uniquePlace['reviews'] = $list_review;

        $response = [
            'data' => $uniquePlace,
            'status' => 200,
            'message' => [
                "Success display detail information of Unique Place"
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
        //
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
        //
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        $deleteUP = $this->uniquePlaceModel->delete(['id_unique_place' => $id]);
        if($deleteUP) {
            $response = [
                'status' => 200,
                'message' => [
                    "Success delete Unique Place"
                ]
            ];
            return $this->respondDeleted($response);
        } else {
            $response = [
                'status' => 404,
                'message' => [
                    "Unique Place not found"
                ]
            ];
            return $this->failNotFound($response);
        }
    }

    public function findByRadius()
    {
        $request = $this->request->getPost();
        $contents = $this->uniquePlaceModel->get_up_by_radius_api($request)->getResult();
        $response = [
            'data' => $contents,
            'status' => 200,
            'message' => [
                "Success find Unique Place by radius"
            ]
        ];
        return $this->respond($response);
    }

    public function findByName()
    {
        $request = $this->request->getPost();
        $name = $request['name'];
        $contents = $this->uniquePlaceModel->get_up_by_name_api($name)->getResult();
        $response = [
            'data' => $contents,
            'status' => 200,
            'message' => [
                "Success find Unique Place by name"
            ]
        ];
        return $this->respond($response);
    }

    public function findByRating()
    {
        $request = $this->request->getPost();
        $rating = $request['rating'];
        $list_rating = $this->reviewModel->get_object_by_rating_api('id_unique_place', $rating)->getResultArray();
        $id_unique_place = array();
        foreach ($list_rating as $rat) {
            $id_unique_place[] = $rat['id_unique_place'];
        }
        if (count($id_unique_place) > 0) {
            $contents = $this->uniquePlaceModel->get_up_in_id_api($id_unique_place)->getResult();
        } else {
            $contents = [];
        }

        $response = [
            'data' => $contents,
            'status' => 200,
            'message' => [
                "Success find Unique Place by rating"
            ]
        ];
        return $this->respond($response);
    }


}
