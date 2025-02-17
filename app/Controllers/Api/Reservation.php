<?php

namespace App\Controllers\Api;

use App\Models\HomestayModel;
use App\Models\PackageModel;
use App\Models\ReservationModel;
use App\Models\ReservationStatusModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use Myth\Auth\Models\UserModel;
use CodeIgniter\Files\File;
use CodeIgniter\I18n\Time;

class Reservation extends ResourceController
{
    use ResponseTrait;

    protected $reservationModel;
    protected $reservationStatusModel;
    protected $packageModel;
    protected $homestayModel;
    protected $userModel;
    protected $helpers = ['auth', 'url', 'filesystem'];

    public function __construct()
    {
        $this->reservationModel = new ReservationModel();
        $this->reservationStatusModel = new ReservationStatusModel();
        $this->packageModel = new PackageModel();
        $this->homestayModel = new HomestayModel();
        $this->userModel = new UserModel();
    }

    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $contents = $this->reservationModel->get_list_s_api()->getResult();
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
        $reservationData = $this->reservationModel->get_r_by_id_api($id)->getRowArray();
        // reservation status dan paket
        $reservationData['status'] = $this->reservationStatusModel->get_s_by_id_api($reservationData['id_reservation_status'])->getRowArray()['status'];
        $reservationData['username'] = $this->userModel->get_u_by_id_api($reservationData['id_user'])->getRowArray()['username'];

        if ($reservationData['id_package'] != null) {
            $data = $this->packageModel->get_tp_by_id_api($reservationData['id_package'])->getRowArray();
            $reservationData['item_name']  = $data['name'];
            $reservationData['item_costum'] = $data['costum'];
            $reservationData['item_price'] = $data['price'];
        } else if ($reservationData['id_homestay'] != null) {
            $data = $this->homestayModel->get_hm_by_id_api($reservationData['id_homestay'])->getRowArray();
            $reservationData['item_name']  = $data['name'];
            $reservationData['item_costum'] = '2';
            $reservationData['item_price'] = $data['ticket_price'];
        }

        return json_encode($reservationData);
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
        $request = $this->request->getRawInput();

        $id = $this->reservationModel->get_new_id_api();
        $requestData = [
            'id' => $id,
            'id_user' => $request['id_user'],
            'id_package' => $request['id_package'],
            'id_reservation_status' => $request['id_reservation_status'],
            'request_date' => $request['reservation_date']
        ];

        $addR = $this->reservationModel->add_r_api($requestData);
        if ($addR) {
            $response = [
                'status' => 200,
                'message' => [
                    "Success create new reservation"
                ]
            ];
            return $this->respondCreated($response);
        } else {
            $response = [
                'status' => 400,
                'message' => [
                    "Fail create new reservation",
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
        // execute when booking canceled
        if (isset($request['canceled_at'])) {
            $request['canceled_at'] = Time::now("Asia/Jakarta");
        }

        // execute when booking confirmed
        if (isset($request['confirmed_at'])) {
            $request['confirmed_at'] = Time::now("Asia/Jakarta");
        }
        // execute when payment accepted
        if (isset($request['payment_accepted_date'])) {
            $request['payment_accepted_date'] = Time::now("Asia/Jakarta");
        }

        // execute when refund uploaded
        if (isset($request['refund_date'])) {
            $request['refund_date'] = Time::now("Asia/Jakarta");
        }

        // execute when upload proof of refund
        if (isset($request['proof_of_refund'])) {
            $folder = $request['proof_of_refund'];
            $filepath = WRITEPATH . 'uploads/' . $folder;
            $filename = get_filenames($filepath)[0];
            $fileImg = new File($filepath . '/' . $filename);
            $fileImg->move(FCPATH . 'media/photos/refund/');
            delete_files($filepath);
            rmdir($filepath);
            $request['proof_of_refund'] = $fileImg->getFilename();
            $request['refund_date'] = Time::now("Asia/Jakarta");
        }
        // execute when upload proof of payment
        if (isset($request['proof_of_deposit'])) {
            $folder = $request['proof_of_deposit'];
            $filepath = WRITEPATH . 'uploads/' . $folder;
            $filename = get_filenames($filepath)[0];
            $fileImg = new File($filepath . '/' . $filename);
            $fileImg->move(FCPATH . 'media/photos/reservation/');
            delete_files($filepath);
            rmdir($filepath);
            $request['proof_of_deposit'] = $fileImg->getFilename();
            $request['deposit_date'] = Time::now();
        }
        $updateFC = $this->reservationModel->update_r_api($id, $request);
        if ($updateFC) {
            $response = [
                'status' => 200,
                'message' => [
                    "Success update reservation"
                ]
            ];
            return $this->respondCreated($response);
        } else {
            $response = [
                'status' => 400,
                'message' => [
                    "Fail update reservation",
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
        $delete = $this->reservationModel->delete(['id' => $id]);
        if ($delete) {
            $response = [
                'status' => 200,
                'message' => [
                    "Success delete Reservation"
                ]
            ];
            return $this->respondDeleted($response);
        } else {
            $response = [
                'status' => 404,
                'message' => [
                    "Reservation not found"
                ]
            ];
            return $this->failNotFound('' + $response);
        }
    }
}
