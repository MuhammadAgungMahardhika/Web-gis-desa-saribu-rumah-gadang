<?php

namespace App\Controllers\Api;

use App\Models\DetailDetailPackageModel;
use App\Models\DetailPackageModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class DetailPackage extends ResourceController
{
    use ResponseTrait;


    protected $DetailPackageModel;

    public function __construct()
    {

        $this->DetailPackageModel = new DetailPackageModel();
    }

    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    // public function index()
    // {
    //     $contents = $this->DetailPackageModel->()->getResult();
    //     $response = [
    //         'data' => $contents,
    //         'status' => 200,
    //         'message' => [
    //             "Success get list of Package"
    //         ]
    //     ];
    //     return $this->respond($response);
    // }

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
        $id = $this->DetailPackageModel->get_new_id_api();
        $requestData = [
            'id' => $id,
            'Package' => $request['Package'],
        ];
        dd($requestData);
        $addFC = $this->DetailPackageModel->add_tp_api($requestData);
        if ($addFC) {
            $response = [
                'status' => 201,
                'message' => [
                    "Success create new Package"
                ]
            ];
            return $this->respondCreated($response);
        } else {
            $response = [
                'status' => 400,
                'message' => [
                    "Fail create new Package",
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
            'Package' => $request['Package'],
        ];
        $updateFC = $this->DetailPackageModel->update_fc_api($id, $requestData);
        if ($updateFC) {
            $response = [
                'status' => 200,
                'message' => [
                    "Success update Package"
                ]
            ];
            return $this->respondCreated($response);
        } else {
            $response = [
                'status' => 400,
                'message' => [
                    "Fail update Package",
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
        $delete = $this->DetailPackageModel->delete(['id' => $id]);
        if ($delete) {
            $response = [
                'status' => 200,
                'message' => [
                    "Success delete Package"
                ]
            ];
            return $this->respondDeleted($response);
        } else {
            $response = [
                'status' => 404,
                'message' => [
                    "Package not found"
                ]
            ];
            return $this->failNotFound($response);
        }
    }
    public function package_day($id = null)
    {
        $objects =
            $response = [
                'status' => 404,
                'message' => [
                    "Package not found"
                ]
            ];
        return $this->failNotFound($response);
    }
}
