<?php

namespace App\Controllers\Api;


use App\Models\DetailPackageModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class PackageObject extends ResourceController
{
    use ResponseTrait;


    protected $detailPackageModel;
    protected $atractionModel;
    protected $culinaryPlaceModel;
    protected $souvenirPlaceModel;
    protected $worshipPlaceModel;

    public function __construct()
    {

        $this->detailPackageModel = new DetailPackageModel();
    }
    function getObjectsByPackageDayId($id_day)
    {

        $objectsData = $this->detailPackageModel->get_objects_by_package_day_id($id_day)->getResultArray();

        $response = [
            'data' => $objectsData,
            'status' => 200,
            'message' => [
                "Success get package"
            ]
        ];
        return $this->respond($response);
    }
}
