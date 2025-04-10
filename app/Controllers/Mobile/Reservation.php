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

    protected $serverKey;
    public function __construct()
    {
        $this->reservationModel = new ReservationModel();
        $this->reservationStatusModel = new ReservationStatusModel();
        $this->packageModel = new PackageModel();
        $this->homestayModel = new HomestayModel();
        require_once APPPATH . 'Config/Midtrans.php';

        // Set konfigurasi Midtrans
        \Midtrans\Config::$serverKey = env('midtrans.serverKey', "SB-Mid-server-g_hKQ3Ku4LuA2uo2x7YsbfkH");
        \Midtrans\Config::$isProduction = false; // Set true untuk production
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;
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

    public function checkout()
    {
        // Ambil data reservasi dari POST request
        $reservationIds = $this->request->getPost('reservation_ids');

        // Validasi data
        if (empty($reservationIds) || !is_array($reservationIds)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada item yang dipilih untuk checkout'
            ]);
        }

        // Ambil detail reservasi dari database
        $reservationModel = new \App\Models\ReservationModel();
        $reservations = $reservationModel->whereIn('id', $reservationIds)->findAll();

        if (!$reservations) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Reservasi tidak ditemukan'
            ]);
        }

        // Load model yang diperlukan
        $packageModel = new \App\Models\PackageModel();
        $homestayModel = new \App\Models\HomestayModel();

        // Hitung total pembayaran
        $totalAmount = 0;
        $itemDetails = [];

        foreach ($reservations as $reservation) {
            $totalAmount += $reservation['total_price'];

            // Tentukan nama item berdasarkan jenis reservasi
            $itemName = 'Reservasi';

            if (!empty($reservation['id_package'])) {
                // Jika reservasi untuk package
                $package = $packageModel->find($reservation['id_package']);
                if ($package) {
                    $itemName = $package['name'];
                }
            } elseif (!empty($reservation['id_homestay'])) {
                // Jika reservasi untuk homestay
                $homestay = $homestayModel->find($reservation['id_homestay']);
                if ($homestay) {
                    $itemName =  $homestay['name'];
                }
            }

            $itemDetails[] = [
                'id' => $reservation['id'],
                'price' => $reservation['total_price'],
                'quantity' => 1,
                'name' => $itemName
            ];
        }


        // Buat ID transaksi unik dengan timestamp dan random string
        $orderId = 'ORDER-' . time() . '-' . bin2hex(random_bytes(3));

        // Dapatkan data user yang sedang login - menggunakan helper CI4 (Auth library)
        $userName = user()->first_name ?? 'Customer';
        $userEmail = user()->email ?? 'customer@example.com';
        $userPhone = user()->phone ?? '08123456789';
        // Set parameter untuk Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $totalAmount,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' =>  $userName,
                'email' =>    $userEmail,
                'phone' =>   $userPhone,
            ],
        ];

        try {
            // Dapatkan token pembayaran dari Midtrans
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            // Update status reservasi menggunakan model CI4
            $db = db_connect();
            $db->transBegin();
            foreach ($reservations as $reservation) {
                $reservationModel->update_r_api($reservation['id'], [
                    'payment_order_id' => $orderId,

                ]);
            }
            // Commit transaksi jika semua operasi berhasil
            $db->transCommit();
            // Kirim token ke frontend
            return $this->response->setJSON([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $orderId
            ]);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            if (isset($db) && $db->transStatus() === false) {
                $db->transRollback();
            }
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Tambahkan endpoint untuk handle callback dari Midtrans
    public function notification()
    {

        try {
            $notification = new \Midtrans\Notification();

            $orderId = $notification->order_id;
            $status = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status;

            $reservationModel = new \App\Models\ReservationModel();
            $reservations = $reservationModel->where('payment_order_id', $orderId)->findAll();

            if ($reservations) {
                $idReservationStatus = 1; // Default status pending

                // Proses status pembayaran
                if ($status == 'capture') {
                    if ($fraudStatus == 'accept') {
                        $idReservationStatus = 4;
                    }
                } else if ($status == 'settlement') {
                    $idReservationStatus = 4;
                } else if ($status == 'cancel' || $status == 'deny' || $status == 'expire') {
                    $idReservationStatus = 3;
                } else if ($status == 'pending') {
                    $idReservationStatus = 1;
                }

                // Update semua reservasi yang terkait dengan order_id ini
                foreach ($reservations as $reservation) {
                    $reservationModel->update($reservation['id'], [
                        'id_reservation_status' => $idReservationStatus,
                    ]);
                }
            }

            return $this->response->setJSON(['success' => true]);
        } catch (\Exception $e) {
            log_message('error', 'Midtrans notification error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Tambahkan endpoint untuk halaman sukses setelah pembayaran
    public function paymentSuccess()
    {
        $orderId = $this->request->getGet('order_id');

        $reservationModel = new \App\Models\ReservationModel();
        $reservations = $reservationModel->where('payment_order_id', $orderId)->findAll();

        return view('mobile/payment_success', [
            'reservations' => $reservations,
            'order_id' => $orderId
        ]);
    }
}
