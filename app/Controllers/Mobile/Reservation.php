<?php

namespace App\Controllers\Mobile;

use App\Models\HomestayModel;
use App\Models\ReservationModel;
use App\Models\ReservationStatusModel;
use App\Models\PackageModel;
use CodeIgniter\RESTful\ResourcePresenter;

date_default_timezone_set('Asia/Jakarta');
class Reservation extends ResourcePresenter
{
    protected $reservationModel;
    protected $reservationStatusModel;
    protected $packageModel;
    protected $homestayModel;

    protected $helpers = ['auth', 'url', 'filesystem'];

    public function __construct()
    {
        $this->reservationModel = new ReservationModel();
        $this->reservationStatusModel = new ReservationStatusModel();
        $this->packageModel = new PackageModel();
        $this->homestayModel = new HomestayModel();
    }
    /**
     * Present a view of resource objects
     *
     * @return mixed
     */
    public function index()
    {
        //
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
        if (url_is('*dashboard*')) {
            $users_reservation = $this->reservationModel->get_list_r_api()->getResultArray();
        } else {
            $users_reservation = $this->reservationModel->get_r_by_id_user_api($id)->getResultArray();
        }


        $no = 0;

        // reservation status dan paket
        foreach ($users_reservation as $item) {
            $reservationId = $item['id'];
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


            // cancel if deposit is not added beyond 3 days
            if ($dateNow == $requestDateMin3 && ($reservation_status == 2 || $reservation_status == 1) && $deposit_date == null) {
                // update status
                $users_reservation[$no]['id_reservation_status'] = 3;
                $this->reservationModel->update_r_api($reservationId, ['id_reservation_status' => 3]);
            }

            // finsih when request date pass the current date
            if ($request_date  < $dateNow && $reservation_status != 3) {
                // update status
                $users_reservation[$no]['id_reservation_status'] = 5;
                $this->reservationModel->update_r_api($reservationId, ['id_reservation_status' => 5]);
            }

            $reservation_status_id = $item['id_reservation_status'];
            $reservationStatus = $this->reservationStatusModel->get_s_by_id_api($reservation_status_id)->getRowArray();
            $users_reservation[$no]['status'] = $reservationStatus['status'];
            if ($item['id_package'] != null) {
                $dataId = $item['id_package'];
                $data = $this->packageModel->get_tp_by_id_api($dataId)->getRowArray();
                $users_reservation[$no]['package_name'] = $data['name'];
                $users_reservation[$no]['package_price'] = $data['price'];
            } else if ($item['id_homestay'] != null) {
                $dataId = $item['id_homestay'];
                $data = $this->homestayModel->get_hm_by_id_api($dataId)->getRowArray();
                $users_reservation[$no]['package_name'] = $data['name'];
                $users_reservation[$no]['package_price'] = $data['ticket_price'];
            }
            $no++;
        }

        $data = [
            'title' => 'User Reservation',
            'data' => $users_reservation,
            'currentUrl' => 'mobile'
        ];

        return view('mobile/reservation', $data);
    }

    public function checkout($ids)
    {
        foreach ($ids as $id) {
            $this->reservationModel->update($id, [
                'id_reservation_status' => 2
            ]);
        }
    }
}
