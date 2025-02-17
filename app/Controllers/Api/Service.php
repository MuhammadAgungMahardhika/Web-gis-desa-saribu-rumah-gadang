<?php

namespace App\Controllers\Api;

use App\Models\ServiceModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class Service extends ResourceController
{
    use ResponseTrait;

    protected $ServiceModel;

    public function __construct()
    {
        $this->ServiceModel = new ServiceModel();
    }

    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $contents = $this->ServiceModel->get_list_s_api()->getResult();
        $response = [
            'data' => $contents,
            'status' => 200,
            'message' => [
                "Success get list of Service"
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
        $id = $this->ServiceModel->get_new_id_api();
        $requestData = [
            'id' => $id,
            'Service' => $request['Service'],
        ];

        $addFC = $this->ServiceModel->add_tp_api($requestData);
        if ($addFC) {
            $response = [
                'status' => 201,
                'message' => [
                    "Success create new Service"
                ]
            ];
            return $this->respondCreated($response);
        } else {
            $response = [
                'status' => 400,
                'message' => [
                    "Fail create new Service",
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
            'Service' => $request['Service'],
        ];
        $updateFC = $this->ServiceModel->update_fc_api($id, $requestData);
        if ($updateFC) {
            $response = [
                'status' => 200,
                'message' => [
                    "Success update Service"
                ]
            ];
            return $this->respondCreated($response);
        } else {
            $response = [
                'status' => 400,
                'message' => [
                    "Fail update Service",
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
        $delete = $this->ServiceModel->delete(['id' => $id]);
        if ($delete) {
            $response = [
                'status' => 200,
                'message' => [
                    "Success delete Service"
                ]
            ];
            return $this->respondDeleted($response);
        } else {
            $response = [
                'status' => 404,
                'message' => [
                    "Service not found"
                ]
            ];
            return $this->failNotFound('' + $response);
        }
    }
}
