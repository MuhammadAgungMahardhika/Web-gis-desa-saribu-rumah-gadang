<?php

namespace App\Controllers\Api;

use App\Models\AccountModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use Myth\Auth\Password;
use Config\Database;

class User extends ResourceController
{

    use ResponseTrait;

    protected $accountModel;
    protected $db;
    public function __construct()
    {
        $this->accountModel = new AccountModel();
        $this->db = Database::connect();
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
        // Ambil data dari request
        $request = $this->request->getPost();

        // Validasi data (penting untuk keamanan dan integritas)
        $validationRules = [
            // Menggunakan validasi default CI4 atau Shield
            // Pastikan rule is_unique dan valid_email sudah terdaftar di Validation.php atau via Services::validation()->setRules()
            'username' => 'required|is_unique[users.username]', // Pastikan username unik
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|valid_email|is_unique[users.email]', // Pastikan email unik dan valid
            'password' => 'required|min_length[8]', // Password minimal 8 karakter
            'confirm_password' => 'required|matches[password]', // Pastikan konfirmasi password sama
        ];

        if (!$this->validate($validationRules)) {
            $response = [
                'status' => 400, // Kode status HTTP untuk Bad Request
                'message' => $this->validator->getErrors(), // Mengembalikan pesan error validasi
            ];
            return $this->respond($response, 400); // Mengirim response dengan kode status 400
        }

        // Hash password sebelum disimpan (sangat penting untuk keamanan)
        // Jika menggunakan Shield, Anda bisa langsung membuat User Entity dan Shield akan handle hashing saat save
        $passwordHash = Password::hash($request['password']); // Jika tidak pakai Shield Entity

        // Siapkan data untuk dimasukkan ke database
        // Jika menggunakan Shield Entity, prosesnya akan sedikit berbeda
        $requestData = [
            'username' => $request['username'],
            'first_name' => $request['first_name'], // Sesuaikan nama kolom di tabel users Anda
            'last_name' => $request['last_name'], // Sesuaikan nama kolom di tabel users Anda
            'email' => $request['email'],
            'password_hash' => $passwordHash, // Simpan hash, bukan password asli (Jika tidak pakai Shield Entity)
            'active' => true // Sesuaikan dengan kolom di tabel users Anda
            // Tambahkan kolom lain yang relevan dari tabel users
        ];


        // Jika TIDAK menggunakan Shield Entity dan langsung pakai Model insert:
        if (!$this->accountModel->insert($requestData)) {
            // Tangani error jika insert gagal
            log_message('error', 'Failed to insert user: ' . print_r($this->accountModel->errors(), true));
            return $this->respond(['status' => 500, 'message' => 'Failed to create user.'], 500);
        }
        $newUserId = $this->accountModel->getInsertID();


        // Siapkan data untuk dimasukkan ke tabel auth_groups_users
        $roleId = 2; // ID role default, bisa diambil dari request jika ada
        $groupUserData = [
            'user_id'  => $newUserId,
            'group_id' => $roleId
        ];

        // Lakukan insert ke tabel auth_groups_users MENGGUNAKAN INSTANCE DATABASE DEFAULT ($this->db)
        // TIDAK perlu memanggil \Config\Database::connect() lagi di sini
        // TIDAK perlu memanggil $db->close() karena framework yang mengelolanya
        if (!$this->db->table('auth_groups_users')->insert($groupUserData)) {
            log_message('error', 'Failed to assign group to user ID: ' . $newUserId);
            // Opsional: Hapus user yang baru dibuat jika gagal assign role, agar data konsisten
            // $this->accountModel->delete($newUserId);
            return $this->respond(['status' => 500, 'message' => 'Failed to assign role to user.'], 500);
        }

        // --- Proses berhasil ---
        $response = [
            'status' => 201, // Kode status HTTP untuk Created
            'message' => "Success create new User",
            'data' => [
                'id' => $newUserId,
                // Tambahkan data lain yang relevan untuk response
            ]
        ];
        return $this->respondCreated($response); // Gunakan helper function untuk response 201

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
