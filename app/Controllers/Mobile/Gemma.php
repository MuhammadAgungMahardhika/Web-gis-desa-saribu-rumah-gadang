<?php

namespace App\Controllers\Mobile;

use App\Models\PackageModel;
use App\Models\ReservationModel;
use App\Models\RumahGadangModel;
use CodeIgniter\RESTful\ResourcePresenter;
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
                    "content" => "Kamu berbahasa Indonesia. Kamu adalah AI yang memberi informasi tentang Aplikasi Desa Wisata Saribu.
                    Gunakan fungsi berikut sesuai kebutuhan:
                    - `getWeather` hanya jika pengguna bertanya tentang cuaca.
                    - `getRumahGadang` hanya jika pengguna bertanya tentang daftar Rumah Gadang.
                    - `getPaketWisata` hanya jika pengguna bertanya tentang paket wisata."
                ]
            ],
            $history
        );
        $tools = [
            [
                "type" => "function",
                "function" => [
                    "name" => "get_weather",
                    "description" => "Mendapatkan informasi cuaca untuk Desa Wisata Saribu Rumah Gadang.",
                    "parameters" => [
                        "type" => "object",
                        "properties" => [
                            "location" => [
                                "type" => "string",
                                "description" => "Nama lokasi tetap untuk mendapatkan informasi cuaca.",
                                "enum" => ["Desa Wisata Saribu Rumah Gadang"]
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
                    "description" => "Mendapatkan daftar Rumah Gadang yang tersedia di Desa Wisata Saribu Rumah Gadang.",
                    "parameters" => [
                        "type" => "object",
                        "properties" => [
                            "homestay" => [
                                "type" => "boolean",
                                "description" => "Jika true, hanya menampilkan Rumah Gadang yang berfungsi sebagai homestay."
                            ]
                        ],
                        "required" => []
                    ]
                ]
            ],
            [
                "type" => "function",
                "function" => [
                    "name" => "get_paket_wisata",
                    "description" => "Mendapatkan daftar Paket Wisata yang tersedia di Desa Wisata Saribu Rumah Gadang.",
                    "parameters" => [
                        "type" => "object",
                        "properties" => new stdClass()
                    ]
                ]
            ],
            [
                "type" => "function",
                "function" => [
                    "name" => "make_reservation_ai",
                    "description" => "Membuat reservasi paket wisata di Desa Wisata Saribu Rumah Gadang untuk pengguna yang sedang login.",
                    "parameters" => [
                        "type" => "object",
                        "properties" => [
                            "package_id" => [
                                "type" => "string",
                                "description" => "ID paket wisata yang ingin dipesan."
                            ],
                            "reservationDate" => [
                                "type" => "string",
                                "format" => "date",
                                "description" => "Tanggal reservasi dalam format YYYY-MM-DD."
                            ],
                            "numberPeople" => [
                                "type" => "integer",
                                "description" => "Jumlah orang yang ikut dalam reservasi."
                            ],
                        ],
                        "required" => ["package_id", "reservationDate", "numberPeople"]
                    ]
                ]
            ]
        ];
        $data = [
            "model" => $model,
            "messages" => $message,
            "max_tokens" => 300,
            "tools" => $tools,
            "tool_choice" => "auto",
            "max_completion_tokens" => 4096
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

        // 🔥 PERBAIKAN: Gunakan tool_calls, bukan function_call
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
            case "make_reservation_ai":
                // Pastikan semua parameter yang diperlukan ada
                if (!isset($arguments['package_id'], $arguments['reservationDate'], $arguments['numberPeople'])) {
                    return $this->response->setJSON(["error" => "Parameter tidak lengkap untuk reservasi."]);
                }

                // Ambil parameter dengan nilai default jika tidak disertakan
                $package_id =  $arguments['package_id'];
                $reservationDate = $arguments['reservationDate'];
                $numberPeople = (int) $arguments['numberPeople'];
                return $this->makeReservationAI($package_id, $reservationDate, $numberPeople);

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
            return $this->response->setJSON([
                "response" => "Saat ini tidak ada Rumah Gadang yang tersedia di Desa Wisata Saribu Rumah Gadang."
            ]);
        }

        foreach ($data as $index => $rumah) {
            $responseText .= ($index + 1) .  ". " . "(" . strip_tags($rumah['id']) . ") <b>"  . strip_tags($rumah['name']) . "</b><br>";
        }

        return $this->response->setJSON(["response" => $responseText]);
    }


    // Fungsi untuk mendapatkan daftar Paket Wisata
    public function getPaketWisata()
    {
        $data = $this->modelPackage->get_list_tp_api()->getResultArray();

        if (empty($data)) {
            return $this->response->setJSON(["response" => "Saat ini belum ada paket wisata yang tersedia di Desa Wisata Saribu Rumah Gadang."]);
        }

        $responseText = "<b>Berikut adalah daftar Paket Wisata yang tersedia di Desa Wisata Saribu Rumah Gadang:</b><br><br>";

        foreach ($data as $index => $paket) {
            $hargaFormatted = number_format($paket['price'], 0, ',', '.');
            $capacity = $paket['capacity'];
            $description =  $paket['description'] ? "Keterangan : {$paket['description']} <br>" : null;
            $responseText .= ($index + 1) .  ". " . "(" . htmlspecialchars($paket['id']) . ") " . "<b>" . htmlspecialchars($paket['name']) . "</b> - Harga: Rp {$hargaFormatted}, Kapasitas : {$capacity} orang<br>{$description}";
        }

        return $this->response->setJSON(["response" => $responseText]);
    }

    // 🔥 Fungsi untuk menangani reservasi AI
    public function makeReservationAI($package_id, $reservationDate, $numberPeople)
    {
        $user_id = user()->id;
        // Dapatkan data paket wisata
        $package = $this->modelPackage->find($package_id);

        if (!$package) {
            return $this->response->setJSON(["error" => "Paket wisata tidak ditemukan."]);
        }

        $capacity = $package['capacity'];
        $price = $package['price'];

        // Cek apakah jumlah orang melebihi kapasitas
        if ($numberPeople <= 0) {
            return $this->response->setJSON(["error" => "Minimal 1 orang untuk reservasi."]);
        }

        if ($numberPeople > $capacity) {
            return $this->response->setJSON(["error" => "Kapasitas maksimal adalah {$capacity} orang."]);
        }

        // Cek apakah tanggal reservasi valid (H-1 minimal)
        $today = date('Y-m-d');
        if ($reservationDate <= $today) {
            return $this->response->setJSON(["error" => "Tanggal reservasi harus minimal H-1 dari hari ini."]);
        }

        // Cek apakah user sudah reservasi di tanggal yang sama
        $existingReservation = $this->modelReservation
            ->where('id_user', $user_id)
            ->where('request_date', $reservationDate)
            ->first();

        if ($existingReservation) {
            return $this->response->setJSON(["error" => "Anda sudah memiliki reservasi pada tanggal yang sama."]);
        }

        // Simpan reservasi baru
        $reservationData = [
            'id' =>   $this->modelReservation->get_new_id_api(),
            'id_user' => $user_id,
            'id_package' => $package_id,
            'request_date' => $reservationDate,
            'id_reservation_status' => 1, // pending status
            'number_people' => $numberPeople,
            'total_price' => $numberPeople * $price,
        ];

        $this->modelReservation->add_r_api($reservationData);

        return $this->response->setJSON(["response" => "Reservasi berhasil dibuat untuk paket : {$package['name']}, tanggal {$reservationDate}. Silahkan melakukan pembayaran!"]);
    }
}
