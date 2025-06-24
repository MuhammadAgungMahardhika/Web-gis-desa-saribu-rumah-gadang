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
    // Make sure to use environment variables or a secure method for API keys
    // For demonstration, keeping it here, but recommend moving to .env
    protected $apiKey = "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";
    protected $apiUrl = "https://api.groq.com/openai/v1/chat/completions";

    protected $modelRumahGadang;
    protected $modelPackage;
    protected $modelReservation;
    protected $modelHomestay; // Added HomestayModel

    protected $userId = null;

    public function __construct()
    {
        $this->currentUrl = 'mobile';
        $this->modelRumahGadang = new RumahGadangModel();
        $this->modelPackage = new PackageModel();
        $this->modelReservation = new ReservationModel();
        $this->modelHomestay = new HomestayModel(); // Initialize HomestayModel
    }

    // Display the main page
    public function index()
    {
        $data = [
            'title' => 'Gemini AI', // Or 'Gemma AI'
            'currentUrl' => $this->currentUrl
        ];
        return view('mobile/gemma', $data);
    }

    // Reset chat history
    public function resetChat()
    {
        session()->remove('chat_history');
        return $this->response->setJSON(['message' => 'Chat history cleared']);
    }

    // Main function to process user requests
    public function processRequest()
    {
        try {
            $message = $this->request->getPost('message');
            // Ensure userId is correctly passed and validated in a real app
            $this->userId = $this->request->getPost('userId');

            if (!$message) {
                return $this->response->setJSON(['error' => 'Message is required'])->setStatusCode(400);
            }
            // In a real app, authenticate the user properly to get userId
            // This is a basic example based on receiving it in POST
            if (!$this->userId) {
                // Depending on your auth system, you might redirect to login
                // or return an error indicating login is needed for certain actions
                // For now, we allow general info requests but block booking
                // return $this->response->setJSON(['error' => 'User is required for this action'])->setStatusCode(401);
            }


            // Get chat history (limit to latest 5 turns to manage token usage)
            $history = session()->get('chat_history') ?? [];
            // Keep a reasonable history length
            if (count($history) > 10) { // Increased history slightly
                $history = array_slice($history, -10);
            }

            // Add current user message to history
            $history[] = ["role" => "user", "content" => $message];

            // Prepare data for the AI API
            // Using Gemma 2 9B Instruct model
            $model = "gemma2-9b-it";

            // System instruction to guide the AI
            $systemInstruction = "You are an AI providing information and booking assistance for the Saribu Rumah Gadang Tourism Village Application. Your goal is to help users find information, list packages or homestays, view their reservations, and facilitate booking processes.

Use the provided functions when the user's intent is clear. If a booking function is requested but details like dates or number of people are missing or unclear, respond by asking for the specific missing information conversationally, *before* attempting to call the function again. Do not guess parameter values.

Available functions:
- `get_weather`: Get current weather for Saribu Rumah Gadang. Use only when user asks about weather.
- `get_rumah_gadang`: List Rumah Gadang. Use only when user asks about Rumah Gadang or homestays.
- `get_paket_wisata`: List tour packages. Use only when user asks about tour packages.
- `get_reservation`: View user's reservation history. Use only when user asks about their reservations.
- `make_package_reservation_ai`: Make a tour package reservation. Use only when user explicitly asks to book a specific package and provides date and number of people, or after you have asked for and received this information.
- `make_homestay_reservation_ai`: Make a homestay reservation. Use only when user explicitly asks to book a specific homestay and provides check-in, check-out dates, and number of people, or after you have asked for and received this information.
- `remove_package_reservation_ai`: Cancel a pending package reservation. Use only when user asks to cancel a reservation and provides the reservation ID.

If the user's request doesn't match any function, respond conversationally based on the information you have about the village. Be friendly and helpful.";


            $messagesWithSystem = array_merge(
                [
                    [
                        "role" => "system",
                        "content" => $systemInstruction
                    ]
                ],
                $history
            );

            // Tools definition remains the same, as this tells the AI *what it can do* and *what info it needs*.
            // The conversational part happens if the AI *cannot* extract the required info and thus doesn't propose the tool call,
            // OR if the PHP function explicitly asks for more info when a call is made with incomplete args.
            $tools = [
                [
                    "type" => "function",
                    "function" => [
                        "name" => "get_weather",
                        "description" => "Retrieve weather information for Saribu Rumah Gadang Tourism Village. Use only when user explicitly asks about the weather.",
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
                        "description" => "Retrieve a list of Rumah Gadang available in Saribu Rumah Gadang Tourism Village. Optionally filter for those that function as homestays. Use only when user explicitly asks about Rumah Gadang or homestays.",
                        "parameters" => [
                            "type" => "object",
                            "properties" => [
                                "homestay" => [
                                    "type" => "boolean",
                                    "description" => "Set to true if the user is specifically asking about Rumah Gadang that function as homestays. Set to false or omit if asking about Rumah Gadang in general (including those that aren't homestays)."
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    "type" => "function",
                    "function" => [
                        "name" => "get_paket_wisata",
                        "description" => "Retrieve a list of available tour packages in Saribu Rumah Gadang Tourism Village. Use only when user explicitly asks about tour packages.",
                        "parameters" => [
                            "type" => "object",
                            "properties" => new stdClass() // No parameters needed
                        ]
                    ]
                ],
                [
                    "type" => "function",
                    "function" => [
                        "name" => "get_reservation",
                        "description" => "Retrieve reservation history of the logged-in user. Use only when user explicitly asks to see their reservations.",
                        "parameters" => [
                            "type" => "object",
                            "properties" => new stdClass() // No parameters needed
                        ]
                    ]
                ],
                [
                    "type" => "function",
                    "function" => [
                        "name" => "make_package_reservation_ai",
                        "description" => "Make a tour package reservation in Saribu Rumah Gadang Tourism Village for a logged-in user. This requires a package identifier (ID or name), the desired reservation date (YYYY-MM-DD), and the number of people. Only use this function if the user has provided or confirmed all these details.",
                        "parameters" => [
                            "type" => "object",
                            "properties" => [
                                "packageId" => [
                                    "type" => "string",
                                    "description" => "ID of the tour package to be booked. Use this if known. If not, use packageName."
                                ],
                                "packageName" => [
                                    "type" => "string",
                                    "description" => "Name of the tour package to be booked. Use this if packageId is not known. Try to match the user's input to an existing package name."
                                ],
                                "requestDate" => [
                                    "type" => "string",
                                    "format" => "date",
                                    "description" => "The specific date the user wants to make the package reservation, in YYYY-MM-DD format. MUST be provided."
                                ],
                                "numberPeople" => [
                                    "type" => "integer",
                                    "description" => "The number of people included in the reservation. MUST be provided."
                                ],
                            ],
                            "required" => ["requestDate", "numberPeople"] // AI should aim to get these before calling
                        ]
                    ]
                ],
                [
                    "type" => "function",
                    "function" => [
                        "name" => "make_homestay_reservation_ai",
                        "description" => "Make a Rumah Gadang / homestay reservation in Saribu Rumah Gadang Tourism Village for a logged-in user. This requires a homestay identifier (Rumah Gadang ID or name), the check-in date (YYYY-MM-DD), the check-out date (YYYY-MM-DD), and the number of people. Only use this function if the user has provided or confirmed all these details.",
                        "parameters" => [
                            "type" => "object",
                            "properties" => [
                                "rumahGadangId" => [
                                    "type" => "string",
                                    "description" => "ID of the Rumah Gadang/homestay to be booked. Use this if known. If not, use rumahGadangOrHomestayName."
                                ],
                                "rumahGadangOrHomestayName" => [
                                    "type" => "string",
                                    "description" => "Name of the Rumah Gadang/homestay to be booked. Use this if rumahGadangId is not known. Try to match the user's input to an existing homestay name."
                                ],
                                "requestDate" => [
                                    "type" => "string",
                                    "format" => "date",
                                    "description" => "The desired check-in date in YYYY-MM-DD format. MUST be provided."
                                ],
                                "requestDateEnd" => [
                                    "type" => "string",
                                    "format" => "date",
                                    "description" => "The desired check-out date in YYYY-MM-DD format. MUST be provided and must be after the check-in date."
                                ],
                                "numberPeople" => [
                                    "type" => "integer",
                                    "description" => "The number of people staying. MUST be provided."
                                ],
                            ],
                            "required" => ["requestDate", "requestDateEnd", "numberPeople"] // AI should aim to get these before calling
                        ]
                    ]
                ],
                [
                    "type" => "function",
                    "function" => [
                        "name" => "remove_package_reservation_ai",
                        "description" => "Cancel a pending tour package reservation for the logged-in user. This requires the reservation ID. Only use if user explicitly asks to cancel a reservation and provides the ID.",
                        "parameters" => [
                            "type" => "object",
                            "properties" => [
                                "reservationId" => [
                                    "type" => "string",
                                    "description" => "The ID of the reservation to cancel. MUST be provided."
                                ],
                            ],
                            "required" => ["reservationId"] // AI should aim to get this before calling
                        ]
                    ]
                ],

            ];

            $data = [
                "model" => $model,
                "messages" => $messagesWithSystem,
                "max_tokens" => 4096, // Increased max_tokens for potentially longer responses/history
                "temperature" => 0.7, // Adjusted temperature slightly for more focused responses
                "top_p" => 1,
                "tools" => $tools,
                "tool_choice" => "auto", // Let the model decide whether to use a tool or generate text
            ];

            // Send request to the AI API
            $ch = curl_init($this->apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer " . $this->apiKey,
                "Content-Type: application/json"
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Get HTTP status code
            curl_close($ch);

            $responseData = json_decode($response, true);

            // Log API response for debugging
            // log_message('debug', 'Groq API Response (' . $httpcode . '): ' . $response);


            // Check for API errors
            if ($httpcode !== 200) {
                $errorMsg = $responseData['error']['message'] ?? 'Unknown API error';
                log_message('error', "Groq API error: " . $errorMsg);
                return $this->response->setJSON(['response' => 'Maaf, terjadi kesalahan saat memproses permintaan Anda. Silakan coba lagi.']);
            }


            // Check if the AI proposed a function call
            if (isset($responseData['choices'][0]['message']['tool_calls'])) {
                $toolCalls = $responseData['choices'][0]['message']['tool_calls'];
                // Handle the first tool call for simplicity in this example
                // You might need more complex logic if multiple calls are returned
                $toolCall = $toolCalls[0];
                $functionName = $toolCall['function']['name'];
                $arguments = json_decode($toolCall['function']['arguments'], true);

                // Before calling the function, save the AI's tool call intent to history
                // This helps the AI remember what it tried to do in the next turn
                // if the function requires more info.
                $history[] = [
                    "role" => "assistant",
                    "tool_calls" => [$toolCall] // Save the specific tool call
                ];
                session()->set('chat_history', $history);


                // Call the internal handler function
                return $this->handleFunctionCall($functionName, $arguments);
            }

            // If no function call, get the AI's text response
            $aiResponse = $responseData['choices'][0]['message']['content'] ?? 'Maaf, saya tidak mengerti. Bisakah Anda ulangi?';

            // Save the AI's text response to history
            $history[] = ["role" => "assistant", "content" => $aiResponse];
            session()->set('chat_history', $history);

            return $this->response->setJSON(['response' => $aiResponse]);
        } catch (Exception $e) {
            // Log the exception for debugging
            log_message('error', 'Exception in processRequest: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            // Return a user-friendly error message
            return $this->response->setJSON(['response' => 'Maaf, terjadi masalah internal. Silakan coba lagi nanti atau hubungi administrator.']);
        }
    }

    // 🔥 Modified: Function to handle function calls with parameters
    private function handleFunctionCall($functionName, $arguments)
    {
        try {
            // Add the function call and its arguments to history before execution
            // This helps maintain context if the function returns a prompt for more info.
            // This was moved inside processRequest() before the call to handleFunctionCall
            // Let's add a tool response to history *after* getting the result from the internal function
            // But first, execute the function:
            $functionResult = null;

            switch ($functionName) {
                case "get_weather":
                    $functionResult = $this->getWeather();
                    break;
                case "get_rumah_gadang":
                    // Pass the 'homestay' argument if it exists, default to null
                    $homestay = isset($arguments['homestay']) ? ($arguments['homestay'] ? "true" : "false") : null;
                    $functionResult = $this->getRumahGadang($homestay);
                    break;
                case "get_paket_wisata":
                    $functionResult = $this->getPaketWisata();
                    break;
                case "get_reservation":
                    // Check if user is logged in before allowing reservation view
                    if (!$this->userId) {
                        // Return a specific response indicating login is required
                        $functionResult = $this->response->setJSON([
                            "response" => "Mohon login terlebih dahulu untuk melihat riwayat reservasi Anda."
                        ]);
                    } else {
                        $functionResult = $this->getReservation();
                    }
                    break;
                case "make_package_reservation_ai":
                    // This function now handles checking for missing parameters internally
                    // We just pass the arguments as received from the AI
                    $functionResult = $this->makePackageReservationAI($arguments);
                    break;
                case "remove_package_reservation_ai":
                    // Check if user is logged in
                    if (!$this->userId) {
                        $functionResult = $this->response->setJSON([
                            "response" => "Mohon login terlebih dahulu untuk membatalkan reservasi."
                        ]);
                    } else if (!isset($arguments['reservationId'])) {
                        // Although the tool requires it, the AI might miss it.
                        // Ask explicitly here as a fallback.
                        $functionResult = $this->response->setJSON(["response" => "Untuk membatalkan reservasi, saya butuh Kode Bookingnya. Kode booking bisa Anda lihat di riwayat reservasi."]);
                    } else {
                        $reservationId = $arguments['reservationId'];
                        $functionResult = $this->removePackageReservationAI($reservationId);
                    }
                    break;
                case "make_homestay_reservation_ai":
                    // Check if user is logged in
                    if (!$this->userId) {
                        $functionResult = $this->response->setJSON([
                            "response" => "Mohon login terlebih dahulu untuk memesan homestay."
                        ]);
                    } else {
                        // This function now handles checking for missing parameters internally
                        // We just pass the arguments as received from the AI
                        $functionResult = $this->makeHomestayReservationAI($arguments);
                    }
                    break;
                default:
                    // This case should ideally not be hit if tool_choice="auto" and tools are well-defined
                    $functionResult = $this->response->setJSON(['response' => "Maaf, saya tidak dapat melakukan tindakan tersebut saat ini."]);
                    break;
            }

            // After executing the function, log/store the tool response
            // This is part of the multi-turn tool calling pattern, though not strictly necessary
            // for just sending the response back to the UI in this simpler model.
            // The UI simply displays whatever JSON 'response' it gets.
            // So we just return the result from the function call.

            return $functionResult;
        } catch (Exception $e) {
            log_message('error', 'Exception in handleFunctionCall: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return $this->response->setJSON(['response' => 'Maaf, terjadi masalah saat menjalankan tindakan.']);
        }
    }


    // Function to get weather (remains the same)
    public function getWeather()
    {
        try {
            // Using OpenWeatherMap API
            $apiKey = "2390a9743ed947a7ab68238ae3039af1"; // Recommend moving to .env
            $lat = "-1.4815029"; // Coordinates for Saribu Rumah Gadang
            $lng = "101.0574556";
            $apiUrl = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lng}&appid={$apiKey}&lang=id&units=metric";

            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // Optional: set timeout
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch); // Get cURL error
            curl_close($ch);

            if ($curlError) {
                log_message('error', 'cURL error in getWeather: ' . $curlError);
                return $this->response->setJSON(['response' => 'Maaf, gagal mengambil data cuaca karena masalah koneksi.']);
            }

            $data = json_decode($response, true);

            if ($httpcode !== 200 || !$data || isset($data['cod']) && $data['cod'] != 200) {
                $errorMsg = $data['message'] ?? 'Unknown weather API error';
                log_message('error', "Weather API error (' . $httpcode . '): " . $errorMsg);
                return $this->response->setJSON(['response' => 'Maaf, gagal mendapatkan data cuaca saat ini.']);
            }

            $weatherDescription = ucfirst($data['weather'][0]['description']);
            $temperature = $data['main']['temp'];
            $locationName = $data['name'] ?? 'Lokasi Desa Wisata Saribu Rumah Gadang'; // Get location name from API if available

            return $this->response->setJSON([
                "response" => "Saat ini cuaca di {$locationName} adalah <b>{$weatherDescription}</b>, dengan suhu sekitar <b>{$temperature}°C</b>. Selalu berhati-hati di perjalanan!"
            ]);
        } catch (Exception $e) {
            log_message('error', 'Exception in getWeather: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return $this->response->setJSON(["response" => "Maaf, terjadi kesalahan saat mencoba mendapatkan cuaca."]);
        }
    }

    // Function to get list of Rumah Gadang (remains the same)
    public function getRumahGadang($homestay = null)
    {
        try {
            if ($homestay === "true") {
                // Fetch only Rumah Gadang linked to a homestay
                $data = $this->modelRumahGadang->get_list_hm_api()->getResultArray();

                if (empty($data)) {
                    // Changed response to be more conversational
                    return $this->response->setJSON(["response" => "Saat ini belum ada homestay yang terdaftar di Desa Wisata Saribu Rumah Gadang."]);
                }

                $responseText = "<div class='homestay-list'>";
                $responseText .= "<h3>🏠 Daftar Homestay</h3>";
                $responseText .= "<p>Berikut daftar homestay yang tersedia di Desa Wisata Saribu Rumah Gadang:</p>";

                foreach ($data as $rumah) {
                    // Ensure fields exist before accessing
                    $price = isset($rumah['ticket_price']) && !empty($rumah['ticket_price'])
                        ? number_format($rumah['ticket_price'], 0, ',', '.')
                        : "Hubungi pengelola";
                    $name = htmlspecialchars($rumah['name'] ?? 'Nama tidak tersedia');
                    $address = htmlspecialchars($rumah['address'] ?? 'Alamat tidak tersedia');
                    $id = $rumah['id'] ?? 'N/A'; // Include ID for clarity

                    $responseText .= "<div class='homestay-item mb-3' style='border: 1px solid #eee; padding: 10px; border-radius: 5px;'>";
                    $responseText .= "<h4>🏡 {$name} (ID: {$id})</h4>"; // Show ID
                    $responseText .= "<ul style='list-style-type: none; padding-left: 10px;'>";
                    $responseText .= "<li>💰 <b>Harga per malam:</b> Rp {$price}</li>";
                    if ($address !== 'Alamat tidak tersedia') {
                        $responseText .= "<li>📍 <b>Alamat:</b> {$address}</li>";
                    }
                    $responseText .= "</ul>";
                    $responseText .= "</div>";
                }

                $responseText .= "<div class='booking-guide mt-3'>";
                $responseText .= "<h4>Cara Pesan Homestay</h4>";
                $responseText .= "<p>Jika Anda tertarik, sebutkan nama atau ID homestay, tanggal check-in, tanggal check-out, dan jumlah orang. Contoh:</p>";
                $responseText .= "<div class='example text-success' style='background-color: #e9ecef; padding: 10px; border-left: 3px solid green;'>";
                $responseText .= "\"Pesan homestay {$name} untuk 2 orang dari tanggal 20-05-2025 sampai 22-05-2025\"";
                $responseText .= "</div>";
                $responseText .= "</div>";

                $responseText .= "</div>";
            } else {
                // Format text for outputting list of Rumah Gadang (not necessarily homestays)
                $data = $this->modelRumahGadang->get_list_rg_api()->getResultArray();

                if (empty($data)) {
                    // Changed response to be more conversational
                    return $this->response->setJSON(["response" => "Saat ini belum ada Rumah Gadang yang terdaftar di Desa Wisata Saribu Rumah Gadang."]);
                }

                $responseText = "<div class='rumah-gadang-list'>";
                $responseText .= "<h3>🏛️ Daftar Rumah Gadang</h3>";
                $responseText .= "<p>Berikut daftar Rumah Gadang yang bisa Anda kunjungi di Desa Wisata Saribu Rumah Gadang:</p>";

                foreach ($data as $rumah) {
                    $name = htmlspecialchars($rumah['name'] ?? 'Nama tidak tersedia');
                    $address = htmlspecialchars($rumah['address'] ?? 'Alamat tidak tersedia');

                    $responseText .= "<div class='rumah-gadang-item mb-3' style='border: 1px solid #eee; padding: 10px; border-radius: 5px;'>";
                    $responseText .= "<h4>🏛️ {$name}</h4>";
                    $responseText .= "<ul style='list-style-type: none; padding-left: 10px;'>";
                    if ($address !== 'Alamat tidak tersedia') {
                        $responseText .= "<li>📍 <b>Alamat:</b> {$address}</li>";
                    }
                    // Optionally add a link or info if it IS a homestay
                    if (!empty($rumah['id_homestay'])) {
                        $responseText .= "<li>🏠 Juga tersedia sebagai Homestay</li>";
                    }
                    $responseText .= "</ul>";
                    $responseText .= "</div>";
                }
                $responseText .= "<p>Untuk melihat daftar yang bisa dipesan sebagai homestay, ketik 'Daftar homestay'.</p>";
                $responseText .= "</div>";
            }

            return $this->response->setJSON(["response" => $responseText]);
        } catch (Exception $e) {
            log_message('error', 'Exception in getRumahGadang: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return $this->response->setJSON(["response" => "Maaf, terjadi kesalahan saat mencoba mengambil daftar Rumah Gadang atau homestay."]);
        }
    }


    // Function to get list of Tour Packages (remains the same)
    public function getPaketWisata()
    {
        try {
            $data = $this->modelPackage->get_list_tp_api()->getResultArray();

            if (empty($data)) {
                // Changed response to be more conversational
                return $this->response->setJSON(["response" => "Saat ini belum ada paket wisata yang tersedia di Desa Wisata Saribu Rumah Gadang."]);
            }

            $responseText = "<div class='package-list'>";
            $responseText .= "<h3>📋 Daftar Paket Wisata</h3>";
            $responseText .= "<p>Berikut daftar paket wisata yang bisa Anda nikmati di Desa Wisata Saribu Rumah Gadang:</p>";

            foreach ($data as $paket) {
                // Ensure fields exist before accessing
                $hargaFormatted = number_format($paket['price'] ?? 0, 0, ',', '.');
                $capacity = $paket['capacity'] ?? '-';
                $description = isset($paket['description']) && !empty($paket['description']) ? "<span class='text-muted'>{$paket['description']}</span>" : "";
                $name = htmlspecialchars($paket['name'] ?? 'Nama tidak tersedia');
                $id = $paket['id'] ?? 'N/A'; // Include ID for clarity


                $responseText .= "<div class='package-item mb-3' style='border: 1px solid #eee; padding: 10px; border-radius: 5px;'>";
                $responseText .= "<h4>🎫 {$name} (ID: {$id})</h4>"; // Show ID
                $responseText .= "<ul style='list-style-type: none; padding-left: 10px;'>";
                $responseText .= "<li>💰 <b>Harga:</b> Rp {$hargaFormatted}</li>";
                $responseText .= "<li>👥 <b>Kapasitas:</b> {$capacity} orang</li>";
                if ($description) {
                    $responseText .= "<li>📝 <b>Detail:</b> {$description}</li>";
                }
                $responseText .= "</ul>";
                $responseText .= "</div>";
            }

            $responseText .= "<div class='booking-guide mt-3'>";
            $responseText .= "<h4>Cara Pemesanan</h4>";
            $responseText .= "<p>Untuk memesan paket, sebutkan nama atau ID paket, tanggal, dan jumlah orang. Contoh:</p>";
            $responseText .= "<div class='example text-success' style='background-color: #e9ecef; padding: 10px; border-left: 3px solid green;'>";
            $responseText .= "\"Pesan paket {$name} untuk 4 orang tanggal 15-05-2025\"";
            $responseText .= "</div>";
            $responseText .= "</div>";


            $responseText .= "</div>";

            return $this->response->setJSON(["response" => $responseText]);
        } catch (Exception $e) {
            log_message('error', 'Exception in getPaketWisata: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return $this->response->setJSON(["response" => "Maaf, terjadi kesalahan saat mencoba mengambil daftar paket wisata."]);
        }
    }

    // 🔥 Modified: Function to handle AI initiated package reservation
    // Now accepts arguments array and checks for missing info conversationally
    public function makePackageReservationAI($arguments)
    {
        try {
            if (!$this->userId) {
                // Use a conversational tone for the response
                return $this->response->setJSON([
                    "response" => "Mohon maaf, Anda perlu login terlebih dahulu untuk bisa melakukan pemesanan paket wisata."
                ]);
            }
            $user_id = $this->userId;

            $package_id = $arguments['packageId'] ?? null;
            $packageName = $arguments['packageName'] ?? null;
            $requestDate = $arguments['requestDate'] ?? null;
            $numberPeople = $arguments['numberPeople'] ?? null; // Raw value, might be string or int or null

            // 1. Identify the package
            $package = null;
            if (!empty($package_id)) {
                $package = $this->modelPackage->find($package_id);
            } elseif (!empty($packageName)) {

            $normalizedName = strtolower(trim(preg_replace('/\s+/', ' ', $packageName)));
            $package = $this->modelPackage
                        ->like('LOWER(name)', $normalizedName, 'both')
                        ->first();
            if (!$package) {
                log_message('error', "Paket tidak ditemukan untuk input: {$packageName}");
            
                $suggestions = $this->modelPackage
                    ->like('LOWER(name)', $normalizedName, 'both')
                    ->limit(5)
                    ->findAll();
            
                if (count($suggestions) > 0) {
                    $list = array_map(fn($s) => "- <b>" . htmlspecialchars($s['name']) . "</b>", $suggestions);
                    return $this->response->setJSON([
                        "response" => "Paket <b>{$packageName}</b> tidak ditemukan. Mungkin yang Anda maksud salah satu ini:<br>" . implode("<br>", $list)
                    ]);
                }
            
                return $this->response->setJSON([
                    "response" => "Maaf, kami tidak menemukan paket wisata dengan nama <b>{$packageName}</b>. Silakan ketik 'Daftar paket wisata' untuk melihat semua pilihan."
                ]);
            }


            $package_name = $package['name'];
            $package_id = $package['id']; // Ensure we have the correct ID
            $capacity = $package['capacity'] ?? 0;
            $price = $package['price'] ?? 0;

            // 2. Check for missing date
            if (empty($requestDate)) {
                // Ask for the date conversationally
                return $this->response->setJSON([
                    "response" => "Baik, paket <b>{$package_name}</b>. Untuk tanggal berapa Anda ingin reservasi? Mohon sebutkan tanggalnya (contoh: 20-05-2025 atau 20 Mei 2025)."
                ]);
            }

            // 3. Check for missing number of people
            // Check if it's provided AND is a valid positive integer
            if ($numberPeople === null || !is_numeric($numberPeople) || (int)$numberPeople <= 0) {
                // Store the package ID and requested date in session temporarily
                // for the next turn when the user provides the number of people.
                // A more robust solution might involve tracking conversation state.
                // For this simplified interactive model, we rely on the AI sending all params
                // again in the next turn based on history, but adding temporary state
                // can make it more reliable if the AI struggles. Let's add basic state.
                session()->set('booking_package_temp', [
                    'packageId' => $package_id,
                    'requestDate' => $requestDate,
                ]);

                // Ask for the number of people conversationally
                return $this->response->setJSON([
                    "response" => "Oke, reservasi paket <b>{$package_name}</b> untuk tanggal <b>{$requestDate}</b>. Berapa orang yang akan ikut?"
                ]);
            }

            // Ensure numberPeople is an integer after checks
            $numberPeople = (int) $numberPeople;

            // 4. Validate the date format and value
            // Try to parse various date formats user might give conversationally
            $dateObj = null;
            $formatsToTry = ['Y-m-d', 'd-m-Y', 'Y/m/d', 'd/m/Y', 'd F Y', 'j F Y', 'd M Y', 'j M Y']; // Add more formats if needed

            foreach ($formatsToTry as $format) {
                $dateObj = DateTime::createFromFormat($format, $requestDate);
                if ($dateObj && $dateObj->format($format) === $requestDate) {
                    break; // Found a valid format
                }
            }

            if (!$dateObj) {
                // If date format is still invalid after trying formats
                // Ask for the date again with clear format examples
                return $this->response->setJSON([
                    "response" => "Maaf, format tanggal <b>{$requestDate}</b> tidak valid. Mohon gunakan format seperti YYYY-MM-DD (contoh: 2025-05-20) atau DD-MM-YYYY (contoh: 20-05-2025)."
                ]);
            }

            $formattedRequestDate = $dateObj->format('Y-m-d'); // Standardize date format for database

            // Check if reservation date is in the future (at least tomorrow)
            $today = new DateTime('today');
            $requestDateTime = new DateTime($formattedRequestDate);

            if ($requestDateTime <= $today) {
                return $this->response->setJSON([
                    "response" => "Tanggal reservasi harus untuk hari esok atau setelahnya. Mohon pilih tanggal setelah tanggal " . $today->format('d F Y') . "."
                ]);
            }


            // 5. Validate number of people against capacity
            if ($numberPeople > $capacity) {
                return $this->response->setJSON([
                    "response" => "Maaf, jumlah peserta ({$numberPeople} orang) melebihi kapasitas maksimal paket <b>{$package_name}</b> ({$capacity} orang). Silakan sesuaikan jumlah peserta atau pilih paket lain."
                ]);
            }

            // 6. Check for existing reservation on the same date for the same package/user
            $existingReservation = $this->modelReservation
                ->where('id_user', $user_id)
                ->where('id_package', $package_id)
                ->where('request_date', $formattedRequestDate)
                ->whereIn('id_reservation_status', [1, 2, 4]) // Check for pending, confirmed, paid
                ->first();

            if ($existingReservation) {
                return $this->response->setJSON([
                    "response" => "Anda sudah memiliki reservasi untuk paket <b>{$package_name}</b> pada tanggal <b>" . date('d F Y', strtotime($formattedRequestDate)) . "</b> (Kode Booking: <b>{$existingReservation['id']}</b>)."
                ]);
            }


            // 7. Proceed with reservation if all checks pass
            $id = $this->modelReservation->get_new_id_api();
            $total_price = $numberPeople * $price;
            $reservationData = [
                'id' => $id,
                'id_user' => $user_id,
                'id_package' => $package_id,
                'request_date' => $formattedRequestDate, // Use standardized format
                'id_reservation_status' => 1, // pending status
                'number_people' => $numberPeople,
                'total_price' => $total_price,
                'created_at' => date('Y-m-d H:i:s'), // Add timestamps
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            // Using add_r_api which likely handles insertion
            $added = $this->modelReservation->add_r_api($reservationData);

            if (!$added) {
                // Check if add_r_api returns a boolean or throws an exception on failure
                // Assuming it might fail silently or return false
                throw new Exception("Gagal menyimpan data reservasi paket.");
            }


            // Clear temporary state if booking was successful
            session()->remove('booking_package_temp');


            // Format confirmation message
            $formattedDate = date('d F Y', strtotime($formattedRequestDate));
            $totalPriceFormatted = number_format($total_price, 0, ',', '.');

            return $this->response->setJSON([
                "response" => "<span class='text-success'>✅ Reservasi paket <b>{$package_name}</b> berhasil dibuat!</span><br><br>" .
                    "📋 <b>Detail Reservasi:</b><br>" .
                    "🔖 Kode Booking: <b>{$id}</b><br>" .
                    "👥 Jumlah Peserta: <b>{$numberPeople} orang</b><br>" .
                    "📅 Tanggal: <b>{$formattedDate}</b><br>" .
                    "💰 Total Harga: <b>Rp {$totalPriceFormatted}</b><br><br>" .
                    "Silakan lakukan pembayaran sesuai petunjuk yang akan dikirimkan ke email Anda.<br>" .
                    "Untuk melihat reservasi Anda, ketik <b>'Lihat reservasi saya'</b>."
            ]);
        } catch (Exception $e) {
            log_message('error', 'Exception in makePackageReservationAI: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            // Return the specific exception message to the user
            return $this->response->setJSON(["response" => "❌ " . $e->getMessage()]);
        }
    }

    // Function to get user reservations (remains the same, minor formatting tweaks)
    public function getReservation()
    {
        try {
            $userId = $this->userId;

            if (!$userId) {
                // This check should ideally happen in handleFunctionCall, but kept here too as a safeguard
                throw new Exception("Mohon login terlebih dahulu untuk melihat riwayat reservasi Anda.");
            }

            $reservations = $this->modelReservation->get_r_by_id_user_api($userId)->getResultArray();

            if (empty($reservations)) {
                // More conversational response
                return $this->response->setJSON([
                    "response" => "Anda belum memiliki riwayat reservasi. \n\nTertarik untuk merencanakan kunjungan? Anda bisa ketik:\n- 'Daftar paket wisata' untuk melihat paket tur.\n- 'Daftar homestay' untuk melihat pilihan penginapan."
                ]);
            }

            $responseText = "<div class='reservation-history'>";
            $responseText .= "<h3>📋 Riwayat Reservasi Anda</h3>";
            $responseText .= "<p>Berikut detail reservasi Anda:</p>";


            // Sort reservations: Pending/Active first, then others
            usort($reservations, function ($a, $b) {
                $statusA = $a['id_reservation_status'];
                $statusB = $b['id_reservation_status'];

                $priority = [1, 2, 4]; // Pending, Confirmed, Paid first

                $priorityA = array_search($statusA, $priority);
                $priorityB = array_search($statusB, $priority);

                if ($priorityA !== false && $priorityB === false) return -1; // A comes first
                if ($priorityA === false && $priorityB !== false) return 1; // B comes first
                if ($priorityA !== false && $priorityB !== false) {
                    // If both are in priority, sort by date (newest first)
                    return strtotime($b['request_date']) - strtotime($a['request_date']);
                }

                // Otherwise, sort by date (newest first)
                return strtotime($b['request_date']) - strtotime($a['request_date']);
            });


            foreach ($reservations as $reservation) {
                $responseText .= $this->formatReservationCard($reservation);
            }

            // Add cancel instruction only if there are pending reservations
            $hasPending = false;
            foreach ($reservations as $res) {
                if ($res['id_reservation_status'] == 1) {
                    $hasPending = true;
                    break;
                }
            }

            if ($hasPending) {
                $responseText .= "<div class='cancel-instruction mt-3'>";
                $responseText .= "<p>Anda dapat membatalkan reservasi yang masih <b>Menunggu Konfirmasi</b> dengan mengetik:</p>";
                $responseText .= "<div class='example text-success' style='background-color: #e9ecef; padding: 10px; border-left: 3px solid green;'>\"Batalkan reservasi [Kode Booking]\"</div>";
                $responseText .= "<p>Contoh: <b>\"Batalkan reservasi ABC123\"</b></p>";
                $responseText .= "</div>";
            }


            $responseText .= "</div>";

            return $this->response->setJSON(["response" => $responseText]);
        } catch (Exception $e) {
            log_message('error', 'Exception in getReservation: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return $this->response->setJSON(["response" => "Maaf, terjadi kesalahan saat mencoba mengambil riwayat reservasi Anda."]);
        }
    }

    // Helper function to format reservation cards (remains the same, minor tweaks)
    private function formatReservationCard($reservation)
    {
        // Ensure fields exist before accessing
        $reservationId = htmlspecialchars($reservation['id'] ?? 'N/A');
        $date = isset($reservation['request_date']) ? date('d F Y', strtotime($reservation['request_date'])) : 'Tanggal tidak tersedia';
        $endDate = isset($reservation['request_date_end']) && !empty($reservation['request_date_end']) ? " - " . date('d F Y', strtotime($reservation['request_date_end'])) : "";
        $totalPrice = isset($reservation['total_price']) ? number_format($reservation['total_price'], 0, ',', '.') : 'Harga tidak tersedia';
        $numberPeople = $reservation['number_people'] ?? '-';
        $statusId = $reservation['id_reservation_status'] ?? 0;


        // Get status text and icon
        $statusInfo = $this->getStatusInfo($statusId);

        // Determine if it's a package or homestay reservation
        $type = !empty($reservation['id_package']) ? 'package' : (!empty($reservation['id_homestay']) ? 'homestay' : 'unknown');
        $typeIcon = ($type == 'package') ? '🎫' : (($type == 'homestay') ? '🏠' : '❓');
        $typeName = ($type == 'package') ? 'Paket Wisata' : (($type == 'homestay') ? 'Homestay' : 'Jenis Tidak Diketahui');
        $itemId = !empty($reservation['id_package']) ? $reservation['id_package'] : (!empty($reservation['id_homestay']) ? $reservation['id_homestay'] : null);

        // Get item name (requires modification to your model or separate query)
        $itemName = $itemId ? $this->getItemName($type, $itemId) : 'Item tidak ditemukan';

        $card = "<div class='reservation-card mb-3 p-3' style='border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9;'>";
        $card .= "<div class='d-flex justify-content-between align-items-center mb-2'>";
        $card .= "<h5 style='margin: 0;'>{$typeIcon} {$typeName}: <b>{$itemName}</b></h5>";
        $card .= "<span class='badge bg-{$statusInfo['class']}'>{$statusInfo['icon']} {$statusInfo['text']}</span>"; // Using Bootstrap badge classes for styling
        $card .= "</div>";
        $card .= "<ul style='list-style-type: none; padding-left: 0; margin-bottom: 0;'>"; // Adjusted padding and margin
        $card .= "<li>🔖 <b>Kode Booking:</b> {$reservationId}</li>";
        $card .= "<li>📅 <b>Tanggal:</b> {$date}{$endDate}</li>";
        if ($type !== 'unknown' && $type !== 'package') { // Only show number of people for homestay if applicable or unknown
            $card .= "<li>👥 <b>Jumlah Tamu:</b> {$numberPeople}</li>";
        } else if ($type === 'package') {
            $card .= "<li>👥 <b>Jumlah Peserta:</b> {$numberPeople}</li>";
        }
        $card .= "<li>💰 <b>Total Harga:</b> Rp {$totalPrice}</li>";
        $card .= "</ul>";
        $card .= "</div>";

        return $card;
    }

    private function getStatusInfo($statusId)
    {
        // Adjusted classes for Bootstrap 5 compatibility if used
        switch ($statusId) {
            case 1: // pending
                return [
                    'text' => 'Menunggu Konfirmasi',
                    'icon' => '⏳',
                    'class' => 'warning' // Bootstrap warning color
                ];
            case 2: // confirmed
                return [
                    'text' => 'Terkonfirmasi',
                    'icon' => '✅',
                    'class' => 'success' // Bootstrap success color
                ];
            case 3: // canceled
                return [
                    'text' => 'Dibatalkan',
                    'icon' => '❌',
                    'class' => 'danger' // Bootstrap danger color
                ];
            case 4: // paid
                return [
                    'text' => 'Terbayar',
                    'icon' => '💰',
                    'class' => 'success' // Paid can also be success
                ];
            case 5: // completed
                return [
                    'text' => 'Selesai',
                    'icon' => '🏁',
                    'class' => 'info' // Bootstrap info color
                ];
            default:
                return [
                    'text' => 'Status Tidak Diketahui',
                    'icon' => '❓',
                    'class' => 'secondary' // Bootstrap secondary color
                ];
        }
    }

    // Helper to get item name based on type and ID
    private function getItemName($type, $itemId)
    {
        try {
            if ($type == 'package') {
                $package = $this->modelPackage->find($itemId);
                return $package ? htmlspecialchars($package['name']) : 'Paket tidak ditemukan';
            } elseif ($type == 'homestay') {
                // Get Homestay record first to get id_rumah_gadang
                $homestay = $this->modelHomestay->find($itemId);
                if ($homestay && !empty($homestay['id_rumah_gadang'])) {
                    // Then get the Rumah Gadang name
                    $rumahGadang = $this->modelRumahGadang->find($homestay['id_rumah_gadang']);
                    return $rumahGadang ? htmlspecialchars($rumahGadang['name']) : 'Homestay tidak ditemukan (RG)';
                }
                return 'Homestay tidak ditemukan (HM)';
            }
            return 'Nama tidak tersedia';
        } catch (Exception $e) {
            // Log the error but return a default message to the user
            log_message('error', 'Error getting item name (type: ' . $type . ', id: ' . $itemId . '): ' . $e->getMessage());
            return 'Nama tidak tersedia';
        }
    }


    public function removePackageReservationAI($reservationId)
    {
        try {
            if (!$this->userId) {
                // Should be caught in handleFunctionCall, but kept as a safeguard
                throw new Exception("Mohon login untuk membatalkan reservasi.");
            }
            $user_id = $this->userId;

            // Find the reservation by ID and user ID
            $reservation = $this->modelReservation
                ->where('id', $reservationId)
                ->where('id_user', $user_id)
                ->first();

            if (!$reservation) {
                return $this->response->setJSON(["response" => "Maaf, reservasi dengan kode booking <b>{$reservationId}</b> tidak ditemukan atau bukan milik Anda."]);
            }

            // Check if the status allows cancellation (only pending status 1)
            if ($reservation['id_reservation_status'] != 1) {
                // Use a more informative message based on current status
                $statusInfo = $this->getStatusInfo($reservation['id_reservation_status']);
                return $this->response->setJSON(["response" => "Maaf, reservasi dengan kode booking <b>{$reservationId}</b> tidak dapat dibatalkan karena berstatus '{$statusInfo['text']}'. Pembatalan hanya bisa dilakukan pada reservasi yang masih 'Menunggu Konfirmasi'."]);
            }

            // Attempt to delete the reservation
            $deleted = $this->modelReservation->delete($reservationId);

            if ($deleted) {
                return $this->response->setJSON(["response" => "<span class='text-success'>✅ Berhasil membatalkan reservasi dengan kode booking <b>{$reservationId}</b>.</span>"]);
            } else {
                // This might happen if delete fails for an unexpected reason
                throw new Exception("Gagal membatalkan reservasi. Silakan coba lagi.");
            }
        } catch (Exception $e) {
            log_message('error', 'Exception in removePackageReservationAI: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            // Return the specific exception message to the user
            return $this->response->setJSON(["response" => "❌ " . $e->getMessage()]);
        }
    }

    // 🔥 Modified: Function to handle AI initiated homestay reservation
    // Now accepts arguments array and checks for missing info conversationally
    public function makeHomestayReservationAI($arguments)
    {
        try {
            if (!$this->userId) {
                // Use a conversational tone for the response
                return $this->response->setJSON([
                    "response" => "Mohon maaf, Anda perlu login terlebih dahulu untuk bisa melakukan pemesanan homestay."
                ]);
            }
            $user_id = $this->userId;

            $rumahGadangId = $arguments['rumahGadangId'] ?? null;
            $rumahGadangOrHomestayName = $arguments['rumahGadangOrHomestayName'] ?? null;
            $requestDate = $arguments['requestDate'] ?? null; // Check-in date
            $requestDateEnd = $arguments['requestDateEnd'] ?? null; // Check-out date
            $numberPeople = $arguments['numberPeople'] ?? null; // Raw value

            // 1. Identify the homestay (via Rumah Gadang linked to a homestay)
            $rumahGadang = null; // This represents the Rumah Gadang entry
            $homestay = null; // This represents the Homestay entry

            if (!empty($rumahGadangId)) {
                // Find the Rumah Gadang first
                $rumahGadang = $this->modelRumahGadang->find($rumahGadangId);
                if ($rumahGadang && !empty($rumahGadang['id_homestay'])) {
                    // If found and linked to a homestay, get the homestay details
                    $homestay = $this->modelHomestay->find($rumahGadang['id_homestay']);
                }
            } elseif (!empty($rumahGadangOrHomestayName)) {
                // Try finding by exact name first, then fuzzy, filtering for those with homestay links

                $rumahGadang = $this->modelRumahGadang
                    ->where('id_homestay IS NOT NULL', null, false)
                    ->groupStart()
                    ->like('name', $rumahGadangOrHomestayName, 'both') // Lebih toleran
                    ->orLike('LOWER(name)', strtolower($rumahGadangOrHomestayName), 'both') // Jaga-jaga case-sensitive
                    ->groupEnd()
                    ->first();


                if ($rumahGadang && !empty($rumahGadang['id_homestay'])) {
                    $homestay = $this->modelHomestay->find($rumahGadang['id_homestay']);
                }
            }

            if (!$rumahGadang || !$homestay) {
                // If homestay not found or not listed as bookable
                return $this->response->setJSON([
                    "response" => "Maaf, homestay atau Rumah Gadang yang Anda maksud tidak ditemukan dalam daftar homestay yang bisa dipesan. Anda bisa melihat daftar homestay yang tersedia dengan mengetik 'Daftar homestay'."
                ]);
            }

            $rumahGadang_name = $rumahGadang['name'];
            $homestay_id = $homestay['id'];
            $price = $homestay['ticket_price'] ?? 0; // Assuming ticket_price is the nightly rate


            // 2. Check for missing dates and number of people sequentially
            // Check for missing check-in date
            if (empty($requestDate)) {
                // Ask for check-in date conversationally
                session()->set('booking_homestay_temp', [
                    'rumahGadangId' => $rumahGadang['id'], // Use RG ID to identify the homestay
                ]);
                return $this->response->setJSON([
                    "response" => "Baik, homestay <b>{$rumahGadang_name}</b>. Untuk tanggal berapa Anda ingin check-in?"
                ]);
            }

            // Check for missing check-out date
            if (empty($requestDateEnd)) {
                // Store RG ID and check-in date temporarily
                session()->set('booking_homestay_temp', [
                    'rumahGadangId' => $rumahGadang['id'],
                    'requestDate' => $requestDate, // Keep the user's input date format for the prompt
                ]);
                return $this->response->setJSON([
                    "response" => "Tanggal check-in <b>{$requestDate}</b>. Tanggal check-out-nya kapan?"
                ]);
            }

            // Check for missing number of people
            if ($numberPeople === null || !is_numeric($numberPeople) || (int)$numberPeople <= 0) {
                // Store RG ID, dates temporarily
                session()->set('booking_homestay_temp', [
                    'rumahGadangId' => $rumahGadang['id'],
                    'requestDate' => $requestDate,
                    'requestDateEnd' => $requestDateEnd,
                ]);
                return $this->response->setJSON([
                    "response" => "Check-in tanggal <b>{$requestDate}</b> sampai <b>{$requestDateEnd}</b>. Berapa orang yang akan menginap?"
                ]);
            }

            // Ensure numberPeople is an integer
            $numberPeople = (int) $numberPeople;

            // 3. Validate date formats and values
            $checkinDateObj = null;
            $checkoutDateObj = null;
            $formatsToTry = ['Y-m-d', 'd-m-Y', 'Y/m/d', 'd/m/Y', 'd F Y', 'j F Y', 'd M Y', 'j M Y'];

            // Validate Check-in Date
            foreach ($formatsToTry as $format) {
                $checkinDateObj = DateTime::createFromFormat($format, $requestDate);
                if ($checkinDateObj && $checkinDateObj->format($format) === $requestDate) {
                    break;
                }
            }

            if (!$checkinDateObj) {
                // Ask for date again with clear format examples
                return $this->response->setJSON([
                    "response" => "Maaf, format tanggal check-in <b>{$requestDate}</b> tidak valid. Mohon gunakan format seperti YYYY-MM-DD (contoh: 2025-05-20) atau DD-MM-YYYY (contoh: 20-05-2025)."
                ]);
            }
            $formattedCheckinDate = $checkinDateObj->format('Y-m-d'); // Standardize format


            // Validate Check-out Date
            foreach ($formatsToTry as $format) {
                $checkoutDateObj = DateTime::createFromFormat($format, $requestDateEnd);
                if ($checkoutDateObj && $checkoutDateObj->format($format) === $requestDateEnd) {
                    break;
                }
            }

            if (!$checkoutDateObj) {
                // Ask for date again with clear format examples
                return $this->response->setJSON([
                    "response" => "Maaf, format tanggal check-out <b>{$requestDateEnd}</b> tidak valid. Mohon gunakan format seperti YYYY-MM-DD (contoh: 2025-05-22) atau DD-MM-YYYY (contoh: 22-05-2025)."
                ]);
            }
            $formattedCheckoutDate = $checkoutDateObj->format('Y-m-d'); // Standardize format


            // Check if check-in date is in the future (at least tomorrow)
            $today = new DateTime('today');
            if ($checkinDateObj <= $today) {
                return $this->response->setJSON([
                    "response" => "Tanggal check-in harus untuk hari esok atau setelahnya. Mohon pilih tanggal setelah tanggal " . $today->format('d F Y') . "."
                ]);
            }

            // Check if check-out date is after check-in date
            if ($checkoutDateObj <= $checkinDateObj) {
                return $this->response->setJSON([
                    "response" => "Tanggal check-out harus setelah tanggal check-in. Mohon periksa kembali tanggalnya."
                ]);
            }

            // Calculate number of nights
            $interval = $checkinDateObj->diff($checkoutDateObj);
            $numberOfNights = $interval->days;

            if ($numberOfNights <= 0) {
                return $this->response->setJSON([
                    "response" => "Durasi menginap harus minimal 1 malam. Mohon periksa kembali tanggal check-in dan check-out."
                ]);
            }


            // 4. Check Homestay Availability (Simplified check based on existing reservations)
            // This is a basic check: see if ANY reservation exists for this homestay
            // that overlaps with the requested dates. A real system needs proper availability logic.
            $overlappingReservation = $this->modelReservation
                ->where('id_homestay', $homestay_id)
                ->groupStart()
                ->where('request_date <=', $formattedCheckoutDate)
                ->where('request_date_end >=', $formattedCheckinDate)
                ->groupEnd()
                ->whereIn('id_reservation_status', [1, 2, 4]) // Consider pending, confirmed, paid as unavailable
                ->first();

            if ($overlappingReservation) {
                return $this->response->setJSON([
                    "response" => "Maaf, homestay <b>{$rumahGadang_name}</b> tidak tersedia pada rentang tanggal <b>" . date('d F Y', strtotime($formattedCheckinDate)) . " - " . date('d F Y', strtotime($formattedCheckoutDate)) . "</b>. Mungkin sudah terisi atau ada reservasi lain yang tumpang tindih." // You might want to fetch the dates of the conflicting reservation here
                ]);
            }

            // 5. Check Homestay Capacity (Assuming Homestay model has capacity)
            $homestayCapacity = $homestay['capacity'] ?? 0; // Add capacity field to homestay model if needed
            if ($homestayCapacity > 0 && $numberPeople > $homestayCapacity) {
                return $this->response->setJSON([
                    "response" => "Maaf, jumlah tamu ({$numberPeople} orang) melebihi kapasitas maksimal homestay <b>{$rumahGadang_name}</b> ({$homestayCapacity} orang)."
                ]);
            }


            // 6. Proceed with reservation if all checks pass
            $id = $this->modelReservation->get_new_id_api();
            $total_price = $numberOfNights * $price * $numberPeople; // Assuming price is per person per night
            // OR $total_price = $numberOfNights * $price; // If price is per night for the homestay regardless of people (up to capacity)
            // Adjust the total_price calculation based on your pricing model


            $reservationData = [
                'id' => $id,
                'id_user' => $user_id,
                'id_homestay' => $homestay_id,
                'request_date' => $formattedCheckinDate, // Use standardized format
                'request_date_end' => $formattedCheckoutDate, // Use standardized format
                'id_reservation_status' => 1, // pending status
                'number_people' => $numberPeople,
                'total_price' => $total_price,
                'created_at' => date('Y-m-d H:i:s'), // Add timestamps
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            // Using add_r_api
            $added = $this->modelReservation->add_r_api($reservationData);

            if (!$added) {
                throw new Exception("Gagal menyimpan data reservasi homestay.");
            }

            // Clear temporary state if booking was successful
            session()->remove('booking_homestay_temp');

            // Format confirmation message
            $formattedCheckin = date('d F Y', strtotime($formattedCheckinDate));
            $formattedCheckout = date('d F Y', strtotime($formattedCheckoutDate));
            $totalPriceFormatted = number_format($total_price, 0, ',', '.');

            return $this->response->setJSON([
                "response" => "<span class='text-success'>✅ Reservasi homestay <b>{$rumahGadang_name}</b> berhasil dibuat!</span><br><br>" .
                    "📋 <b>Detail Reservasi:</b><br>" .
                    "🔖 Kode Booking: <b>{$id}</b><br>" .
                    "🏠 Homestay: <b>{$rumahGadang_name}</b><br>" .
                    "👥 Jumlah Tamu: <b>{$numberPeople} orang</b><br>" .
                    "📅 Tanggal Menginap: <b>{$formattedCheckin}</b> sampai <b>{$formattedCheckout}</b> ({$numberOfNights} malam)<br>" .
                    "💰 Total Harga: <b>Rp {$totalPriceFormatted}</b><br><br>" .
                    "Silakan lakukan pembayaran sesuai petunjuk yang akan dikirimkan ke email Anda.<br>" .
                    "Untuk melihat reservasi Anda, ketik <b>'Lihat reservasi saya'</b>."
            ]);
        } catch (Exception $e) {
            log_message('error', 'Exception in makeHomestayReservationAI: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            // Return the specific exception message to the user
            return $this->response->setJSON(["response" => "❌ " . $e->getMessage()]);
        }
    }

    // Added a basic way to handle date formats beyond YYYY-MM-DD
    // You might need a more sophisticated date parsing helper
    private function parseFlexibleDate($dateString)
    {
        $formatsToTry = [
            'Y-m-d',       // 2025-04-28
            'd-m-Y',       // 28-04-2025
            'Y/m/d',       // 2025/04/28
            'd/m/Y',       // 28/04/2025
            'd F Y',       // 28 April 2025 (requires locale settings for month names)
            'j F Y',       // 28 April 2025
            'd M Y',       // 28 Apr 2025
            'j M Y',       // 28 Apr 2025
            'F d, Y',      // April 28, 2025
            'M d, Y',      // Apr 28, 2025
            // Add more formats if needed
        ];

        foreach ($formatsToTry as $format) {
            $dateObj = DateTime::createFromFormat($format, $dateString);
            // The second check ensures that the original string fully matched the format
            if ($dateObj && $dateObj->format($format) === $dateString) {
                return $dateObj;
            }
        }

        return false; // Return false if no format matches
    }
}
