<?php

namespace App\Controllers\Web;

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
            'data' => $users_reservation
        ];

        if (url_is('*dashboard*')) {
            return view('dashboard/reservation', $data);
        }

        return view('profile/reservation', $data);
    }

    /**
     * Present a view to present a new single resource object
     *
     * @return mixed
     */
    public function new()
    {
        $id = $this->reservationModel->get_new_id_api();
        $data = [
            'title' => 'New Facility',
            'id' => $id
        ];
        return view('dashboard/facility_form', $data);
    }

    /**
     * Process the creation/insertion of a new resource object.
     * This should be a POST.
     *
     * @return mixed
     */
    public function create()
    {
        $request = $this->request->getRawInput();

        $id = $this->reservationModel->get_new_id_api();
        $requestData = [
            'id' => $id,
            'id_user' => $request['id_user'],
            'id_package' => isset($request['id_package']) ? $request['id_package'] : null,
            'id_homestay' => isset($request['id_homestay']) ? $request['id_homestay'] : null,
            'id_reservation_status' => $request['id_reservation_status'],
            'request_date' => $request['reservation_date'],
            'request_date_end' => isset($request['reservation_date_end']) ? $request['reservation_date_end'] : null,
            'number_people' => $request['number_people'],
            'total_price' => $request['total_price'],
            'comment' => $request['comment']
        ];

        $addR = $this->reservationModel->add_r_api($requestData);
        return json_encode($addR);
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
        $facility = $this->reservationModel->get_fc_by_id_api($id)->getRowArray();
        $data = [
            'title' => 'Edit Facility',
            'data' => $facility
        ];
        return view('dashboard/facility_form', $data);
    }

    /**
     * Process the updating, full or partial, of a specific resource object.
     * This should be a POST.
     *
     * @param mixed $id
     *
     * @return mixed
     */
    public function update($id = null)
    {
        $request = $this->request->getPost();
        $updateFC = $this->reservationModel->update_r_api($id, $request);
        if ($updateFC) {
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


    public function check($user_id, $date)
    {
        $isDuplicate = $this->reservationModel->checkIsDateDuplicate($user_id, $date);
        return json_encode($isDuplicate);
    }
    public function checkHomestay($user_id, $date_start, $date_end = null)
    {
        $isDuplicate = $this->reservationModel->checkIsDateHomestayDuplicate($user_id, $date_start, $date_end);
        return json_encode($isDuplicate);
    }
}
