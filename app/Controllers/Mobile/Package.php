<?php

namespace App\Controllers\Mobile;

use App\Models\PackageModel;
use CodeIgniter\RESTful\ResourcePresenter;
use App\Models\AtractionModel;
use App\Models\CulinaryPlaceModel;
use App\Models\SouvenirPlaceModel;
use App\Models\WorshipPlaceModel;
use App\Models\PackageDayModel;
use App\Models\DetailPackageModel;
use App\Models\ServiceModel;
use App\Models\HomestayModel;
use App\Models\DetailServicePackageModel;
use App\Models\PackageTypeModel;
use App\Models\ReservationModel;
use App\Models\ReviewPackageModel;

class Package extends ResourcePresenter
{
    protected $PackageModel;
    protected $packageTypeModel;
    protected $atractionModel;
    protected $culinaryModel;
    protected $souvenirModel;
    protected $worshipModel;
    protected $packageDayModel;
    protected $detailPackageModel;
    protected $ServiceModel;
    protected $HomestayModel;
    protected $DetailServicePackageModel;
    protected $ReviewModel;
    protected $ReservasionModel;
    protected $currentUrl;
    protected $helpers = ['auth', 'url', 'filesystem'];

    public function __construct()
    {
        $this->currentUrl = 'mobile';
        $this->PackageModel = new PackageModel();
        $this->packageTypeModel = new PackageTypeModel();
        $this->atractionModel = new AtractionModel();
        $this->culinaryModel = new CulinaryPlaceModel();
        $this->souvenirModel = new SouvenirPlaceModel();
        $this->worshipModel = new WorshipPlaceModel();
        $this->packageDayModel = new PackageDayModel();
        $this->detailPackageModel = new DetailPackageModel();
        $this->ServiceModel = new ServiceModel();
        $this->HomestayModel = new HomestayModel();
        $this->DetailServicePackageModel = new DetailServicePackageModel();
        $this->ReviewModel = new ReservationModel();
        $this->ReservasionModel = new ReservationModel();
    }

    public function index()
    {
        $contents = $this->PackageModel->get_list_tp_api()->getResultArray();
        $data = [
            'title' => 'Tourism Package',
            'data' => $contents,
            'currentUrl' => $this->currentUrl
        ];

        return view('mobile/package', $data);
    }
    public function show($id = null)
    {
        $package = $this->PackageModel->get_tp_by_id_api($id)->getRowArray();
        if (empty($package)) {
            return redirect()->to(substr(current_url(), 0, -strlen($id)));
        }
        // avg rating
        $avg_rating = $this->ReviewModel->getAvgRating($id)->getRowArray()['avg_rating'];
        // review
        $list_review = $this->ReviewModel->getObjectComment($id)->getResultArray();

        // service
        $list_service = $this->DetailServicePackageModel->get_service_by_package_api($id)->getResultArray();
        $services = array();
        foreach ($list_service as $service) {
            $services[] = $service['name'];
        }


        // package type
        if ($package['id_package_type'] != null) {
            $packageTypeData = $this->packageTypeModel->get_t_by_id_api($package['id_package_type'])->getRowArray();
            $package['type_name'] = $packageTypeData['name'];
        }

        // package day
        $package_day = $this->packageDayModel->get_pd_by_package_id_api($id)->getResultArray();

        for ($i = 0; $i < count($package_day); $i++) {
            $package_day[$i]['package_day_detail'] = $this->detailPackageModel->get_detail_package_by_dp_api($package_day[$i]['day'])->getResultArray();
        }

        $package['avg_rating'] = $avg_rating;
        $package['services'] = $services;
        $package['reviews'] = $list_review;
        $package['package_day'] = $package_day;
        $package['gallery'] = [$package['url']];
        $package['video_url'] = null;


        $data = [
            'title' => $package['name'],
            'data' => $package,
            'currentUrl' => $this->currentUrl
        ];

        return view('mobile/detail_package', $data);
    }

    public function maps($id)
    {
        $contents = $this->PackageModel->get_list_tp_api_by_id($id);
        $data = [
            'title' => 'Package',
            'data' => $contents,
            'currentUrl' => $this->currentUrl
        ];

        return view('mobile/maps/package', $data);
    }
}
