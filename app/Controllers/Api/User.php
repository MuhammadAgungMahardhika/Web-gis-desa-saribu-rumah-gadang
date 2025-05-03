<?php

namespace App\Controllers\Api;

use App\Models\AccountModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class User extends ResourceController
{

    use ResponseTrait;

    protected $accountModel;

    public function __construct()
    {
        $this->accountModel = new AccountModel();
    }

    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $contents = $this->accountModel->get_list_user_api()->getResult();
        $response = [
            'data' => $contents,
            'status' => 200,
            'message' => [
                "Success get list of User"
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
        $contents = $this->accountModel->get_account_by_id_api($id)->getRowArray();
        $response = [
            'data' => $contents,
            'status' => 200,
            'message' => [
                "Success display detail information of User"
            ]
        ];
        return $this->respond($response);
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

    public function register()
    {
        // Ambil semua data dari query string GET
        $request = $this->request->getGet();

        // Validasi sederhana (Idealnya gunakan Validation Library CodeIgniter)
        // Pastikan field yang dibutuhkan ada di query string
        $requiredFields = ['email', 'username', 'password']; // Hanya field ini yang dibutuhkan
        foreach ($requiredFields as $field) {
            if (empty($request[$field])) {
                return $this->failValidationError("Missing required field in query string: " . $field);
            }
        }

        // Siapkan data untuk dimasukkan ke database
        // Hanya ambil field yang relevan
        $requestData = [
            'id'         => $this->accountModel->get_new_id_api(), // Dapatkan ID baru
            'email'      => $request['email'] ?? null,
            'username'   => $request['username'] ?? null,
            'password'   => $request['password'] ?? null, // Password asli dari input
            // Field lain seperti first_name, last_name, dll. akan bernilai default database atau NULL
        ];

        // Lakukan hashing password sebelum menyimpan (SANGAT PENTING!)
        if (!empty($requestData['password'])) {
            // Hash password menggunakan fungsi bawaan PHP yang aman
            $requestData['password'] = password_hash($requestData['password'], PASSWORD_DEFAULT);
            // Pastikan kolom password di database Anda cukup panjang (VARCHAR 255 direkomendasikan)
        } else {
            // Jika password kosong padahal required, validasi di atas seharusnya sudah menangani
            // Tapi bisa ditambahkan penanganan error tambahan di sini jika perlu.
            return $this->failValidationError("Password is required.");
        }


        // Coba masukkan data ke database
        try {
            // Pastikan model Anda mengizinkan field ini untuk diisi (cek $allowedFields di Model)
            if ($this->accountModel->insert($requestData)) {
                // Jika berhasil, siapkan respons sukses
                $insertedId = $this->accountModel->getInsertID(); // Dapatkan ID yang baru saja dimasukkan
                $response = [
                    'status'  => 201, // 201 Created
                    'message' => "Success: New user created ",
                    'data'    => [ // Kembalikan data yang relevan (tanpa password)
                        'id' => $insertedId, // Gunakan ID yang sebenarnya dari database
                        'email' => $request['email'], // Kembalikan email asli
                        'username' => $request['username'] // Kembalikan username asli
                    ]
                ];
                return $this->respondCreated($response); // Kembalikan respons 201 Created
            } else {
                // Jika insert gagal karena alasan lain (misal error validasi model)
                $errors = $this->accountModel->errors(); // Ambil pesan error dari model jika ada
                return $this->failValidationErrors($errors ?: ['error' => 'Failed to create user.']);
            }
        } catch (\Exception $e) {
            // Tangani jika ada exception saat insert (misal: constraint database unik email/username)
            log_message('error', '[ERROR] createUserWithGet: ' . $e->getMessage());
            // Berikan pesan error yang lebih spesifik jika memungkinkan
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return $this->failResourceExists('Email or username already exists.'); // 409 Conflict
            }
            return $this->failServerError('An unexpected error occurred while creating the user.'); // 500 Internal Server Error
        }
    }
    public function create()
    {
        $request = $this->request->getPost();
        $requestData = [
            'id' => $this->accountModel->get_new_id_api(),
            'username' => $request['username'],
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'email' => $request['email'],
            'address' => $request['address'],
            'phone' => $request['phone'],
            'password' => $request['password'],
            'avatar' => $request['avatar'],
            'role_id' => $request['role_id'],
        ];
        $this->accountModel->insert($requestData);
        $response = [
            'status' => 201,
            'message' => [
                "Success create new Users"
            ]
        ];
        return $this->respondCreated($response);
    }

    public function createUserWithGet()
    {
        // Ambil semua data dari query string GET
        $request = $this->request->getGet();

        // Validasi sederhana (Idealnya gunakan Validation Library CodeIgniter)
        // Pastikan semua field yang dibutuhkan ada di query string
        $requiredFields = ['username', 'first_name', 'last_name', 'email', 'password', 'role_id'];
        foreach ($requiredFields as $field) {
            if (empty($request[$field])) {
                return $this->failValidationError("Missing required field in query string: " . $field);
            }
        }

        // Siapkan data untuk dimasukkan ke database
        // Ambil nilai dari $request, berikan default jika tidak ada (meskipun sudah divalidasi)
        $requestData = [
            'id'         => $this->accountModel->get_new_id_api(), // Dapatkan ID baru
            'username'   => $request['username'] ?? null,
            'first_name' => $request['first_name'] ?? null,
            'last_name'  => $request['last_name'] ?? null,
            'email'      => $request['email'] ?? null,
            'address'    => $request['address'] ?? null, // Field opsional?
            'phone'      => $request['phone'] ?? null,   // Field opsional?
            'password'   => $request['password'] ?? null, // !!! Password di URL sangat tidak aman !!!
            'avatar'     => $request['avatar'] ?? null,    // Field opsional?
            'role_id'    => $request['role_id'] ?? null,
        ];

        // Lakukan hashing password sebelum menyimpan (SANGAT PENTING!)
        // Pastikan Anda memiliki mekanisme hashing yang aman. Contoh sederhana:
        if (!empty($requestData['password'])) {
            // Ganti dengan metode hashing yang aman sesuai framework/library Anda
            // Contoh: password_hash($requestData['password'], PASSWORD_DEFAULT);
            // Jangan simpan password sebagai plain text!
            // Untuk contoh ini, kita asumsikan model atau logic lain menghandle hashing.
            // Jika tidak, HASH DI SINI!
            // $requestData['password'] = password_hash($requestData['password'], PASSWORD_DEFAULT);
        }


        // Coba masukkan data ke database
        try {
            if ($this->accountModel->insert($requestData)) {
                // Jika berhasil, siapkan respons sukses
                $response = [
                    'status'  => 201, // 201 Created
                    'message' => "Success: New user created using GET (Not Recommended).",
                    'data'    => $requestData // Opsional: kembalikan data yang dibuat (tanpa password)
                ];
                // Hapus password dari data respons sebelum dikirim
                unset($response['data']['password']);
                return $this->respondCreated($response); // Kembalikan respons 201 Created
            } else {
                // Jika insert gagal karena alasan lain (misal error model)
                return $this->failServerError('Failed to create user due to a server error.');
            }
        } catch (\Exception $e) {
            // Tangani jika ada exception saat insert (misal: constraint database)
            log_message('error', 'Error creating user via GET: ' . $e->getMessage());
            return $this->failServerError('An unexpected error occurred while creating the user.');
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
        $requestData = [
            'username' => $request['username'],
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'email' => $request['email'],
            'address' => $request['address'],
            'phone' => $request['phone'],
            'password' => $request['password'],
            'avatar' => $request['avatar'],
        ];
        $updateAccount = $this->accountModel->update_account_api($id, $requestData);
        if ($updateAccount) {
            $response = [
                'status' => 200,
                'message' => [
                    "Success update User"
                ]
            ];
            return $this->respond($response);
        } else {
            $response = [
                'status' => 400,
                'message' => [
                    "Fail update User"
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
        $delete = $this->accountModel->delete_user_api($id);
        if ($delete) {
            $response = [
                'status' => 200,
                'message' => [
                    "Success delete User"
                ]
            ];
            return $this->respondDeleted($response);
        } else {
            $response = [
                'status' => 404,
                'message' => [
                    "User not found"
                ]
            ];
            return $this->failNotFound($response);
        }
    }

    public function owner()
    {
        $contents = $this->accountModel->get_list_owner_api()->getResult();
        $response = [
            'data' => $contents,
            'status' => 200,
            'message' => [
                "Success get list of Owner"
            ]
        ];
        return $this->respond($response);
    }
}
