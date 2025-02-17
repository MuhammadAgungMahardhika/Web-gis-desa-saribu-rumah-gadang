<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\AccountModel;
use App\Models\AtractionFacilityModel;
use App\Models\AtractionModel;
use App\Models\EventModel;
use App\Models\FacilityRumahGadangModel;
use App\Models\HomestayFacilityModel;
use App\Models\HomestayModel;
use App\Models\HomestayUnitFacilityModel;
use App\Models\HomestayUnitModel;
use App\Models\PackageDayModel;
use App\Models\RumahGadangModel;
use App\Models\ReservationModel;
use App\Models\ReservationStatusModel;
use App\Models\PackageModel;
use App\Models\PackageTypeModel;
use App\Models\ServiceModel;
use App\Models\UniquePlaceModel;
use Myth\Auth\Models\userModel;

date_default_timezone_set('Asia/Jakarta');
class Dashboard extends BaseController
{
    protected $userModel;
    protected $reservationModel;
    protected $homestayModel;
    protected $homestayFacilityModel;
    protected $homestayUnitModel;
    protected $homestayUnitFacilityModel;
    protected $reservationStatusModel;
    protected $rumahGadangModel;
    protected $atractionModel;
    protected $atractionFacilityModel;
    protected $packageModel;
    protected $packageTypeModel;
    protected $serviceModel;
    protected $packageDayModel;
    protected $eventModel;
    protected $facilityModel;
    protected $accountModel;
    protected $uniquePlace;
    protected $helpers = ['auth'];

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->rumahGadangModel = new RumahGadangModel();
        $this->atractionModel = new AtractionModel();
        $this->atractionFacilityModel = new AtractionFacilityModel();
        $this->homestayModel = new HomestayModel();
        $this->homestayFacilityModel = new HomestayFacilityModel();
        $this->homestayUnitModel = new HomestayUnitModel();
        $this->homestayUnitFacilityModel = new HomestayUnitFacilityModel();
        $this->reservationModel = new ReservationModel();
        $this->reservationStatusModel = new ReservationStatusModel();
        $this->packageModel = new PackageModel();
        $this->packageTypeModel = new PackageTypeModel();
        $this->serviceModel = new ServiceModel();
        $this->packageDayModel = new PackageDayModel();
        $this->eventModel = new EventModel();
        $this->uniquePlace = new UniquePlaceModel();
        $this->facilityModel = new FacilityRumahGadangModel();
        $this->accountModel = new AccountModel();
    }
    public function index()
    {
        if (in_groups("owner")) {
            return redirect()->to(base_url('/dashboard/rumahGadang'));
        } elseif (in_groups("admin")) {

            return redirect()->to(base_url('/dashboard/dashboard'));
        }
        return redirect()->to(base_url('/web'));
    }

    public function dashboard()
    {
        $data = [
            'title' => 'Dashboard',
            'category' => 'Dashboard Menu'
        ];
        return view('dashboard/index', $data);
    }
    public function rumahGadang()
    {
        $contents = [];
        if (in_groups('admin')) {
            $contents = $this->rumahGadangModel->get_list_rg_api()->getResultArray();
        } elseif (in_groups('owner')) {
            $contents = $this->rumahGadangModel->list_by_owner_api(user()->id)->getResultArray();
        }

        $data = [
            'title' => 'Manage Rumah Gadang',
            'category' => 'Rumah Gadang',
            'data' => $contents,
        ];
        return view('dashboard/manage', $data);
    }
    public function atraction()
    {
        $contents = [];
        if (in_groups('admin')) {
            $contents = $this->atractionModel->get_list_a_api()->getResultArray();
        }

        $data = [
            'title' => 'Manage Atraction',
            'category' => 'Atraction',
            'data' => $contents,
        ];
        return view('dashboard/manage', $data);
    }
    public function atractionFacility()
    {
        $contents = $this->atractionFacilityModel->get_list_fc_api()->getResultArray();
        $data = [
            'title' => 'Manage Atraction Facility',
            'category' => 'Atraction Facility',
            'data' => $contents,
        ];
        return view('dashboard/manage', $data);
    }
    public function homestayFacility()
    {
        $contents = $this->homestayFacilityModel->get_list_fc_api()->getResultArray();
        $data = [
            'title' => 'Manage Homestay Facility',
            'category' => 'Homestay Facility',
            'data' => $contents,
        ];
        return view('dashboard/manage', $data);
    }

    public function homestay()
    {
        $contents = [];
        if (in_groups('admin')) {
            $contents = $this->homestayModel->get_list_hm_api()->getResultArray();
        }

        $data = [
            'title' => 'Manage Homestay',
            'category' => 'Homestay',
            'data' => $contents,
        ];
        return view('dashboard/manage', $data);
    }
    public function homestayUnit()
    {
        $contents = [];
        if (in_groups('admin')) {
            $contents = $this->homestayUnitModel->get_list_hm_api()->getResultArray();
        }

        $data = [
            'title' => 'Manage Homestay Unit',
            'category' => 'Homestay Unit',
            'data' => $contents,
        ];
        return view('dashboard/manage', $data);
    }
    public function homestayUnitFacility()
    {
        $contents = [];
        if (in_groups('admin')) {
            $contents = $this->homestayUnitFacilityModel->get_list_fc_api()->getResultArray();
        }

        $data = [
            'title' => 'Manage Unit Facility',
            'category' => 'Unit Facility',
            'data' => $contents,
        ];
        return view('dashboard/manage', $data);
    }
    public function reservation()
    {
        $contents = [];
        if (in_groups('admin')) {
            $contents = $this->reservationModel->get_list_r_api()->getResultArray();
            $userData = $this->userModel->get_users()->getResultObject();
            $packageData = $this->packageModel->get_list_tp_api()->getResult();
            $statusData = $this->reservationStatusModel->get_list_s_api()->getResultObject();
        }

        $no = 0;
        // reservation status dan paket
        foreach ($contents as $item) {
            $reservationId = $item['id'];
            $user_id = $item['id_user'];
            $request_date = $item['request_date'];
            $reservation_status = $item['id_reservation_status'];
            $deposit_date = $item['deposit_date'];
            //check if date is passed
            $dateNow = date('Y-m-d');
            $dateConvert = getDate(strtotime($request_date));
            $yearConvert = $dateConvert['year'];
            $monthConvert = $dateConvert['mon'];

            $dayConvert = $dateConvert['mday'] - 3;
            $requestDateMin3 = $yearConvert . "-" . $monthConvert . "-" . $dayConvert;

            if ($dateNow == $requestDateMin3 && ($reservation_status == 2 || $reservation_status == 1) && $deposit_date == null) {
                // update status
                $contents[$no]['id_reservation_status'] = 3;
                $this->reservationModel->update_r_api($reservationId, ['id_reservation_status' => 3]);
            }
            if ($request_date < $dateNow && $reservation_status != 3) {
                // update status
                $contents[$no]['id_reservation_status'] = 4;
                $this->reservationModel->update_r_api($reservationId, ['id_reservation_status' => 4]);
            }

            $reservation_status_id = $item['id_reservation_status'];
            $reservationStatus = $this->reservationStatusModel->get_s_by_id_api($reservation_status_id)->getRowArray();
            $contents[$no]['status'] = $reservationStatus['status'];
            $user_id = $item['id_user'];
            $user = $this->userModel->get_u_by_id_api($user_id)->getRowArray();
            $contents[$no]['username'] = $user['username'];

            if ($item['id_package'] != null) {
                $item_id = $item['id_package'];
                $item = $this->packageModel->get_tp_by_id_api($item_id)->getRowArray();
                $contents[$no]['package_name'] = $item['name'];
            } else if ($item['id_homestay'] != null) {
                $item_id = $item['id_homestay'];
                $item = $this->homestayModel->get_hm_by_id_api($item_id)->getRowArray();
                $contents[$no]['package_name'] = $item['name'];
            }
            $no++;
        }

        $data = [
            'title' => 'Manage Reservation',
            'category' => 'Reservation',
            'data' => $contents,
            'userData' => $userData,
            'packageData' => $packageData,
            'statusData' => $statusData
        ];

        return view('dashboard/reservation', $data);
    }
    public function reservationH()
    {
        $contents = [];
        if (in_groups('admin')) {
            $contents = $this->reservationModel->get_list_r_api()->getResultArray();
        }

        $no = 0;
        // reservation status dan paket
        foreach ($contents as $item) {
            $reservation_status_id = $item['id_reservation_status'];
            $package_id = $item['id_package'];
            $user_id = $item['id_user'];
            $user = $this->userModel->get_u_by_id_api($user_id)->getRowArray();
            $reservationStatus = $this->reservationStatusModel->get_s_by_id_api($reservation_status_id)->getRowArray();
            $package = $this->packageModel->get_tp_by_id_api($package_id)->getRowArray();
            $contents[$no]['username'] = $user['username'];
            $contents[$no]['status'] = $reservationStatus['status'];
            $contents[$no]['package_name'] = $package['name'];
            $no++;
        }

        $data = [
            'title' => 'Manage Reservation',
            'category' => 'Reservation',
            'data' => $contents,
        ];
        return view('dashboard/reservation', $data);
    }
    public function package()
    {
        $contents = [];
        if (in_groups('admin')) {
            $contents = $this->packageModel->get_list_tp_api()->getResultArray();
        }

        $data = [
            'title' => 'Manage Paket Wisata',
            'category' => 'Paket Wisata',
            'data' => $contents,
        ];

        return view('dashboard/manage', $data);
    }
    public function packageType()
    {
        $contents = [];
        if (in_groups('admin')) {
            $contents = $this->packageTypeModel->get_list_t_api()->getResultArray();
        }

        $data = [
            'title' => 'Manage Paket Wisata',
            'category' => 'Paket Wisata',
            'data' => $contents,
        ];

        return view('dashboard/manage', $data);
    }

    public function service()
    {
        $contents = $this->serviceModel->get_list_s_api()->getResultArray();
        $data = [
            'title' => 'Manage Service',
            'category' => 'Service',
            'data' => $contents,
        ];
        return view('dashboard/manage', $data);
    }


    public function event()
    {
        $contents = [];
        if (in_groups('admin')) {
            $contents = $this->eventModel->get_list_ev_api()->getResultArray();
        } elseif (in_groups('owner')) {
            $contents = $this->eventModel->list_by_owner_api(user()->id)->getResultArray();
        }

        $data = [
            'title' => 'Manage Event',
            'category' => 'Event',
            'data' => $contents,
        ];
        return view('dashboard/manage', $data);
    }

    public function uniquePlace()
    {
        $contents = [];
        if (in_groups('admin')) {
            $contents = $this->uniquePlace->get_list_up_api()->getResultArray();
        }

        $data = [
            'title' => 'Manage Unique Place',
            'category' => 'Unique Place',
            'data' => $contents,
        ];
        return view('dashboard/manage', $data);
    }

    public function facility()
    {
        $contents = $this->facilityModel->get_list_fc_api()->getResultArray();
        $data = [
            'title' => 'Manage Facility',
            'category' => 'Facility',
            'data' => $contents,
        ];
        return view('dashboard/manage', $data);
    }


    public function users()
    {
        $contents = $this->accountModel->get_list_user_api()->getResultArray();
        $data = [
            'title' => 'Manage Users',
            'category' => 'Users',
            'data' => $contents,
        ];
        return view('dashboard/manage', $data);
    }

    public function recommendation()
    {
        $contents = [];
        if (in_groups('admin')) {
            $contents = $this->rumahGadangModel->get_list_recommendation_api()->getResultArray();
        } elseif (in_groups('owner')) {
            $contents = $this->rumahGadangModel->recommendation_by_owner_api(user()->id)->getResultArray();
        }

        $recommendations = $this->rumahGadangModel->get_recommendation_data_api()->getResultArray();
        $data = [
            'title' => 'Manage Recommendation',
            'category' => 'Recommendation',
            'data' => $contents,
            'recommendations' => $recommendations,
        ];
        return view('dashboard/recommendation', $data);
    }
}
