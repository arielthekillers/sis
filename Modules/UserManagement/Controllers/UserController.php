<?php

namespace Modules\UserManagement\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Shield\Models\UserModel;

class UserController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        // Ambil semua pengguna beserta grup/role-nya
        $users = $this->userModel->orderBy('created_at', 'DESC')->paginate(10);
        $pager = $this->userModel->pager;

        // Ambil daftar grup dari konfigurasi Shield
        $authGroups = config('AuthGroups')->groups;

        $data = [
            'users' => $users,
            'pager' => $pager,
            'available_groups' => $authGroups,
        ];

        return view('Modules\UserManagement\Views\index', $data);
    }

    public function store()
    {
        // Pastikan user punya hak akses (Keamanan Ganda)
        if (! auth()->user()->can('users.create')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk menambah pengguna.');
        }

        $rules = [
            'username' => 'required|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[auth_identities.secret]',
            'password' => 'required|min_length[8]',
            'group'    => 'required'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors()[0] ?? 'Data tidak valid.');
        }

        // Buat entitas User Shield
        $usersProvider = auth()->getProvider();
        $user = new \CodeIgniter\Shield\Entities\User([
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
        ]);

        try {
            $usersProvider->save($user);
            $userId = $usersProvider->getInsertID();
            $savedUser = $usersProvider->findById($userId);
            
            // Aktifkan akun agar bisa langsung dipakai login
            $savedUser->activate();

            // Tambahkan ke dalam grup (Role)
            $group = $this->request->getPost('group');
            $savedUser->addGroup($group);

            return redirect()->to('/users')->with('success', 'Akun pengguna berhasil dibuat.');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function update()
    {
        if (! auth()->user()->can('users.edit')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk mengubah pengguna.');
        }

        $id = $this->request->getPost('id');
        $usersProvider = auth()->getProvider();
        $user = $usersProvider->findById($id);

        if (!$user) {
            return redirect()->back()->with('error', 'Pengguna tidak ditemukan.');
        }

        $rules = [
            'username' => "required|is_unique[users.username,id,{$id}]",
            'email'    => "required|valid_email", // Should ideally check unique identity
            'group'    => 'required'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors()[0] ?? 'Data tidak valid.');
        }

        $user->fill([
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
        ]);

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            if (strlen($password) < 8) {
                return redirect()->back()->withInput()->with('error', 'Password minimal 8 karakter.');
            }
            $user->fill(['password' => $password]);
        }

        try {
            $usersProvider->save($user);

            // Update group
            $newGroup = $this->request->getPost('group');
            $currentGroups = $user->getGroups();
            
            // Remove old groups and add new one
            foreach ($currentGroups as $g) {
                $user->removeGroup($g);
            }
            $user->addGroup($newGroup);

            return redirect()->to('/users')->with('success', 'Data pengguna berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        if (! auth()->user()->can('users.delete')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk menghapus pengguna.');
        }

        $usersProvider = auth()->getProvider();
        $user = $usersProvider->findById($id);

        if (!$user) {
            return redirect()->back()->with('error', 'Pengguna tidak ditemukan.');
        }

        try {
            $usersProvider->delete($id, true); // true for force delete
            return redirect()->to('/users')->with('success', 'Pengguna berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}
