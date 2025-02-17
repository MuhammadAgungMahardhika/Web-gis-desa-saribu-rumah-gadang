<?php

namespace App\Controllers\Web;

use App\Controllers\Api\WorshipPlace;
use App\Models\PackageModel;
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
use CodeIgniter\RESTful\ResourcePresenter;
use CodeIgniter\Files\File;

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
    protected $helpers = ['auth', 'url', 'filesystem'];

    public function __construct()
    {
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
    /**
     * Present a view of resource objects
     *
     * @return mixed
     */
    public function index()
    {
        $contents = $this->PackageModel->get_list_tp_api()->getResultArray();
        $data = [
            'title' => 'Tourism Package',
            'data' => $contents,
        ];

        return view('web/list_package', $data);
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
        $package = $this->PackageModel->get_tp_by_id_api($id)->getRowArray();
        if (empty($package)) {
            return redirect()->to(substr(current_url(), 0, -strlen($id)));
        }
        // # TASK 1
        //untuk ajax
        // if ($this->request->isAJAX()) {
        //     $user_id = $this->request->getGet('user_id');
        //     if ($id) {
        //         $countRating = $this->reservationModel->getRating($id)->getRow();
        //         $userTotal = $this->reservationModel->getUserTotal($id)->getRow();
        //         $userRating = $this->reservationModel->getUserRating($user_id, $id)->getRow();
        //     }
        //     $data = [
        //         'countRating' =>  $countRating,
        //         'userTotal' =>  $userTotal,
        //         'userRating' => $userRating
        //     ];
        //     return json_encode($data);
        // }

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

        // service exclude
        $list_service = $this->DetailServicePackageModel->get_service_by_package_api_exclude($id)->getResultArray();
        $servicesExclude = array();
        foreach ($list_service as $service) {
            $servicesExclude[] = $service['name'];
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
        $package['servicesExclude'] = $servicesExclude;
        $package['reviews'] = $list_review;
        $package['package_day'] = $package_day;
        $package['gallery'] = [$package['url']];
        $package['video_url'] = null;


        $data = [
            'title' => $package['name'],
            'data' => $package,
        ];

        if (url_is('*dashboard*')) {
            return view('dashboard/detail_package', $data);
        }


        return view('web/detail_package', $data);
    }

    /**
     * Present a view to present a new single resource object
     *
     * @return mixed
     */
    public function new()
    {
        $packageTypeData = $this->packageTypeModel->get_list_t_api()->getResultArray();
        $serviceData = $this->ServiceModel->get_list_s_api()->getResultArray();
        $objectData = [];


        $atractionData = $this->atractionModel->get_list_a_api()->getResultArray();
        foreach ($atractionData as $atraction) {
            $atraction['geoJSON'] = null;
            $objectData[] = $atraction;
        }
        $culinaryData = $this->culinaryModel->get_list_cp_api()->getResultArray();
        foreach ($culinaryData as $culinary) {

            $culinary['geoJSON'] = null;
            $objectData[] = $culinary;
        }
        $souvenirData = $this->souvenirModel->get_list_sp_api()->getResultArray();
        foreach ($souvenirData as $souvenir) {
            $souvenir['geoJSON'] = null;
            $objectData[] = $souvenir;
        }
        $worshipData = $this->worshipModel->get_list_wp_api()->getResultArray();
        foreach ($worshipData as $worship) {
            $worship['geoJSON'] = null;
            $objectData[] = $worship;
        }
        $homestayData = $this->HomestayModel->get_list_hm_api()->getResultArray();
        foreach ($homestayData as $homestay) {
            $homestay['geoJSON'] = null;
            $objectData[] = $homestay;
        }
        $data['service_package'] =  [null];
        $data['service_package_exclude'] = [null];

        $data = [
            'title' => 'New Package',
            'packageTypeData' => $packageTypeData,
            'packageDayData' => null,
            'data' => $data,
            'objectData' => $objectData,
            'serviceData' => $serviceData
        ];
        return view('dashboard/package_form', $data);
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
        $id_package = $this->PackageModel->get_new_id_api();
        $url = null;
        if (isset($request['gallery'])) {
            $folder = $request['gallery'][0];
            $filepath = WRITEPATH . 'uploads/' . $folder;
            $filename = get_filenames($filepath)[0];
            $fileImg = new File($filepath . '/' . $filename);
            $fileImg->move(FCPATH . 'media/photos/package');
            delete_files($filepath);
            rmdir($filepath);
            $url = $fileImg->getFilename();
        }

        $requestData = [
            'id' => $id_package,
            'name' => $request['name'],
            // 'id_package_type' => $request['id_package_type'],
            'price' => empty($request['price']) ? "0" : $request['price'],
            'capacity' => $request['capacity'],
            'cp' => $request['cp'],
            'url' => $url,
            'costum' => $request['costum'],
            'description' => $request['description'],
        ];
        // dd($requestData);
        $addtp = $this->PackageModel->add_tp_api($requestData);
        if (isset($request['packageDetailData'])) {
            foreach ($request['packageDetailData'] as $packageDay) {
                $packageDayId = $this->packageDayModel->get_new_id_api();
                $requestPackageDay = [
                    'day' => $packageDayId,
                    'id_package' => $id_package,
                    'description' => $packageDay['packageDayDescription']
                ];
                $addPackageDay = $this->packageDayModel->add_pd_api($requestPackageDay);

                if ($addPackageDay) {
                    foreach ($packageDay['detailPackage'] as $detailPackage) {
                        $detailPackageId = $this->detailPackageModel->get_new_id_api();
                        $requestDetailPackage = [
                            'activity' => $detailPackageId,
                            'id_day' => $packageDayId,
                            'id_package' => $id_package,
                            'id_object' => $detailPackage['id_object'],
                            'activity_type' => $detailPackage['activity_type'],
                            'description' => $detailPackage['description']
                        ];
                        $addDetailPackage =  $this->detailPackageModel->add_dp_api($requestDetailPackage);
                    }
                }
            }
        }
        // Service include
        $addService = true;
        if (isset($request['service_package'])) {
            $services = $request['service_package'];
            $addService = $this->DetailServicePackageModel->add_service_api($id_package, $services, 'include');
        }

        // service exclude
        $addServiceExclude = true;
        if (isset($request['service_package_exclude'])) {
            $servicesExclude = $request['service_package_exclude'];
            $addServiceExclude = $this->DetailServicePackageModel->add_service_api($id_package, $servicesExclude, 'exclude');
        }


        if ($addtp && $addService &&  $addServiceExclude) {
            return redirect()->to(base_url('dashboard/package'));
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
        $package = $this->PackageModel->get_tp_by_id_api($id)->getRowArray();
        $packageTypeData = $this->packageTypeModel->get_list_t_api()->getResultArray();
        $serviceData = $this->ServiceModel->get_list_s_api()->getResultArray();

        // get include service package
        $packageService = $this->DetailServicePackageModel->get_service_by_package_api($id)->getResultArray();

        $selectedService = array();
        foreach ($packageService as $service) {
            $selectedService[] = $service['name'];
        }

        // get exclude service package
        $packageServiceExclude = $this->DetailServicePackageModel->get_service_by_package_api_exclude($id)->getResultArray();
        $selectedServiceExclude = array();
        foreach ($packageServiceExclude as $service) {
            $selectedServiceExclude[] = $service['name'];
        }

        $packageDay = $this->packageDayModel->get_pd_by_package_id_api($id)->getResultArray();
        $no = 0;
        foreach ($packageDay as $day) {
            $packageDay[$no]['detailPackage'] = $this->detailPackageModel->get_objects_by_package_day_id($day['day'])->getResultArray();
            $no++;
        }

        $objectData = [];


        $atractionData = $this->atractionModel->get_list_a_api()->getResultArray();
        foreach ($atractionData as $atraction) {
            $objectData[] = $atraction;
        }
        $culinaryData = $this->culinaryModel->get_list_cp_api()->getResultArray();
        foreach ($culinaryData as $culinary) {
            $objectData[] = $culinary;
        }
        $souvenirData = $this->souvenirModel->get_list_sp_api()->getResultArray();
        foreach ($souvenirData as $souvenir) {
            $objectData[] = $souvenir;
        }
        $worshipData = $this->worshipModel->get_list_wp_api()->getResultArray();
        foreach ($worshipData as $worship) {
            $objectData[] = $worship;
        }
        $homestayData = $this->HomestayModel->get_list_hm_api()->getResultArray();
        foreach ($homestayData as $homestay) {
            $objectData[] = $homestay;
        }


        $package['service_package'] =  $selectedService;
        $package['service_package_exclude'] = $selectedServiceExclude;
        $package['gallery'] = [$package['url']];

        $data = [
            'title' => 'Edit Package',
            'data' => $package,
            'packageTypeData' => $packageTypeData,
            'packageDayData' => $packageDay,
            'objectData' => $objectData,
            'serviceData' => $serviceData
        ];
        return view('dashboard/package_form', $data);
    }

    /**
     * Process the updating, full or partial, of a specific resource object.
     * This should be a POST.
     *
     * @param mixed $id
     *
     * @return mixed
     */
    public function update($id_package = null)
    {
        $request = $this->request->getPost();
        $url = null;
        if (isset($request['gallery'])) {
            $folder = $request['gallery'][0];
            $filepath = WRITEPATH . 'uploads/' . $folder;
            $filename = get_filenames($filepath)[0];
            $fileImg = new File($filepath . '/' . $filename);
            $fileImg->move(FCPATH . 'media/photos/package');
            delete_files($filepath);
            rmdir($filepath);
            $url = $fileImg->getFilename();
        }
        $requestData = [
            'name' => $request['name'],
            // 'id_package_type' => $request['id_package_type'],
            'price' => empty($request['price']) ? "0" : $request['price'],
            'capacity' => $request['capacity'],
            'cp' => $request['cp'],
            'url' => $url,
            'costum' => $request['costum'],
            'description' => $request['description'],
        ];

        $updateTp = $this->PackageModel->update_tp_api($id_package, $requestData);
        if (isset($request['packageDetailData'])) {
            $deletePackageDay = $this->packageDayModel->delete_pd_by_package_id($id_package);

            foreach ($request['packageDetailData'] as $packageDay) {
                $packageDayId = $this->packageDayModel->get_new_id_api();
                $requestPackageDay = [
                    'day' => $packageDayId,
                    'id_package' => $id_package,
                    'description' => $packageDay['packageDayDescription']
                ];
                $addPackageDay = $this->packageDayModel->add_pd_api($requestPackageDay);

                if ($addPackageDay) {
                    if (isset($packageDay['detailPackage'])) {
                        foreach ($packageDay['detailPackage'] as $detailPackage) {
                            $detailPackageId = $this->detailPackageModel->get_new_id_api();
                            $requestDetailPackage = [
                                'activity' => $detailPackageId,
                                'id_day' => $packageDayId,
                                'id_package' => $id_package,
                                'id_object' => $detailPackage['id_object'],
                                'activity_type' => $detailPackage['activity_type'],
                                'description' => $detailPackage['description']
                            ];
                            $addDetailPackage =  $this->detailPackageModel->add_dp_api($requestDetailPackage);
                        }
                    } else {
                        $rollbackPackageDay = $this->packageDayModel->delete_pd_by_day_id($packageDayId);
                    }
                }
            }
        }
        // service include
        $addService = true;

        if (isset($request['service_package'])) {
            $services = $request['service_package'];
            $addService = $this->DetailServicePackageModel->update_service_api($id_package, $services, 'include');
        }

        // service include
        $addServiceExclude = true;
        if (isset($request['service_package_exclude'])) {
            $servicesExclude = $request['service_package_exclude'];
            $addServiceExclude = $this->DetailServicePackageModel->update_service_api($id_package, $servicesExclude, 'exclude');
        }

        if ($updateTp && $addService && $addServiceExclude) {
            return redirect()->to(base_url('dashboard/package'));
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

    public function newCostum()
    {
        $packageTypeData = $this->packageTypeModel->get_list_t_api()->getResultArray();
        $serviceData = $this->ServiceModel->get_list_s_api()->getResultArray();
        $objectData = [];


        $atractionData = $this->atractionModel->get_list_a_api()->getResultArray();
        foreach ($atractionData as $atraction) {
            $atraction['geoJSON'] = null;
            $objectData[] = $atraction;
        }
        $culinaryData = $this->culinaryModel->get_list_cp_api()->getResultArray();
        foreach ($culinaryData as $culinary) {
            $culinary['geoJSON'] = null;
            $objectData[] = $culinary;
        }
        $souvenirData = $this->souvenirModel->get_list_sp_api()->getResultArray();
        foreach ($souvenirData as $souvenir) {
            $souvenir['geoJSON'] = null;
            $objectData[] = $souvenir;
        }
        $worshipData = $this->worshipModel->get_list_wp_api()->getResultArray();
        foreach ($worshipData as $worship) {
            $worship['geoJSON'] = null;
            $objectData[] = $worship;
        }
        $homestayData = $this->HomestayModel->get_list_hm_api()->getResultArray();
        foreach ($homestayData as $homestay) {
            $homestay['geoJSON'] = null;
            $objectData[] = $homestay;
        }

        $data = [
            'title' => 'Costum Destination',
            'packageTypeData' => $packageTypeData,
            'packageDayData' => null,
            'objectData' => $objectData,
            'serviceData' => $serviceData
        ];
        return view('web/costum', $data);
    }
    public function saveCostum()
    {

        // ---------------------Data request------------------------------------
        $request = $this->request->getPost();

        // create package
        $id_package = $this->PackageModel->get_new_id_api();

        $requestData = [
            'id' => $id_package,
            'name' => 'Costume Package By -' . $request['username'],
            'price' => empty($request['price']) ? "0" : $request['price'],
            'capacity' => $request['reservationData']['number_people'],
            'url' => 'costum_package.jpg',
            'costum' => $request['reservationData']['costum'],
        ];

        $addtp = $this->PackageModel->add_tp_api($requestData);

        // create package day + detail package
        if (isset($request['packageDetailData'])) {
            foreach ($request['packageDetailData'] as $packageDay) {
                if (isset($packageDay['detailPackage'])) {
                    $packageDayId = $this->packageDayModel->get_new_id_api();
                    $requestPackageDay = [
                        'day' => $packageDayId,
                        'id_package' => $id_package,
                        'description' => $packageDay['packageDayDescription']
                    ];
                    $addPackageDay = $this->packageDayModel->add_pd_api($requestPackageDay);

                    if ($addPackageDay) {

                        foreach ($packageDay['detailPackage'] as $detailPackage) {
                            $detailPackageId = $this->detailPackageModel->get_new_id_api();
                            $requestDetailPackage = [
                                'activity' => $detailPackageId,
                                'id_day' => $packageDayId,
                                'id_package' => $id_package,
                                'id_object' => $detailPackage['id_object'],
                                'activity_type' => $detailPackage['activity_type'],
                                'description' => $detailPackage['description']
                            ];
                            $addDetailPackage =  $this->detailPackageModel->add_dp_api($requestDetailPackage);
                        }
                    }
                }
            }
        }

        // create detail service
        $addService = true;
        if (isset($request['service_package'])) {
            $services = $request['service_package'];
            $addService = $this->DetailServicePackageModel->add_service_api($id_package, $services, 'include');
        }

        // create reservation
        $addR = true;
        if (isset($request['reservationData'])) {
            $id = $this->ReviewModel->get_new_id_api();
            $requestData = [
                'id' => $id,
                'id_user' => $request['id_user'],
                'id_package' => $id_package,
                'total_price' => empty($request['price']) ? "0" : $request['price'],
                'id_reservation_status' => '1',
                'request_date' => $request['reservationData']['reservation_date'],
                'number_people' => $request['reservationData']['number_people'],
                'comment' => $request['reservationData']['comment']
            ];

            $addR = $this->ReviewModel->add_r_api($requestData);
        }

        if ($addtp && $addService && $addR) {
            return redirect()->to(base_url('web/reservation/') . '/' . $request['id_user']);
        } else {
            return redirect()->back()->withInput();
        }
    }

    public function costumExisting($id)
    {
        // 
        $package = $this->PackageModel->get_tp_by_id_api($id)->getRowArray();

        $serviceData = $this->ServiceModel->get_list_s_api()->getResultArray();

        // get include service package
        $packageService = $this->DetailServicePackageModel->get_service_by_package_api($id)->getResultArray();
        $selectedService = array();
        foreach ($packageService as $service) {
            $selectedService[] = $service['name'];
        }

        // get exclude service package
        $packageServiceExclude = $this->DetailServicePackageModel->get_service_by_package_api_exclude($id)->getResultArray();
        $selectedServiceExclude = array();
        foreach ($packageServiceExclude as $service) {
            $selectedServiceExclude[] = $service['name'];
        }

        $packageDay = $this->packageDayModel->get_pd_by_package_id_api($id)->getResultArray();
        $no = 0;
        foreach ($packageDay as $day) {
            $packageDay[$no]['detailPackage'] = $this->detailPackageModel->get_objects_by_package_day_id($day['day'])->getResultArray();
            $no++;
        }

        $objectData = [];


        $atractionData = $this->atractionModel->get_list_a_api()->getResultArray();
        foreach ($atractionData as $atraction) {

            $atraction['geoJSON'] = null;
            $objectData[] = $atraction;
        }
        $culinaryData = $this->culinaryModel->get_list_cp_api()->getResultArray();
        foreach ($culinaryData as $culinary) {
            $culinary['geoJSON'] = null;
            $objectData[] = $culinary;
        }
        $souvenirData = $this->souvenirModel->get_list_sp_api()->getResultArray();
        foreach ($souvenirData as $souvenir) {
            $souvenir['geoJSON'] = null;
            $objectData[] = $souvenir;
        }
        $worshipData = $this->worshipModel->get_list_wp_api()->getResultArray();
        foreach ($worshipData as $worship) {
            $worship['geoJSON'] = null;
            $objectData[] = $worship;
        }
        $homestayData = $this->HomestayModel->get_list_hm_api()->getResultArray();
        foreach ($homestayData as $homestay) {
            $homestay['geoJSON'] = null;
            $objectData[] = $homestay;
        }


        $package['service_package'] =  $selectedService;
        $package['service_package_exclude'] = $selectedServiceExclude;
        $package['gallery'] = [$package['url']];

        $data = [
            'title' => 'Edit Package',
            'data' => $package,
            // 'homestayData' => $homestayData,
            'packageDayData' => $packageDay,
            'objectData' => $objectData,
            'serviceData' => $serviceData
        ];
        return view('web/costum_existing_package', $data);
    }

    public function saveCostumExisting()
    {
        // ---------------------Data request------------------------------------
        $request = $this->request->getPost();

        // create package
        $id_package = $this->PackageModel->get_new_id_api();
        $requestData = [
            'id' => $id_package,
            'name' =>  $request['reservationData']['package_name'] . ' + Costum By -' . $request['username'],
            'price' => empty($request['price']) ? "0" : $request['price'],
            'capacity' => $request['reservationData']['number_people'],
            'url' => 'costum_package.jpg',
            'costum' => $request['reservationData']['costum'],
        ];

        $addtp = $this->PackageModel->add_tp_api($requestData);

        // create package day + detail package
        if (isset($request['packageDetailData'])) {
            foreach ($request['packageDetailData'] as $packageDay) {
                if (isset($packageDay['detailPackage'])) {
                    $packageDayId = $this->packageDayModel->get_new_id_api();
                    $requestPackageDay = [
                        'day' => $packageDayId,
                        'id_package' => $id_package,
                        'description' => $packageDay['packageDayDescription']
                    ];
                    $addPackageDay = $this->packageDayModel->add_pd_api($requestPackageDay);

                    if ($addPackageDay) {

                        foreach ($packageDay['detailPackage'] as $detailPackage) {
                            $detailPackageId = $this->detailPackageModel->get_new_id_api();
                            $requestDetailPackage = [
                                'activity' => $detailPackageId,
                                'id_day' => $packageDayId,
                                'id_package' => $id_package,
                                'id_object' => $detailPackage['id_object'],
                                'activity_type' => $detailPackage['activity_type'],
                                'description' => $detailPackage['description']
                            ];
                            $addDetailPackage =  $this->detailPackageModel->add_dp_api($requestDetailPackage);
                        }
                    }
                }
            }
        }

        // create detail service
        $addService = true;
        if (isset($request['service_package'])) {
            $services = $request['service_package'];
            $addService = $this->DetailServicePackageModel->add_service_api($id_package, $services, 'include');
        }

        // create reservation
        $addR = true;
        if (isset($request['reservationData'])) {
            $requestData = [
                'id' => $this->ReservasionModel->get_new_id_api(),
                'id_user' => $request['id_user'],
                'id_package' => $id_package,
                'request_date' => $request['reservationData']['reservation_date'],
                'total_price' => empty($request['price']) ? "0" : $request['price'],
                'id_reservation_status' => '1',
                'number_people' => $request['reservationData']['number_people'],
                'comment' => $request['reservationData']['comment']
            ];

            $addR = $this->ReservasionModel->add_r_api($requestData);
        }

        if ($addtp && $addService && $addR) {
            return redirect()->to(base_url('web/reservation/') . '/' . $request['id_user']);
        } else {
            return redirect()->back()->withInput();
        }
    }
}
