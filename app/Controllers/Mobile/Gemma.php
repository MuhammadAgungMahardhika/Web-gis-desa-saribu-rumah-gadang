<?php

namespace App\Controllers\Mobile;

use App\Models\HomestayModel;
use App\Models\PackageModel;
use App\Models\ReservationModel;
use App\Models\RumahGadangModel;
use CodeIgniter\RESTful\ResourcePresenter;
use Exception;
use stdClass;

class Gemma extends ResourcePresenter
{
    protected $currentUrl;
    protected $helpers = ['auth', 'url', 'filesystem'];
    protected $apiKey = "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";
    protected $apiUrl = "https://api.groq.com/openai/v1/chat/completions";

    protected $modelRumahGadang;
    protected $modelPackage;
    protected $modelReservation;

    public function __construct()
    {
        $this->currentUrl = 'mobile';
        $this->modelRumahGadang = new RumahGadangModel();
        $this->modelPackage = new PackageModel();
        $this->modelReservation = new ReservationModel();
    }

    // Menampilkan halaman utama
    public function index()
    {
        $data = [
            'title' => 'Gemini AI',
            'currentUrl' => $this->currentUrl
        ];
        return view('mobile/gemma', $data);
    }

    // Reset history percakapan
    public function resetChat()
    {
        session()->remove('chat_history');
        return $this->response->setJSON(['message' => 'Chat history cleared']);
    }

    // Fungsi utama untuk memproses permintaan pengguna
    public function processRequest()
    {
        try {
            $message = $this->request->getPost('message');
            if (!$message) {
                return $this->response->setJSON(['error' => 'Message is required'])->setStatusCode(400);
            }

            // Ambil history chat
            $history = session()->get('chat_history') ?? [];
            if (count($history) > 5) {
                $history = array_slice($history, -5);
            }
            $history[] = ["role" => "user", "content" => $message];

            // Persiapkan data untuk dikirim ke AI
            $model =  "gemma2-9b-it";
            $message = array_merge(
                [
                    [
                        "role" => "system",
                        "content" => "You are an AI providing information about the Saribu Tourism Village Application.
                        Use the following functions as needed, do not run one of these if user not asking:
                        - `getWeather` only if the user asks about the weather.
                        - `getRumahGadang` only if the user asks about the list of Rumah Gadang.
                        - `getPaketWisata` only if the user asks about tour packages.
                        - `makePackageReservationAi` only if the user requests to book a specific tour package.
                        - `makeHomestayReservationAi` only if the user requests to book a specific Rumah Gadang (homestay)."
                    ]
                ],
                $history
            );
            $tools = [
                [
                    "type" => "function",
                    "function" => [
                        "name" => "get_weather",
                        "description" => "Only when user asked,Retrieve weather information for Saribu Rumah Gadang Tourism Village.",
                        "parameters" => [
                            "type" => "object",
                            "properties" => [
                                "location" => [
                                    "type" => "string",
                                    "description" => "Fixed location name to get weather information.",
                                    "enum" => ["Saribu Rumah Gadang Tourism Village"]
                                ]
                            ],
                            "required" => ["location"]
                        ]
                    ]
                ],
                [
                    "type" => "function",
                    "function" => [
                        "name" => "get_rumah_gadang",
                        "description" => "Only when user asked,Retrieve a list of Rumah Gadang available in Saribu Rumah Gadang Tourism Village.",
                        "parameters" => [
                            "type" => "object",
                            "properties" => [
                                "homestay" => [
                                    "type" => "boolean",
                                    "description" => "If true, only display Rumah Gadang that function as homestays."
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    "type" => "function",
                    "function" => [
                        "name" => "get_paket_wisata",
                        "description" => "Only when user asked,Retrieve a list of available tour packages in Saribu Rumah Gadang Tourism Village.",
                        "parameters" => [
                            "type" => "object",
                            "properties" => new stdClass()
                        ]
                    ]
                ],
                [
                    "type" => "function",
                    "function" => [
                        "name" => "make_package_reservation_ai",
                        "description" => "Make a tour package reservation in Saribu Rumah Gadang Tourism Village for a logged-in user.",
                        "parameters" => [
                            "type" => "object",
                            "properties" => [
                                "packageId" => [
                                    "type" => "string",
                                    "description" => "ID of the tour package to be booked. Leave empty to search by package name."
                                ],
                                "packageName" => [
                                    "type" => "string",
                                    "description" => "Name of the tour package to be booked. If 'packageId' is provided, this parameter can be omitted."
                                ],
                                "requestDate" => [
                                    "type" => "string",
                                    "format" => "date",
                                    "description" => "Reservation date in YYYY-MM-DD format."
                                ],
                                "numberPeople" => [
                                    "type" => "integer",
                                    "description" => "Number of people included in the reservation."
                                ],
                            ],
                            "required" => ["requestDate", "numberPeople"]
                        ]
                    ]
                ],
                [
                    "type" => "function",
                    "function" => [
                        "name" => "remove_package_reservation_ai",
                        "description" => "Remove or abort a tour package reservation in Saribu Rumah Gadang Tourism Village for a logged-in user.",
                        "parameters" => [
                            "type" => "object",
                            "properties" => [
                                "reservationId" => [
                                    "type" => "string",
                                    "description" => "ID of the reservation"
                                ],
                            ],
                            "required" => ["reservationId"]
                        ]
                    ]
                ],
                [
                    "type" => "function",
                    "function" => [
                        "name" => "make_homestay_reservation_ai",
                        "description" => "Make a homestay reservation in Saribu Rumah Gadang Tourism Village for a logged-in user.",
                        "parameters" => [
                            "type" => "object",
                            "properties" => [
                                "homestayId" => [
                                    "type" => "string",
                                    "description" => "ID of the homestay to be booked."
                                ],
                                "requestDate" => [
                                    "type" => "string",
                                    "format" => "date",
                                    "description" => "Start date of reservation in YYYY-MM-DD format."
                                ],
                                "requestDateEnd" => [
                                    "type" => "string",
                                    "format" => "date",
                                    "description" => "End date of reservation in YYYY-MM-DD format."
                                ],
                                "numberPeople" => [
                                    "type" => "integer",
                                    "description" => "Number of people included in the reservation."
                                ],
                            ],
                            "required" => ["homestay_id", "requestDate", "numberPeople"]
                        ]
                    ]
                ]
            ];

            $data = [
                "model" => $model,
                "messages" => $message,
                "max_tokens" => 300,
                "temperature" => 0.9,
                "top_p" => 1,
                "tools" => $tools,
                "tool_choice" => "auto",
                "max_completion_tokens" => 4096,

            ];

            // Kirim request ke API
            $ch = curl_init($this->apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer " . $this->apiKey,
                "Content-Type: application/json"
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            $response = curl_exec($ch);
            curl_close($ch);

            $responseData = json_decode($response, true);

            // jika ada function call
            if (isset($responseData['choices'][0]['message']['tool_calls'])) {
                foreach ($responseData['choices'][0]['message']['tool_calls'] as $toolCall) {
                    $functionName = $toolCall['function']['name'];
                    $arguments = json_decode($toolCall['function']['arguments'], true);
                    return $this->handleFunctionCall($functionName, $arguments);
                }
            }

            // Jika tidak ada function call, lanjutkan percakapan biasa
            $aiResponse = $responseData['choices'][0]['message']['content'] ?? 'Maaf, terjadi kesalahan.';
            $history[] = ["role" => "assistant", "content" => $aiResponse];

            session()->set('chat_history', $history);
            return $this->response->setJSON(['response' => $aiResponse]);
        } catch (Exception $e) {
            return $this->response->setJSON(['response' => $e->getMessage()]);
        }
    }

    // 🔥 PERBAIKAN: Fungsi untuk menangani function call dengan parameter
    private function handleFunctionCall($functionName, $arguments)
    {
        switch ($functionName) {
            case "get_weather":
                return $this->getWeather(); // Tidak perlu argumen karena lokasinya tetap

            case "get_rumah_gadang":
                $homestay = isset($arguments['homestay']) ? ($arguments['homestay'] ? "true" : "false") : null;
                return $this->getRumahGadang($homestay);

            case "get_paket_wisata":
                return $this->getPaketWisata();

            case "make_package_reservation_ai":
                // Jika packageId tidak ada, cari berdasarkan nama paket
                if (empty($arguments['packageId']) && !empty($arguments['packageName'])) {
                    $packageName = $arguments['packageName'];

                    $package = $this->modelPackage
                        ->where("SOUNDEX(name)", soundex($packageName))
                        ->orLike("name", $packageName, 'both') // Tambahan pencocokan kasar
                        ->first();

                    if (!$package || empty($package['id'])) {
                        return $this->response->setJSON([
                            "response" => "Paket wisata '{$packageName}' tidak ditemukan."
                        ]);
                    }

                    $arguments['packageId'] = $package['id'];
                }

                if (!isset($arguments['requestDate'])) {
                    return $this->response->setJSON(["response" => "Tanggal berapa anda ingin reservasi paket?"]);
                }
                if (!isset($arguments['numberPeople'])) {
                    return $this->response->setJSON(["response" => "Berapa orang yang akan ikut?"]);
                }

                // Ambil parameter dengan nilai default jika tidak disertakan
                $package_id = $arguments['packageId'];
                $requestDate = $arguments['requestDate'];
                $numberPeople = (int) $arguments['numberPeople'];

                return $this->makePackageReservationAI($package_id, $requestDate, $numberPeople);

            case "remove_package_reservation_ai":
                if (!isset($arguments['reservationId'])) {
                    return $this->response->setJSON(["response" => "tidak ada kode reservasi"]);
                }
                $reservationId = $arguments['reservationId'];
                return $this->removePackageReservationAI($reservationId);
            case "make_homestay_reservation_ai":
                // Pastikan semua parameter yang diperlukan ada
                if (!isset($arguments['homestayId'], $arguments['requestDate'], $arguments['numberPeople'])) {
                    return $this->response->setJSON(["error" => "Parameter tidak lengkap untuk reservasi."]);
                }

                // Ambil parameter dengan nilai default jika tidak disertakan
                $homestayId =  $arguments['homestayId'];
                $requestDate = $arguments['requestDate'];
                $numberPeople = (int) $arguments['numberPeople'];

                return $this->makeHomestayReservationAI($homestayId, $requestDate, $numberPeople);

            default:
                return $this->response->setJSON(['error' => "Function '$functionName' not recognized"]);
        }
    }



    // Fungsi untuk mendapatkan cuaca
    public function getWeather()
    {
        $apiKey = "2390a9743ed947a7ab68238ae3039af1";
        $lat = "-1.4815029";
        $lng = "101.0574556";
        $apiUrl = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lng}&appid={$apiKey}&lang=id&units=metric";

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        if (!$data || isset($data['cod']) && $data['cod'] != 200) {
            return $this->response->setJSON(['error' => 'Gagal mendapatkan data cuaca.']);
        }

        $weatherDescription = ucfirst($data['weather'][0]['description']);
        $temperature = $data['main']['temp'];


        return $this->response->setJSON([
            "response" => "Saat ini cuaca di Desa Wisata Saribu Rumah Gadang adalah <b>{$weatherDescription}</b>, dengan suhu sekitar **<b>{$temperature}°C</b> <br>" . "Selalu berhati2 diperjalanan!"
        ]);
    }

    // Fungsi untuk mendapatkan daftar Rumah Gadang
    public function getRumahGadang($homestay = null)
    {
        try {
            // Jika homestay bernilai "true", ambil daftar rumah gadang dengan homestay
            if ($homestay === "true") {
                // Format teks untuk output
                $responseText = "**Berikut adalah daftar Homestay Rumah Gadang di Desa Wisata Saribu Rumah Gadang:**<br>";
                $data = $this->modelRumahGadang->get_list_hm_api()->getResultArray();
            } else {
                // Format teks untuk output
                $responseText = "**Berikut adalah daftar Rumah Gadang  di Desa Wisata Saribu Rumah Gadang:**<br>";
                $data = $this->modelRumahGadang->get_list_rg_api()->getResultArray();
            }

            // Jika tidak ada data, kembalikan respons kosong
            if (empty($data)) {
                throw new Exception("Saat ini tidak ada Rumah Gadang yang tersedia di Desa Wisata Saribu Rumah Gadang.");
            }

            foreach ($data as $index => $rumah) {
                $price = !empty($rumah['ticket_price'])
                    ? "Harga: Rp " . number_format($rumah['ticket_price'], 0, ',', '.') . "/ malam"
                    : "";
                $responseText .= ($index + 1) .  ". " . "(" . strip_tags($rumah['id']) . ") <b>"  . strip_tags($rumah['name']) . "</b><br>" . $price . "<br>";
            }

            return $this->response->setJSON(["response" => $responseText]);
        } catch (Exception $e) {
            return $this->response->setJSON(["response" => $e->getMessage()]);
        }
    }


    // Fungsi untuk mendapatkan daftar Paket Wisata
    public function getPaketWisata()
    {
        try {
            $data = $this->modelPackage->get_list_tp_api()->getResultArray();

            if (empty($data)) {
                throw new Exception("Saat ini belum ada paket wisata yang tersedia di Desa Wisata Saribu Rumah Gadang.");
            }

            $responseText = "<b>Berikut adalah daftar Paket Wisata yang tersedia di Desa Wisata Saribu Rumah Gadang:</b><br><br>";

            foreach ($data as $index => $paket) {
                $hargaFormatted = number_format($paket['price'], 0, ',', '.');
                $capacity = $paket['capacity'];
                $description =  $paket['description'] ? "Keterangan : {$paket['description']} <br>" : null;
                $responseText .= ($index + 1) .  ". " . "(" . htmlspecialchars($paket['id']) . ") " . "<b>" . htmlspecialchars($paket['name']) . "</b> - Harga: Rp {$hargaFormatted}, Kapasitas : {$capacity} orang<br>{$description}";
            }

            return $this->response->setJSON(["response" => $responseText]);
        } catch (Exception $e) {
            return $this->response->setJSON(["response" => $e->getMessage()]);
        }
    }

    // 🔥 Fungsi untuk menangani reservasi AI utk paket
    public function makePackageReservationAI($package_id, $requestDate, $numberPeople)
    {
        try {
            if (!logged_in()) {
                throw new Exception("Mohon login untuk memesan paket");
            }
            $user_id = user()->id;

            // Dapatkan data paket wisata
            $package = $this->modelPackage->find($package_id);

            if (!$package) {
                throw new Exception("Paket wisata tidak ditemukan.");
            }

            $capacity = $package['capacity'];
            $price = $package['price'];

            // Cek apakah jumlah orang melebihi kapasitas
            if ($numberPeople <= 0) {
                throw new Exception("Tentukan berapa orang yang ikut, Minimal 1 orang untuk reservasi.");
            }

            if ($numberPeople > $capacity) {
                throw new Exception("Kapasitas maksimal paket adalah {$capacity} orang.");
            }

            // Cek apakah tanggal reservasi valid (H-1 minimal)
            $today = date('Y-m-d');
            if ($requestDate <= $today) {
                throw new Exception("Tanggal reservasi harus minimal H-1 dari hari ini.");
            }

            // Cek apakah user sudah reservasi di tanggal yang sama
            $existingReservation = $this->modelReservation
                ->where('id_user', $user_id)
                ->where('id_package', $package_id)
                ->where('request_date', $requestDate)
                ->first();

            if ($existingReservation) {
                throw new Exception("Anda sudah reservasi untuk paket dan tanggal yang sama.");
            }

            // Simpan reservasi baru
            $id =  $this->modelReservation->get_new_id_api();
            $reservationData = [
                'id' =>  $id,
                'id_user' => $user_id,
                'id_package' => $package_id,
                'request_date' => $requestDate,
                'id_reservation_status' => 1, // pending status
                'number_people' => $numberPeople,
                'total_price' => $numberPeople * $price,
            ];

            $this->modelReservation->add_r_api($reservationData);

            $reservationData = $this->modelReservation->find($id);
            $reservationPeople = $reservationData['number_people'];
            $reservationTotalPrice  =  number_format($reservationData['total_price'], 0, ',', '.');
            return $this->response->setJSON(["response" => "Reservasi berhasil dibuat.<br><b><u>{$reservationData['id']}-{$package['name']}-{$reservationPeople} orang - tanggal {$requestDate} - total harga {$reservationTotalPrice} </u></b>.<br> Silahkan melakukan pembayaran!"]);
        } catch (Exception $e) {
            log_message('error', 'Exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return $this->response->setJSON(["response" => $e->getMessage()]);
        }
    }

    public function removePackageReservationAI($reservationId)
    {
        try {
            if (!logged_in()) {
                throw new Exception("Mohon login untuk membatalkan paket");
            }
            $user_id = user()->id;

            // Dapatkan data paket wisata
            $reservation = $this->modelReservation
                ->where('id_user', $user_id)
                ->where('id', $reservationId)
                ->first();
            if (!$reservation) {
                throw new Exception("Reservasi tidak ditemukan.");
            }
            if ($reservation['id_reservation_status'] != 1) {
                throw new Exception("Reservasi tidak dapat dibatalkan!.");
            }
            $this->modelReservation->delete($reservationId);
            // remove reservation
            return  $this->response->setJSON(["response" => "Berhasil membatalkan reservasi <b><u>{$reservationId}</u></b>"]);
        } catch (Exception $e) {
            return $this->response->setJSON(["response" => $e->getMessage()]);
        }
    }

    // 🔥 Fungsi untuk menangani reservasi AI utk homestay
    public function makeHomestayReservationAI($homestay_id, $requestDate, $numberPeople)
    {
        try {
            if (!logged_in()) {
                throw new Exception("Mohon login untuk memesan paket");
            }

            $user_id = user()->id;

            // Dapatkan data homestay
            $homestay = $this->modelRumahGadang->where('id_homestay', $homestay_id);

            if (!$homestay) {
                throw new Exception("Homestay tidak ditemukan.");
            }

            $capacity = $homestay['capacity'];
            $price = $homestay['price'];

            // Cek apakah jumlah orang melebihi kapasitas
            if ($numberPeople <= 0) {
                throw new Exception("Minimal 1 orang untuk reservasi.");
            }

            if ($numberPeople > $capacity) {
                throw new Exception("Kapasitas maksimal adalah {$capacity} orang.");
            }

            // Cek apakah tanggal reservasi valid (H-1 minimal)
            $today = date('Y-m-d');
            if ($requestDate <= $today) {
                throw new Exception("Tanggal reservasi harus minimal H-1 dari hari ini.");
            }

            // Cek apakah user sudah reservasi di tanggal yang sama
            $existingReservation = $this->modelReservation
                ->where('id_user', $user_id)
                ->where('request_date', $requestDate)
                ->first();

            if ($existingReservation) {
                throw new Exception("Anda sudah memiliki reservasi pada tanggal yang sama.");
            }

            // Simpan reservasi baru
            $reservationData = [
                'id' =>   $this->modelReservation->get_new_id_api(),
                'id_user' => $user_id,
                'id_homestay' => $homestay_id,
                'request_date' => $requestDate,
                'id_reservation_status' => 1, // pending status
                'number_people' => $numberPeople,
                'total_price' => $numberPeople * $price,
            ];

            $this->modelReservation->add_r_api($reservationData);

            return $this->response->setJSON(["response" => "Reservasi berhasil dibuat untuk paket : {$homestay['name']}, tanggal {$requestDate}. Silahkan melakukan pembayaran!"]);
        } catch (Exception $e) {
            return $this->response->setJSON(["response" => $e->getMessage()]);
        }
    }
}
