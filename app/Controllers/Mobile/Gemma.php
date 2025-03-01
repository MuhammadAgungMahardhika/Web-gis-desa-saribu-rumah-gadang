<?php

namespace App\Controllers\Mobile;

use App\Models\PackageModel;
use App\Models\RumahGadangModel;
use CodeIgniter\RESTful\ResourcePresenter;

class Gemma extends ResourcePresenter
{
    protected $currentUrl;
    protected $helpers = ['auth', 'url', 'filesystem'];
    protected $apiKey = "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";
    protected $apiUrl = "https://api.groq.com/openai/v1/chat/completions";

    protected $modelRumahGadang;
    protected $modelPackage;

    public function __construct()
    {
        $this->currentUrl = 'mobile';
        $this->modelRumahGadang = new RumahGadangModel();
        $this->modelPackage = new PackageModel();
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

    // Memproses request dari frontend
    public function processRequest()
    {
        // Ambil pesan dari request POST
        $message = $this->request->getPost('message');
        if (!$message) {
            return $this->response->setJSON(['error' => 'Message is required'])->setStatusCode(400);
        }

        // Ambil data dari database
        $rumahGadang = $this->modelRumahGadang->get_list_rg_ai()->getResultArray();
        $package = $this->modelPackage->get_list_tp_ai()->getResultArray();
        $jsonRumahGadang = json_encode($rumahGadang, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $jsonPackage = json_encode($package, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        // Ambil history chat dari session (jika ada)
        $history = session()->get('chat_history') ?? [];

        // Ambil riwayat chat dari session
        $history = session()->get('chat_history') ?? [];
        // Batasi jumlah chat agar tidak terlalu besar (misalnya, hanya menyimpan 2 terakhir)
        if (count($history) > 2) {
            $history = array_slice($history, -2); // Hanya simpan 2 percakapan terakhir
        }
        // Tambahkan pesan user baru ke dalam riwayat percakapan
        $history[] = ["role" => "user", "content" => $message];

        // Siapkan data untuk dikirim ke API
        $data = [
            "model" => "gemma2-9b-it",
            "messages" => array_merge(
                [
                    [
                        "role" => "system",
                        "content" => "Kamu berbahasa Indonesia. Kamu adalah AI yang memberi informasi tentang Aplikasi Desa Wisata Saribu Rumah Gadang.\n\n
                    Berikut adalah data Rumah Gadang yang tersedia:\n$jsonRumahGadang\n\n
                    Berikut adalah data Paket Wisata yang tersedia:\n$jsonPackage\n\n
                    Jawablah pertanyaan pengguna berdasarkan informasi di atas."
                    ]
                ],
                $history // Kirim riwayat percakapan sebelumnya
            ),
            'max_tokens' => 300,

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
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Periksa jika ada error dari API
        if ($httpCode !== 200) {
            return $this->response->setJSON(['error' => 'Failed to get response from API'])->setStatusCode($httpCode);
        }

        // Ambil jawaban AI dari response
        $responseData = json_decode($response, true);
        $aiResponse = $responseData['choices'][0]['message']['content'] ?? 'Maaf, terjadi kesalahan.';

        // Tambahkan jawaban AI ke dalam riwayat percakapan
        $history[] = ["role" => "assistant", "content" => $aiResponse];

        // Simpan riwayat percakapan ke session
        session()->set('chat_history', $history);

        return $this->response->setJSON(['response' => $aiResponse]);
    }
}
