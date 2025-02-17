<?php

namespace App\Controllers\Api;

use App\Models\HomestayUnitModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class HomestayUnit extends ResourceController
{
    use ResponseTrait;

    protected $HomestayUnitModel;

    public function __construct()
    {
        $this->HomestayUnitModel = new HomestayUnitModel();
    }

    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $contents = $this->HomestayUnitModel->get_list_hm_api()->getResult();
        $response = [
            'data' => $contents,
            'status' => 200,
            'message' => [
                "Success get list of HomestayUnit"
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
        //
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
        $id = $this->HomestayUnitModel->get_new_id_api();
        $requestData = [
            'id' => $id,
            'name' => $request['HomestayUnit'],
            'description' => $request['description'],
        ];
        $addFC = $this->HomestayUnitModel->add_hm_api($requestData);
        if ($addFC) {
            $response = [
                'status' => 201,
                'message' => [
                    "Success create new HomestayUnit"
                ]
            ];
            return $this->respondCreated($response);
        } else {
            $response = [
                'status' => 400,
                'message' => [
                    "Fail create new HomestayUnit",
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
            'HomestayUnit' => $request['HomestayUnit'],
        ];
        $updateFC = $this->HomestayUnitModel->update_hm_api($id, $requestData);
        if ($updateFC) {
            $response = [
                'status' => 200,
                'message' => [
                    "Success update HomestayUnit"
                ]
            ];
            return $this->respondCreated($response);
        } else {
            $response = [
                'status' => 400,
                'message' => [
                    "Fail update HomestayUnit",
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
        $deleteFC = $this->HomestayUnitModel->delete(['id' => $id]);
        if ($deleteFC) {
            $response = [
                'status' => 200,
                'message' => [
                    "Success delete HomestayUnit"
                ]
            ];
            return $this->respondDeleted($response);
        } else {
            $response = [
                'status' => 404,
                'message' => [
                    "HomestayUnit not found"
                ]
            ];
            return $this->failNotFound($response);
        }
    }
}
