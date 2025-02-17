<?php

namespace App\Controllers\Api;

use App\Models\DetailFacilityatractionModel;
use App\Models\GalleryatractionModel;
// use App\Models\ReviewModel;
use App\Models\atractionModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class atraction extends ResourceController
{
    use ResponseTrait;

    protected $atractionModel;
    protected $galleryAtractionModel;
    protected $detailFacilityAtractionModel;
    // protected $reviewModel;

    public function __construct()
    {
        $this->atractionModel = new atractionModel();
        $this->galleryAtractionModel = new GalleryatractionModel();
        $this->detailFacilityAtractionModel = new DetailFacilityatractionModel();
        // $this->reviewModel = new ReviewModel();
    }

    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $atraction = array();
        $contents = $this->atractionModel->get_list_a_api()->getResult();
        foreach ($contents as $content) {
            $list_gallery = $this->galleryAtractionModel->get_gallery_api($content->id)->getResultArray();
            $galleries = array();
            foreach ($list_gallery as $gallery) {
                $galleries[] = $gallery['url'];
            }
            $content->gallery = $galleries[0];
            $atraction[] = $content;
        }
        $response = [
            'data' => $atraction,
            'status' => 200,
            'message' => [
                "Success get list of Atraction"
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
        $atraction = $this->atractionModel->get_a_by_id_api($id)->getRowArray();

        $list_gallery = $this->galleryAtractionModel->get_gallery_api($id)->getResultArray();
        $galleries = array();
        foreach ($list_gallery as $gallery) {
            $galleries[] = $gallery['url'];
        }

        $list_facility = $this->detailFacilityAtractionModel->get_facility_by_a_api($id)->getResultArray();
        $facilities = array();
        foreach ($list_facility as $facility) {
            $facilities[] = $facility['name'];
        }

        // $list_review = $this->reviewModel->get_review_object_api('id_rumah_gadang', $id)->getResultArray();
        // $avg_rating = $this->reviewModel->get_rating('id_rumah_gadang', $id)->getRowArray()['avg_rating'];

        $atraction['facilities'] = $facilities;
        $atraction['gallery'] = $galleries;
        // $atraction['avg_rating'] = $avg_rating;
        // $atraction['reviews'] = $list_review;

        $response = [
            'data' => $atraction,
            'status' => 200,
            'message' => [
                "Success display detail information of Atraction"
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
        $request = $this->request->getJSON(true);
        $id = $this->atractionModel->get_new_id_api();
        $requestData = [
            'id_rumah_gadang' => $id,
            'name' => $request['name'],
            'address' => $request['address'],
            'open' => $request['open'],
            'close' => $request['close'],
            'price_ticket' => $request['ticket_price'],
            'cp' => $request['contact_person'],
            'status' => $request['status'],
            'id_recommendation' => $request['recom'],
            'id_user' => $request['owner'],
            'description' => $request['description'],
            'video_url' => $request['video_url'],
        ];
        foreach ($requestData as $key => $value) {
            if (empty($value)) {
                unset($requestData[$key]);
            }
        }
        $geojson = $request['geojson'];
        $addRG = $this->atractionModel->add_rg_api($requestData, $geojson);
        $facilities = $request['facilities'];
        $addFacilities = $this->detailFacilityAtractionModel->add_facility_api($id, $facilities);
        $gallery = $request['gallery'];
        $addGallery = $this->galleryAtractionModel->add_gallery_api($id, $gallery);
        if ($addRG && $addFacilities && $addGallery) {
            $response = [
                'status' => 201,
                'message' => [
                    "Success create new Atraction"
                ]
            ];
            return $this->respondCreated($response);
        } else {
            $response = [
                'status' => 400,
                'message' => [
                    "Fail create new Atraction",
                    "Add Atraction: {$addRG}",
                    "Add Facilities: {$addFacilities}",
                    "Add Gallery: {$addGallery}",
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
        $request = $this->request->getJSON(true);
        $requestData = [
            'name' => $request['name'],
            'address' => $request['address'],
            'open' => $request['open'],
            'close' => $request['close'],
            'price_ticket' => $request['ticket_price'],
            'cp' => $request['contact_person'],
            'status' => $request['status'],
            'id_recommendation' => $request['recom'],
            'id_user' => $request['owner'],
            'description' => $request['description'],
            'video_url' => $request['video_url'],
        ];
        $geojson = $request['geojson'];
        $updateRG = $this->atractionModel->update_rg_api($id, $requestData, $geojson);
        $facilities = $request['facilities'];
        $updateFacilities = $this->detailFacilityAtractionModel->update_facility_api($id, $facilities);
        $gallery = $request['gallery'];
        $updateGallery = $this->galleryAtractionModel->update_gallery_api($id, $gallery);
        if ($updateRG && $updateFacilities && $updateGallery) {
            $response = [
                'status' => 200,
                'message' => [
                    "Success update Atraction"
                ]
            ];
            return $this->respondCreated($response);
        } else {
            $response = [
                'status' => 400,
                'message' => [
                    "Fail update Atraction",
                    "Update Atraction: {$updateRG}",
                    "Update Facilities: {$updateFacilities}",
                    "Update Gallery: {$updateGallery}",
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
        $deleteAt = $this->atractionModel->delete(['id' => $id]);
        if ($deleteAt) {
            $response = [
                'status' => 200,
                'message' => [
                    "Success delete Atraction"
                ]
            ];
            return $this->respondDeleted($response);
        } else {
            $response = [
                'status' => 404,
                'message' => [
                    "Atraction not found"
                ]
            ];
            return $this->failNotFound($response);
        }
    }

    public function recommendation()
    {

        $contents = $this->atractionModel->get_recommendation_api()->getResultArray();
        for ($index = 0; $index < count($contents); $index++) {
            $list_gallery = $this->galleryatractionModel->get_gallery_api($contents[$index]['id_rumah_gadang'])->getResultArray();
            $galleries = array();
            foreach ($list_gallery as $gallery) {
                $galleries[] = $gallery['url'];
            }
            $contents[$index]['gallery'] = $galleries;
        }

        $response = [
            'data' => $contents,
            'status' => 200,
            'message' => [
                "Success get list of recommended Atraction"
            ]
        ];
        return $this->respond($response);
    }

    public function recommendationByOwner()
    {
        $request = $this->request->getPost();
        $contents = $this->atractionModel->recommendation_by_owner_api($request['id'])->getResultArray();
        for ($index = 0; $index < count($contents); $index++) {
            $list_gallery = $this->galleryatractionModel->get_gallery_api($contents[$index]['id'])->getResultArray();
            $galleries = array();
            foreach ($list_gallery as $gallery) {
                $galleries[] = $gallery['url'];
            }
            $contents[$index]['gallery'] = $galleries;
        }

        $response = [
            'data' => $contents,
            'status' => 200,
            'message' => [
                "Success get list of recommended Atraction"
            ]
        ];
        return $this->respond($response);
    }

    public function recommendationList()
    {
        $contents = $this->atractionModel->get_recommendation_data_api()->getResultArray();

        $response = [
            'data' => $contents,
            'status' => 200,
            'message' => [
                "Success get list of recommendation"
            ]
        ];
        return $this->respond($response);
    }

    public function updateRecommendation()
    {
        $request = $this->request->getPost();
        $requestData = [
            'id_rumah_gadang' => $request['id'],
            'id_recommendation' => $request['recom']
        ];
        $updateRecom = $this->atractionModel->update_recom_api($requestData);
        if ($updateRecom) {
            $response = [
                'status' => 201,
                'message' => [
                    "Success update Atraction Recommendation"
                ]
            ];
            return $this->respondCreated($response);
        } else {
            $response = [
                'status' => 400,
                'message' => [
                    "Fail update Atraction Recommendation",
                    "Update Atraction Recommendation: {$updateRecom}",
                ]
            ];
            return $this->respond($response, 400);
        }
    }

    public function findByName()
    {
        $request = $this->request->getPost();
        $name = $request['name'];
        $contents = $this->atractionModel->get_rg_by_name_api($name)->getResult();
        $response = [
            'data' => $contents,
            'status' => 200,
            'message' => [
                "Success find Atraction by name"
            ]
        ];
        return $this->respond($response);
    }

    public function findByRadius()
    {
        $request = $this->request->getPost();
        $contents = $this->atractionModel->get_a_by_radius_api($request)->getResult();
        $response = [
            'data' => $contents,
            'status' => 200,
            'message' => [
                "Success find Atraction by radius"
            ]
        ];
        return $this->respond($response);
    }

    public function findByFacility()
    {
        $request = $this->request->getPost();
        $facility = $request['facility'];
        $list_facility = $this->detailFacilityatractionModel->get_facility_by_fc_api($facility)->getResultArray();
        $rumah_gadang_id = array();
        foreach ($list_facility as $facil) {
            $rumah_gadang_id[] = $facil['id_rumah_gadang'];
        }
        $contents = $this->atractionModel->get_rg_in_id_api($rumah_gadang_id)->getResult();
        $response = [
            'data' => $contents,
            'status' => 200,
            'message' => [
                "Success find Atraction by facility"
            ]
        ];
        return $this->respond($response);
    }

    public function findByRating()
    {
        $request = $this->request->getPost();
        $rating = $request['rating'];
        $list_rating = $this->reviewModel->get_object_by_rating_api('id_rumah_gadang', $rating)->getResultArray();
        $rumah_gadang_id = array();
        foreach ($list_rating as $rat) {
            $rumah_gadang_id[] = $rat['id_rumah_gadang'];
        }
        if (count($rumah_gadang_id) > 0) {
            $contents = $this->atractionModel->get_rg_in_id_api($rumah_gadang_id)->getResult();
        } else {
            $contents = [];
        }

        $response = [
            'data' => $contents,
            'status' => 200,
            'message' => [
                "Success find Atraction by rating"
            ]
        ];
        return $this->respond($response);
    }

    public function findByCategory()
    {
        $request = $this->request->getPost();
        $status = $request['category'];
        $contents = $this->atractionModel->get_rg_by_status_api($status)->getResult();
        $response = [
            'data' => $contents,
            'status' => 200,
            'message' => [
                "Success find Atraction by status"
            ]
        ];
        return $this->respond($response);
    }

    public function listByOwner()
    {
        $request = $this->request->getPost();
        $contents = $this->atractionModel->list_by_owner_api($request['id'])->getResult();
        $response = [
            'data' => $contents,
            'status' => 200,
            'message' => [
                "Success get list of Atraction"
            ]
        ];
        return $this->respond($response);
    }
}
