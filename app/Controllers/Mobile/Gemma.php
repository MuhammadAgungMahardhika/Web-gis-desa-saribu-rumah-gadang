<?php

namespace App\Controllers\Mobile;

use App\Models\HomestayModel;
use App\Models\PackageModel;
use App\Models\ReservationModel;
use App\Models\RumahGadangModel;
use CodeIgniter\RESTful\ResourcePresenter;
use DateTime;
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

    protected $userId = null;
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
            $this->userId = $this->request->getPost('userId');
            if (!$message) {
                return $this->response->setJSON(['error' => 'Message is required'])->setStatusCode(400);
            }
            if (!$this->userId) {
                return $this->response->setJSON(['error' => 'User is required'])->setStatusCode(400);
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
                        "content" => "Anda adalah asisten AI 'Gemma' yang membantu pengunjung Desa Wisata Saribu Rumah Gadang dengan informasi dan layanan pemesanan.
                
                PANDUAN UTAMA:
                1. JANGAN JALANKAN FUNGSI kecuali pengguna benar-benar memintanya
                2. Selalu gunakan bahasa Indonesia yang ramah dan santun
                3. Berikan respon singkat, padat, dan membantu
                4. Pastikan semua data yang diperlukan sudah didapat sebelum menjalankan fungsi
                
                KEMAMPUAN ANDA:
                - Memberikan informasi tentang cuaca di Desa Wisata Saribu Rumah Gadang
                - Menampilkan daftar Rumah Gadang dan homestay yang tersedia
                - Menampilkan daftar paket wisata yang tersedia
                - Membantu pemesanan paket wisata dan homestay
                - Menampilkan riwayat pemesanan pengguna
                - Membatalkan pemesanan yang masih berstatus pending
                
                FUNGSI YANG TERSEDIA:
                - `getWeather` - informasi cuaca terkini
                - `getRumahGadang` - daftar Rumah Gadang (atur parameter homestay=true untuk melihat yang bisa dipesan)
                - `getPaketWisata` - daftar paket wisata tersedia
                - `getReservation` - riwayat pemesanan pengguna
                - `makePackageReservationAi` - pemesanan paket wisata
                - `makeHomestayReservationAi` - pemesanan homestay/penginapan
                - `removePackageReservationAi` - pembatalan reservasi
                
                KATA KUNCI YANG HARUS DIKENALI:
                
                1. Untuk cuaca:
                   - 'cuaca', 'hujan', 'panas', 'mendung', 'suhu', 'cuaca hari ini'
                
                2. Untuk informasi Rumah Gadang & homestay:
                   - 'rumah gadang', 'bangunan', 'arsitektur', 'homestay', 'penginapan', 'menginap'
                   - 'daftar rumah', 'list homestay', 'ada homestay apa saja', 'rumah adat'
                
                3. Untuk paket wisata:
                   - 'paket wisata', 'tur', 'wisata', 'jalan-jalan', 'liburan', 'paket tour'
                   - 'daftar paket', 'pilihan paket', 'ada paket apa saja', 'list paket'
                
                4. Untuk pemesanan paket wisata (JALANKAN makePackageReservationAi):
                   - 'pesan paket', 'booking paket', 'reservasi paket', 'order paket' 
                   - 'beli paket', 'ambil paket', 'mau ikut paket', 'gabung paket'
                   - 'saya ingin memesan paket', 'tolong pesankan paket', 'booking wisata'
                
                5. Untuk pemesanan homestay (JALANKAN makeHomestayReservationAi):
                   - 'pesan homestay', 'booking homestay', 'pesan penginapan', 'sewa rumah'
                   - 'ingin menginap di', 'cari kamar', 'reservasi homestay', 'booking penginapan'
                   - 'mau tidur di', 'sewa kamar', 'bermalam di', 'ingin booking rumah'
                
                6. Untuk melihat reservasi (JALANKAN getReservation):
                   - 'lihat pesanan', 'cek reservasi', 'lihat booking', 'pesanan saya'
                   - 'ada reservasi apa', 'booking saya', 'lihat tiket', 'cek pesanan'
                   - 'status pesanan', 'daftar reservasi', 'riwayat pemesanan'
                
                7. Untuk pembatalan (JALANKAN removePackageReservationAi):
                   - 'batalkan pesanan', 'cancel booking', 'hapus reservasi', 'batal pesan'
                   - 'tidak jadi pesan', 'batalkan tiket', 'cancel order', 'cancel reservasi'
                
                FORMAT TANGGAL YANG HARUS DIKENALI:
                - '15 Mei 2025', '15-05-2025', '15/05/2025', '2025-05-15'
                - 'besok', 'lusa', 'minggu depan', 'bulan depan', 'akhir bulan'
                - 'Senin depan', 'Jumat minggu depan', dll
                
                PANDUAN PEMESANAN:
                
                1. Untuk pemesanan paket, pastikan mendapatkan:
                   - Nama paket atau ID paket yang jelas
                   - Jumlah peserta (minimal 1 orang)
                   - Tanggal kunjungan yang valid (minimal H-1)
                
                2. Untuk pemesanan homestay, pastikan mendapatkan:
                   - Nama homestay atau ID homestay yang jelas
                   - Jumlah tamu (minimal 1 orang)
                   - Tanggal check-in (minimal H-1)
                   - Tanggal check-out (setelah tanggal check-in)
                
                CONTOH DIALOG:
                
                Pengguna: 'Mau pesan paket wisata Budaya'
                Anda: 'Untuk pemesanan paket Wisata Budaya, mohon beritahu jumlah peserta dan tanggal kunjungan yang diinginkan.'
                
                Pengguna: 'Untuk 4 orang, tanggal 15 Mei'
                Anda: [Jalankan fungsi makePackageReservationAi dengan parameter yang sesuai]"

                    ],
                    $history
                ]
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
                        "name" => "get_reservation",
                        "description" => "Only when user asked,Retrieve reservation history of logged user.",
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
                        "name" => "make_homestay_reservation_ai",
                        "description" => "Make a rumah gadang / homestay reservation in Saribu Rumah Gadang Tourism Village for a logged-in user.",
                        "parameters" => [
                            "type" => "object",
                            "properties" => [
                                "rumahGadangId" => [
                                    "type" => "string",
                                    "description" => "ID of the rumah gadang to be booked."
                                ],
                                "rumahGadangOrHomestayName" => [
                                    "type" => "string",
                                    "description" => "Name of the rumah gadang / homestay to be booked. If 'rumah gadang id ' is provided, this parameter can be omitted."
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
                            "required" => ["requestDate", "requestDateEnd", "numberPeople"]
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
            log_message('error', 'Exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
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
            case "get_reservation":
                return $this->getReservation();
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
                // Jika rumahGadangId tidak ada, cari berdasarkan nama paket
                if (empty($arguments['rumahGadangId']) && !empty($arguments['rumahGadangOrHomestayName'])) {
                    $rumahGadangOrHomestayName = $arguments['rumahGadangOrHomestayName'];
                    $rumahGadang = $this->modelRumahGadang
                        ->where('id_homestay IS NOT NULL', null, false)
                        ->where("SOUNDEX(name)", soundex($rumahGadangOrHomestayName))
                        ->orLike("name", $rumahGadangOrHomestayName, 'both')
                        ->first();
                    if (!$rumahGadang || empty($rumahGadang['id_homestay'])) {

                        return $this->response->setJSON([
                            "response" => "Homestay '{$rumahGadangOrHomestayName}' tidak ditemukan."
                        ]);
                    }
                    $arguments['rumahGadangId'] = $rumahGadang['id'];
                }
                // Pastikan semua parameter yang diperlukan ada
                if (!isset($arguments['rumahGadangId'], $arguments['requestDate'], $arguments['requestDateEnd'], $arguments['numberPeople'])) {
                    return $this->response->setJSON(["response" => "Parameter tidak lengkap untuk reservasi."]);
                }

                // Ambil parameter dengan nilai default jika tidak disertakan
                $rumahGadangId =  $arguments['rumahGadangId'];
                $requestDate = $arguments['requestDate'];
                $requestDateEnd = $arguments['requestDateEnd'];
                $numberPeople = (int) $arguments['numberPeople'];
                return $this->makeHomestayReservationAI($rumahGadangId, $requestDate, $requestDateEnd, $numberPeople);
            default:
                return $this->response->setJSON(['error' => "Function '$functionName' not recognized"]);
        }
    }



    // Fungsi untuk mendapatkan cuaca
    public function getWeather()
    {
        try {
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
        } catch (Exception $e) {
            log_message('error', 'Exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return $this->response->setJSON([
                "response" => $e->getMessage()
            ]);
        }
    }
    // Fungsi untuk mendapatkan daftar Rumah Gadang
    public function getRumahGadang($homestay = null)
    {
        try {
            // Jika homestay bernilai "true", ambil daftar rumah gadang dengan homestay
            if ($homestay === "true") {
                $data = $this->modelRumahGadang->get_list_hm_api()->getResultArray();

                if (empty($data)) {
                    throw new Exception("Saat ini tidak ada homestay yang tersedia di Desa Wisata Saribu Rumah Gadang.");
                }

                $responseText = "<div class='homestay-list'>";
                $responseText .= "<h3>🏠 Homestay Tersedia</h3>";
                $responseText .= "<p>Berikut daftar homestay yang bisa Anda pesan di Desa Wisata Saribu Rumah Gadang:</p>";

                foreach ($data as $index => $rumah) {
                    $price = !empty($rumah['ticket_price'])
                        ? number_format($rumah['ticket_price'], 0, ',', '.')
                        : "Hubungi pengelola";

                    $responseText .= "<div class='homestay-item mb-3'>";
                    $responseText .= "<h4>🏡 " . strip_tags($rumah['name']) . "</h4>";
                    $responseText .= "<ul style='list-style-type: none; padding-left: 10px;'>";
                    $responseText .= "<li>💰 <b>Harga per malam:</b> Rp {$price}</li>";
                    if (!empty($rumah['address'])) {
                        $responseText .= "<li>📍 <b>Alamat:</b> " . strip_tags($rumah['address']) . "</li>";
                    }
                    $responseText .= "</ul>";
                    $responseText .= "</div>";
                }

                $responseText .= "<div class='booking-guide'>";
                $responseText .= "<h4>Cara Pemesanan Homestay</h4>";
                $responseText .= "<p>Untuk memesan homestay, Anda dapat ketik:</p>";
                $responseText .= "<div class='example text-success'>";
                $responseText .= "\"Saya ingin memesan homestay [nama homestay] untuk [jumlah] orang check-in tanggal [tanggal-bulan-tahun] dan check-out tanggal [tanggal-bulan-tahun]\"";
                $responseText .= "</div>";
                $responseText .= "<p>Contoh:</p>";
                $responseText .= "<div class='example text-success'>";
                $responseText .= "\"Booking homestay Rumah Gadang Sianok untuk 2 orang dari tanggal 20 Mei 2025 sampai 22 Mei 2025\"";
                $responseText .= "</div>";
                $responseText .= "</div>";
                $responseText .= "</div>";
            } else {
                // Format teks untuk output daftar rumah gadang (bukan homestay)
                $data = $this->modelRumahGadang->get_list_rg_api()->getResultArray();

                if (empty($data)) {
                    throw new Exception("Saat ini tidak ada Rumah Gadang yang tersedia di Desa Wisata Saribu Rumah Gadang.");
                }

                $responseText = "<div class='rumah-gadang-list'>";
                $responseText .= "<h3>🏛️ Rumah Gadang Tersedia</h3>";
                $responseText .= "<p>Berikut daftar Rumah Gadang yang bisa Anda kunjungi di Desa Wisata Saribu Rumah Gadang:</p>";

                foreach ($data as $index => $rumah) {
                    $responseText .= "<div class='rumah-gadang-item mb-3'>";
                    $responseText .= "<h4>🏛️ " . strip_tags($rumah['name']) . "</h4>";
                    $responseText .= "<ul style='list-style-type: none; padding-left: 10px;'>";
                    if (!empty($rumah['address'])) {
                        $responseText .= "<li>📍 <b>Alamat:</b> " . strip_tags($rumah['address']) . "</li>";
                    }
                    $responseText .= "</ul>";
                    $responseText .= "</div>";
                }

                $responseText .= "</div>";
            }

            return $this->response->setJSON(["response" => $responseText]);
        } catch (Exception $e) {
            log_message('error', 'Exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
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

            $responseText = "<div class='package-list'>";
            $responseText .= "<h3>📋 Paket Wisata Tersedia</h3>";
            $responseText .= "<p>Berikut daftar paket wisata yang bisa Anda nikmati di Desa Wisata Saribu Rumah Gadang:</p>";

            foreach ($data as $index => $paket) {
                $hargaFormatted = number_format($paket['price'], 0, ',', '.');
                $capacity = $paket['capacity'];
                $description = $paket['description'] ? "<span class='text-muted'>{$paket['description']}</span>" : "";

                $responseText .= "<div class='package-item mb-3'>";
                $responseText .= "<h4>🎫 " . htmlspecialchars($paket['name']) . "</h4>";
                $responseText .= "<ul style='list-style-type: none; padding-left: 10px;'>";
                $responseText .= "<li>💰 <b>Harga:</b> Rp {$hargaFormatted}</li>";
                $responseText .= "<li>👥 <b>Kapasitas:</b> {$capacity} orang</li>";
                if ($description) {
                    $responseText .= "<li>📝 <b>Detail:</b> {$description}</li>";
                }
                $responseText .= "</ul>";
                $responseText .= "</div>";
            }

            $responseText .= "<div class='booking-guide'>";
            $responseText .= "<h4>Cara Pemesanan</h4>";
            $responseText .= "<p>Untuk memesan paket wisata, Anda dapat ketik:</p>";
            $responseText .= "<div class='example text-success'>";
            $responseText .= "\"Saya ingin memesan paket [nama paket] untuk [jumlah] orang pada tanggal [tanggal-bulan-tahun]\"";
            $responseText .= "</div>";
            $responseText .= "<p>Contoh:</p>";
            $responseText .= "<div class='example text-success'>";
            $responseText .= "\"Pesan paket Wisata Budaya untuk 4 orang tanggal 15 Mei 2025\"";
            $responseText .= "</div>";
            $responseText .= "</div>";
            $responseText .= "</div>";

            return $this->response->setJSON(["response" => $responseText]);
        } catch (Exception $e) {
            log_message('error', 'Exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return $this->response->setJSON(["response" => $e->getMessage()]);
        }
    }

    // 🔥 Fungsi untuk menangani reservasi AI utk paket
    public function makePackageReservationAI($package_id, $requestDate, $numberPeople)
    {
        try {
            if (!$this->userId) {
                throw new Exception("Mohon login terlebih dahulu untuk memesan paket wisata");
            }
            $user_id = $this->userId;

            // Format the date if it's in DD-MM-YYYY format
            if (preg_match("/^\d{1,2}-\d{1,2}-\d{4}$/", $requestDate)) {
                $dateParts = explode('-', $requestDate);
                $requestDate = "{$dateParts[2]}-{$dateParts[1]}-{$dateParts[0]}";
            }

            // Dapatkan data paket wisata
            $package = $this->modelPackage->find($package_id);

            if (!$package) {
                throw new Exception("Paket wisata tidak ditemukan. Silahkan cek daftar paket yang tersedia.");
            }

            $capacity = $package['capacity'];
            $price = $package['price'];

            // Cek apakah jumlah orang valid
            if ($numberPeople <= 0) {
                throw new Exception("Jumlah peserta harus minimal 1 orang.");
            }

            if ($numberPeople > $capacity) {
                throw new Exception("Jumlah peserta melebihi kapasitas maksimal. Kapasitas paket {$package['name']} adalah {$capacity} orang.");
            }

            // Validate date format
            $dateObj = DateTime::createFromFormat('Y-m-d', $requestDate);
            if (!$dateObj || $dateObj->format('Y-m-d') !== $requestDate) {
                throw new Exception("Format tanggal tidak valid. Gunakan format YYYY-MM-DD (contoh: 2025-05-20).");
            }

            // Cek apakah tanggal reservasi valid (H-1 minimal)
            $today = date('Y-m-d');
            if ($requestDate <= $today) {
                throw new Exception("Tanggal reservasi harus minimal H-1 dari hari ini. Silahkan pilih tanggal setelah {$today}.");
            }

            // Cek apakah user sudah reservasi di tanggal yang sama
            $existingReservation = $this->modelReservation
                ->where('id_user', $user_id)
                ->where('id_package', $package_id)
                ->where('request_date', $requestDate)
                ->first();

            if ($existingReservation) {
                throw new Exception("Anda sudah memiliki reservasi untuk paket {$package['name']} pada tanggal {$requestDate}.");
            }

            // Simpan reservasi baru
            $id = $this->modelReservation->get_new_id_api();
            $total_price = $numberPeople * $price;
            $reservationData = [
                'id' => $id,
                'id_user' => $user_id,
                'id_package' => $package_id,
                'request_date' => $requestDate,
                'id_reservation_status' => 1, // pending status
                'number_people' => $numberPeople,
                'total_price' => $total_price,
            ];

            $this->modelReservation->add_r_api($reservationData);

            // Format tanggal untuk response
            $formattedDate = date('d F Y', strtotime($requestDate));
            $totalPriceFormatted = number_format($total_price, 0, ',', '.');

            return $this->response->setJSON([
                "response" => "<span class='text-success'>✅ Reservasi berhasil dibuat!</span><br><br>" .
                    "📋 <b>Detail Reservasi:</b><br>" .
                    "🔖 Kode Booking: <b>{$id}</b><br>" .
                    "🎫 Paket: <b>{$package['name']}</b><br>" .
                    "👥 Jumlah Peserta: <b>{$numberPeople} orang</b><br>" .
                    "📅 Tanggal: <b>{$formattedDate}</b><br>" .
                    "💰 Total Harga: <b>Rp {$totalPriceFormatted}</b><br><br>" .
                    "Silahkan lakukan pembayaran sesuai petunjuk yang akan dikirimkan ke email Anda.<br>" .
                    "Untuk melihat reservasi Anda, ketik <b>'Lihat reservasi saya'</b>."
            ]);
        } catch (Exception $e) {
            log_message('error', 'Exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return $this->response->setJSON(["response" => "❌ " . $e->getMessage()]);
        }
    }

    public function getReservation()
    {
        try {
            $userId = $this->userId;

            if (!$userId) {
                throw new Exception("Mohon login terlebih dahulu untuk melihat reservasi Anda.");
            }

            $reservations = $this->modelReservation->get_r_by_id_user_api($userId)->getResultArray();

            if (empty($reservations)) {
                throw new Exception("Anda belum memiliki reservasi. Anda dapat memesan paket wisata atau homestay dengan mengetik 'Pesan paket wisata' atau 'Pesan homestay'.");
            }

            $responseText = "<div class='reservation-history'>";
            $responseText .= "<h3>📋 Riwayat Reservasi Anda</h3>";

            // Group reservations by status
            $pendingReservations = [];
            $confirmedReservations = [];
            $otherReservations = [];

            foreach ($reservations as $reservation) {
                $status = $reservation['id_reservation_status'];

                if ($status == 1) { // pending
                    $pendingReservations[] = $reservation;
                } else if ($status == 2 || $status == 4) { // confirmed or paid
                    $confirmedReservations[] = $reservation;
                } else {
                    $otherReservations[] = $reservation;
                }
            }

            // Display pending reservations first
            if (!empty($pendingReservations)) {
                $responseText .= "<div class='pending-reservations mb-4'>";
                $responseText .= "<h4>⏳ Menunggu Konfirmasi</h4>";

                foreach ($pendingReservations as $reservation) {
                    $responseText .= $this->formatReservationCard($reservation);
                }

                $responseText .= "</div>";
            }

            // Display confirmed/paid reservations
            if (!empty($confirmedReservations)) {
                $responseText .= "<div class='confirmed-reservations mb-4'>";
                $responseText .= "<h4>✅ Reservasi Aktif</h4>";

                foreach ($confirmedReservations as $reservation) {
                    $responseText .= $this->formatReservationCard($reservation);
                }

                $responseText .= "</div>";
            }

            // Display other reservations
            if (!empty($otherReservations)) {
                $responseText .= "<div class='other-reservations mb-4'>";
                $responseText .= "<h4>📜 Riwayat Lainnya</h4>";

                foreach ($otherReservations as $reservation) {
                    $responseText .= $this->formatReservationCard($reservation);
                }

                $responseText .= "</div>";
            }

            // Add cancel instruction
            $responseText .= "<div class='cancel-instruction'>";
            $responseText .= "<p>Untuk membatalkan reservasi yang masih pending, ketik:</p>";
            $responseText .= "<div class='example text-success'>\"Batalkan reservasi [kode reservasi]\"</div>";
            $responseText .= "</div>";

            $responseText .= "</div>";

            return $this->response->setJSON(["response" => $responseText]);
        } catch (Exception $e) {
            log_message('error', 'Exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return $this->response->setJSON(["response" => $e->getMessage()]);
        }
    }

    // Helper function to format reservation cards
    private function formatReservationCard($reservation)
    {
        $reservationId = $reservation['id'];
        $date = date('d F Y', strtotime($reservation['request_date']));
        $endDate = !empty($reservation['request_date_end']) ? " - " . date('d F Y', strtotime($reservation['request_date_end'])) : "";
        $totalPrice = number_format($reservation['total_price'], 0, ',', '.');
        $numberPeople = $reservation['number_people'] ?? '-';

        // Get status text and icon
        $statusInfo = $this->getStatusInfo($reservation['id_reservation_status']);

        // Determine if it's a package or homestay reservation
        $type = !empty($reservation['id_package']) ? 'package' : 'homestay';
        $typeIcon = ($type == 'package') ? '🎫' : '🏠';
        $typeName = !empty($reservation['id_package']) ? 'Paket Wisata' : 'Homestay';
        $itemId = !empty($reservation['id_package']) ? $reservation['id_package'] : $reservation['id_homestay'];

        // Get item name (requires modification to your model)
        $itemName = $this->getItemName($type, $itemId);

        $card = "<div class='reservation-card mb-3 p-3' style='border: 1px solid #ddd; border-radius: 8px;'>";
        $card .= "<div class='d-flex justify-content-between'>";
        $card .= "<h5>{$typeIcon} {$typeName}: {$itemName}</h5>";
        $card .= "<span class='{$statusInfo['class']}'>{$statusInfo['icon']} {$statusInfo['text']}</span>";
        $card .= "</div>";
        $card .= "<ul style='list-style-type: none; padding-left: 5px;'>";
        $card .= "<li>🔖 <b>Kode:</b> {$reservationId}</li>";
        $card .= "<li>📅 <b>Tanggal:</b> {$date}{$endDate}</li>";
        $card .= "<li>👥 <b>Jumlah Orang:</b> {$numberPeople}</li>";
        $card .= "<li>💰 <b>Total Harga:</b> Rp {$totalPrice}</li>";
        $card .= "</ul>";
        $card .= "</div>";

        return $card;
    }

    private function getStatusInfo($statusId)
    {
        switch ($statusId) {
            case 1:
                return [
                    'text' => 'Menunggu Konfirmasi',
                    'icon' => '⏳',
                    'class' => 'text-warning'
                ];
            case 2:
                return [
                    'text' => 'Terkonfirmasi',
                    'icon' => '✅',
                    'class' => 'text-success'
                ];
            case 3:
                return [
                    'text' => 'Dibatalkan',
                    'icon' => '❌',
                    'class' => 'text-danger'
                ];
            case 4:
                return [
                    'text' => 'Terbayar',
                    'icon' => '💰',
                    'class' => 'text-success'
                ];
            case 5:
                return [
                    'text' => 'Selesai',
                    'icon' => '🏁',
                    'class' => 'text-info'
                ];
            default:
                return [
                    'text' => 'Status Tidak Diketahui',
                    'icon' => '❓',
                    'class' => 'text-secondary'
                ];
        }
    }
    private function getItemName($type, $itemId)
    {
        try {
            if ($type == 'package') {
                $package = $this->modelPackage->find($itemId);
                return $package ? $package['name'] : 'Paket tidak ditemukan';
            } else {
                // For homestay, we need to get the name from RumahGadang
                $homestayModel = new \App\Models\HomestayModel();
                $homestay = $homestayModel->find($itemId);

                if ($homestay && !empty($homestay['id_rumah_gadang'])) {
                    $rumahGadang = $this->modelRumahGadang->find($homestay['id_rumah_gadang']);
                    return $rumahGadang ? $rumahGadang['name'] : 'Homestay tidak ditemukan';
                }

                return 'Homestay tidak ditemukan';
            }
        } catch (Exception $e) {
            return 'Nama tidak tersedia';
        }
    }

    public function removePackageReservationAI($reservationId)
    {
        try {
            if (!$this->userId) {
                throw new Exception("Mohon login untuk membatalkan paket");
            }
            $user_id = $this->userId;

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
    public function makeHomestayReservationAI($rumahGadangId,  $requestDate, $requestDateEnd)
    {
        try {
            if (!$this->userId) {
                throw new Exception("Mohon login untuk memesan paket");
            }
            $user_id = $this->userId;
            // Dapatkan data homestay
            $rumahGadang = $this->modelRumahGadang->find($rumahGadangId);
            if (!$rumahGadang && empty($rumahGadang['id_homestay'])) {
                throw new Exception("Homestay tidak ditemukan.");
            }
            $price = $rumahGadang['price_ticket'];

            // Cek apakah tanggal reservasi valid (H-1 minimal)
            $today = date('Y-m-d');
            if ($requestDate <= $today) {
                throw new Exception("Tanggal reservasi harus minimal H-1 dari hari ini.");
            }
            if ($requestDateEnd < $requestDate) {
                throw new Exception("Tanggal berakhir reservasi harus lebih dari tanggal reservasi.");
            }

            // Cek apakah user sudah reservasi di tanggal yang sama
            $existingReservation = $this->modelReservation
                ->where('id_user', $user_id)
                ->where('id_homestay', $rumahGadang['id_homestay'])
                ->where('request_date', $requestDate)
                ->first();

            if ($existingReservation) {
                throw new Exception("Anda sudah memiliki reservasi pada tanggal yang sama.");
            }
            // Pastikan $requestDate dan $requestDateEnd adalah string sebelum dikonversi
            $requestDate = new DateTime((string) $requestDate);
            $requestDateEnd = new DateTime((string) $requestDateEnd);

            // Hitung selisih hari antara tanggal mulai dan tanggal akhir
            $totalDay = $requestDate->diff($requestDateEnd)->days;

            $requestDate =  $requestDate->format('Y-m-d');
            $requestDateEnd = $requestDateEnd->format('Y-m-d');
            // Simpan reservasi baru dengan format tanggal yang benar
            $reservationData = [
                'id' => $this->modelReservation->get_new_id_api(),
                'id_user' => $user_id,
                'id_homestay' => $rumahGadang['id_homestay'],
                'request_date' => $requestDate, // Pastikan diubah ke string
                'request_date_end' => $requestDateEnd, // Pastikan diubah ke string
                'id_reservation_status' => 1, // pending status
                'total_price' => $totalDay * $price,
            ];

            $this->modelReservation->add_r_api($reservationData);
            return $this->response->setJSON(["response" => "<span class='text-success'>Reservasi berhasil dibuat.<br><b><u>{$reservationData['id']}-{$rumahGadang['name']}, tanggal {$requestDate} - {$requestDateEnd}, total harga {$reservationData['total_price']} </u></b>.</span><br> Silahkan melakukan pembayaran!"]);
        } catch (Exception $e) {
            return $this->response->setJSON(["response" => $e->getMessage()]);
        }
    }
}
