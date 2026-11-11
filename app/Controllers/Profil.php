<?php

namespace App\Controllers;

use Modules\CivitasAkademika\Models\GuruStafModel;

class Profil extends BaseController
{
    protected GuruStafModel $guruStafModel;

    public function __construct()
    {
        $this->guruStafModel = new GuruStafModel();
    }

    /**
     * Halaman Pengaturan Akun & Profil Pengguna Sendiri
     */
    public function index()
    {
        $currentUser = auth()->user();
        if (!$currentUser) {
            return redirect()->to('login');
        }

        // Jika user adalah wali murid (dan bukan superadmin), arahkan ke portal wali murid
        if ($currentUser->inGroup('wali_murid') && !$currentUser->inGroup('superadmin', 'direksi')) {
            return redirect()->to(base_url('wali-murid/profil'));
        }

        // Jika user adalah wali kelas (dan bukan superadmin), arahkan ke portal guru
        if ($currentUser->inGroup('wali_kelas') && !$currentUser->inGroup('superadmin', 'direksi')) {
            return redirect()->to(base_url('wali-kelas/profil'));
        }

        // Dapatkan data guru/staf jika akun tertaut
        $guruProfile = $this->guruStafModel->where('user_id', $currentUser->id)->first();

        // Dapatkan grup/peran
        $groups = $currentUser->getGroups();
        $authGroupsConfig = config('AuthGroups')->groups ?? [];
        $groupTitles = [];
        foreach ($groups as $g) {
            $groupTitles[] = $authGroupsConfig[$g]['title'] ?? ucfirst($g);
        }

        $data = [
            'title'        => 'Profil & Pengaturan Akun Saya',
            'currentUser'  => $currentUser,
            'guruProfile'  => $guruProfile,
            'groups'       => $groups,
            'groupTitles'  => $groupTitles,
        ];

        return view('profil/index', $data);
    }

    /**
     * Update Informasi Akun (Username & Email)
     */
    public function updateAkun()
    {
        $currentUser = auth()->user();
        if (!$currentUser) {
            return redirect()->to('login');
        }

        $username = trim($this->request->getPost('username'));
        $email    = trim($this->request->getPost('email'));

        $rules = [
            'username' => "required|min_length[3]|max_length[50]|is_unique[users.username,id,{$currentUser->id}]",
            'email'    => "required|valid_email",
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors()[0] ?? 'Data tidak valid.');
        }

        // Validasi keunikan email pada tabel auth_identities
        $db = \Config\Database::connect();
        $existingEmail = $db->table('auth_identities')
            ->where('type', 'email_password')
            ->where('secret', $email)
            ->where('user_id !=', $currentUser->id)
            ->countAllResults();

        if ($existingEmail > 0) {
            return redirect()->back()->withInput()->with('error', 'Alamat email tersebut sudah digunakan oleh akun lain.');
        }

        try {
            $usersProvider = auth()->getProvider();
            $currentUser->fill([
                'username' => $username,
                'email'    => $email,
            ]);
            $usersProvider->save($currentUser);

            // Perbarui secret di auth_identities
            $db->table('auth_identities')
                ->where('user_id', $currentUser->id)
                ->where('type', 'email_password')
                ->update(['secret' => $email]);

            return redirect()->back()->with('success', 'Informasi akun berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui akun: ' . $e->getMessage());
        }
    }

    /**
     * Update Password Akun Pengguna
     */
    public function updatePassword()
    {
        $currentUser = auth()->user();
        if (!$currentUser) {
            return redirect()->to('login');
        }

        $rules = [
            'password_sekarang'   => 'required',
            'password_baru'       => 'required|min_length[6]',
            'konfirmasi_password' => 'required|matches[password_baru]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors()[0] ?? 'Form tidak valid.');
        }

        $passwordSekarang = $this->request->getPost('password_sekarang');
        $passwordBaru     = $this->request->getPost('password_baru');

        // Validasi kecocokan password lama
        $credentials = [
            'email'    => $currentUser->email,
            'password' => $passwordSekarang,
        ];

        $valid = auth()->check($credentials);
        if (!$valid->isOK()) {
            return redirect()->back()->with('error', 'Password saat ini yang Anda masukkan salah.');
        }

        try {
            $usersProvider = auth()->getProvider();
            $currentUser->fill(['password' => $passwordBaru]);
            $usersProvider->save($currentUser);

            // Perbarui juga initial_password di tabel users
            \Config\Database::connect()->table('users')->where('id', $currentUser->id)->update(['initial_password' => $passwordBaru]);

            return redirect()->back()->with('success', 'Kata sandi berhasil diperbarui! Gunakan kata sandi baru untuk login berikutnya.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui kata sandi: ' . $e->getMessage());
        }
    }
}

