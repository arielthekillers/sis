<?php

namespace Modules\CivitasAkademika\Controllers;

use App\Controllers\BaseController;
use Modules\CivitasAkademika\Models\SiswaModel;

class Siswa extends BaseController
{
    protected $siswaModel;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Data Siswa',
            'siswa' => $this->siswaModel->orderBy('nama_lengkap', 'ASC')->paginate(10, 'siswa'),
            'pager' => $this->siswaModel->pager
        ];
        return view('Modules\CivitasAkademika\Views\siswa\index', $data);
    }

    public function save()
    {
        $id = $this->request->getPost('id');
        
        $rules = [
            'nama_lengkap'  => 'required',
            'jenis_kelamin' => 'required|in_list[L,P]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Data tidak valid. Silakan periksa kembali form Anda.');
        }

        $saveData = [
            'nis'            => $this->request->getPost('nis') ?: null,
            'nisn'           => $this->request->getPost('nisn') ?: null,
            'nama_lengkap'   => $this->request->getPost('nama_lengkap'),
            'nama_panggilan' => $this->request->getPost('nama_panggilan') ?: null,
            'jenis_kelamin'  => $this->request->getPost('jenis_kelamin'),
            'tempat_lahir'   => $this->request->getPost('tempat_lahir') ?: null,
            'tanggal_lahir'  => $this->request->getPost('tanggal_lahir') ?: null,
            'agama'          => $this->request->getPost('agama') ?: null,
            'alamat'         => $this->request->getPost('alamat') ?: null,
            'nama_ayah'      => $this->request->getPost('nama_ayah') ?: null,
            'nama_ibu'       => $this->request->getPost('nama_ibu') ?: null,
            'no_hp_ortu'     => $this->request->getPost('no_hp_ortu') ?: null,
            'status_siswa'   => $this->request->getPost('status_siswa') ?: 'Aktif',
            'program'        => $this->request->getPost('program') ?: 'Reguler',
            'is_active'      => $this->request->getPost('is_active') !== null ? 1 : 0
        ];

        // Handle Image Upload
        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $uploader = new \App\Libraries\ImageUploader('uploads/profiles/');
            $result = $uploader->process($foto, true, ['thumb' => 150, 'md' => 500]);
            
            if ($result['status'] === 'success') {
                $saveData['foto'] = $result['files']['original'];
            } else {
                return redirect()->back()->withInput()->with('error', 'Gagal mengunggah foto: ' . $result['message']);
            }
        }

        if ($id) {
            $this->siswaModel->update($id, $saveData);
            $message = 'Data Siswa berhasil diperbarui.';
        } else {
            $this->siswaModel->insert($saveData);
            $message = 'Data Siswa berhasil ditambahkan.';
        }

        return redirect()->to('civitas-akademika/siswa')->with('success', $message);
    }

    public function delete($id)
    {
        $this->siswaModel->delete($id);
        return redirect()->to('civitas-akademika/siswa')->with('success', 'Data Siswa berhasil dihapus.');
    }

    public function profil($id)
    {
        $siswa = $this->siswaModel->find($id);
        if (!$siswa) {
            return redirect()->to('civitas-akademika/siswa')->with('error', 'Siswa tidak ditemukan.');
        }

        $historyKelas = [];
        try {
            $db = \Config\Database::connect();
            $historyKelas = $db->table('rombel_siswa')
                ->select('rombel_siswa.*, rombel.nama_rombel, tahun_ajaran.tahun, tahun_ajaran.semester')
                ->join('rombel', 'rombel.id = rombel_siswa.rombel_id', 'left')
                ->join('tahun_ajaran', 'tahun_ajaran.id = rombel.tahun_ajaran_id', 'left')
                ->where('rombel_siswa.siswa_id', $id)
                ->orderBy('tahun_ajaran.id', 'DESC')
                ->get()->getResultArray();
        } catch (\Exception $e) {}

        $historyPembayaran = [];
        try {
            $db = \Config\Database::connect();
            $historyPembayaran = $db->table('keu_pembayaran')
                ->where('siswa_id', $id)
                ->orderBy('tanggal_bayar', 'DESC')
                ->get()->getResultArray();
        } catch (\Exception $e) {}

        $orangTuaList = [];
        try {
            $db = \Config\Database::connect();
            $orangTuaList = $db->table('orang_tua_siswa')
                ->select('orang_tua_siswa.*, users.username, users.initial_password, auth_identities.secret as email')
                ->join('users', 'users.id = orang_tua_siswa.user_id')
                ->join('auth_identities', 'auth_identities.user_id = users.id AND auth_identities.type = "email_password"', 'left')
                ->where('orang_tua_siswa.siswa_id', $id)
                ->where('orang_tua_siswa.deleted_at IS NULL')
                ->get()->getResultArray();

            // Pastikan semua akun orang tua memiliki initial_password
            $usersProvider = auth()->getProvider();
            foreach ($orangTuaList as &$otItem) {
                if (empty($otItem['initial_password'])) {
                    $pool = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ@#';
                    $autoPassword = substr(str_shuffle(str_repeat($pool, 4)), 0, 8);
                    $uObj = $usersProvider->findById($otItem['user_id']);
                    if ($uObj) {
                        $uObj->fill(['password' => $autoPassword]);
                        $usersProvider->save($uObj);
                    }
                    $db->table('users')->where('id', $otItem['user_id'])->update(['initial_password' => $autoPassword]);
                    $otItem['initial_password'] = $autoPassword;
                }
            }
            unset($otItem);
        } catch (\Exception $e) {}

        $perkembanganModel = new \Modules\CivitasAkademika\Models\PerkembanganSiswaModel();
        $perkembanganList = $perkembanganModel->getBySiswa($id, false);

        $userModel = new \CodeIgniter\Shield\Models\UserModel();
        $availableOrtuUsers = $userModel
            ->select('users.*, auth_identities.secret as email')
            ->join('auth_groups_users', 'auth_groups_users.user_id = users.id')
            ->join('auth_identities', 'auth_identities.user_id = users.id AND auth_identities.type = "email_password"', 'left')
            ->where('auth_groups_users.group', 'wali_murid')
            ->findAll();

        $data = [
            'title'              => 'Profil Siswa',
            'siswa'              => $siswa,
            'historyKelas'       => $historyKelas,
            'historyPembayaran'  => $historyPembayaran,
            'orangTuaList'       => $orangTuaList,
            'perkembanganList'   => $perkembanganList,
            'availableOrtuUsers' => $availableOrtuUsers
        ];

        return view('Modules\CivitasAkademika\Views\siswa\profil', $data);
    }

    public function buatDanTautkanOrangTua()
    {
        $siswaId = (int) $this->request->getPost('siswa_id');
        $siswa = $this->siswaModel->find($siswaId);
        if (!$siswa) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan.');
        }

        $rules = [
            'username' => 'required|min_length[3]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[auth_identities.secret]',
            'hubungan' => 'required|in_list[ayah,ibu,wali]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors()[0] ?? 'Data tidak valid.');
        }

        $username = trim($this->request->getPost('username'));
        $email    = trim($this->request->getPost('email'));
        $hubungan = $this->request->getPost('hubungan');
        $password = $this->request->getPost('password');

        // Jika password kosong, otomatis buatkan password acak ramah baca (8 karakter)
        if (empty($password)) {
            $pool = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ@#';
            $password = substr(str_shuffle(str_repeat($pool, 4)), 0, 8);
        }

        $usersProvider = auth()->getProvider();
        $user = new \CodeIgniter\Shield\Entities\User([
            'username' => $username,
            'email'    => $email,
            'password' => $password,
        ]);

        try {
            $usersProvider->save($user);
            $userId = $usersProvider->getInsertID();
            \Config\Database::connect()->table('users')->where('id', $userId)->update(['initial_password' => $password]);
            $savedUser = $usersProvider->findById($userId);
            $savedUser->activate();
            $savedUser->addGroup('wali_murid');

            // Tautkan ke siswa
            $orangTuaModel = new \Modules\CivitasAkademika\Models\OrangTuaSiswaModel();
            $orangTuaModel->insert([
                'siswa_id' => $siswaId,
                'user_id'  => $userId,
                'hubungan' => $hubungan
            ]);

            $accountInfo = [
                'username'   => $username,
                'email'      => $email,
                'password'   => $password,
                'hubungan'   => ucfirst($hubungan),
                'nama_siswa' => $siswa['nama_lengkap'],
                'no_hp_ortu' => $siswa['no_hp_ortu'] ?? '',
                'login_url'  => base_url('login')
            ];

            return redirect()->to('civitas-akademika/siswa/profil/' . $siswaId)
                ->with('success', 'Akun orang tua berhasil dibuat dan langsung ditautkan!')
                ->with('new_account', $accountInfo);

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat akun: ' . $e->getMessage());
        }
    }

    public function tautkanOrangTua()
    {
        $siswaId = $this->request->getPost('siswa_id');
        $userId = $this->request->getPost('user_id');
        $hubungan = $this->request->getPost('hubungan') ?: 'wali';

        if (!$siswaId || !$userId) {
            return redirect()->back()->with('error', 'Pilih akun orang tua yang valid.');
        }

        $orangTuaModel = new \Modules\CivitasAkademika\Models\OrangTuaSiswaModel();
        $existing = $orangTuaModel->where('siswa_id', $siswaId)->where('user_id', $userId)->first();
        if ($existing) {
            return redirect()->back()->with('warning', 'Akun orang tua ini sudah terhubung ke siswa ini.');
        }

        $orangTuaModel->insert([
            'siswa_id' => $siswaId,
            'user_id'  => $userId,
            'hubungan' => $hubungan
        ]);

        return redirect()->back()->with('success', 'Akun orang tua berhasil ditautkan ke ananda.');
    }

    public function resetPasswordOrtu()
    {
        $siswaId = (int) $this->request->getPost('siswa_id');
        $userId = (int) $this->request->getPost('user_id');
        $password = $this->request->getPost('password');

        $siswa = $this->siswaModel->find($siswaId);
        if (!$siswa) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan.');
        }

        if (empty($password)) {
            $pool = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ@#';
            $password = substr(str_shuffle(str_repeat($pool, 4)), 0, 8);
        }

        if (strlen($password) < 8) {
            return redirect()->back()->with('error', 'Password minimal 8 karakter.');
        }

        $usersProvider = auth()->getProvider();
        $user = $usersProvider->findById($userId);
        if (!$user) {
            return redirect()->back()->with('error', 'Akun pengguna tidak ditemukan.');
        }

        try {
            $user->fill(['password' => $password]);
            $usersProvider->save($user);
            \Config\Database::connect()->table('users')->where('id', $userId)->update(['initial_password' => $password]);

            $userEmail = '';
            $identity = $user->getEmailIdentity();
            if ($identity) {
                $userEmail = $identity->secret;
            }

            $orangTuaModel = new \Modules\CivitasAkademika\Models\OrangTuaSiswaModel();
            $rel = $orangTuaModel->where('siswa_id', $siswaId)->where('user_id', $userId)->first();
            $hubungan = $rel ? ucfirst($rel['hubungan']) : 'Orang Tua / Wali';

            $accountInfo = [
                'username'   => $user->username,
                'email'      => $userEmail,
                'password'   => $password,
                'hubungan'   => $hubungan,
                'nama_siswa' => $siswa['nama_lengkap'],
                'no_hp_ortu' => $siswa['no_hp_ortu'] ?? '',
                'login_url'  => base_url('login')
            ];

            return redirect()->to('civitas-akademika/siswa/profil/' . $siswaId)
                ->with('success', 'Password akun orang tua berhasil direset!')
                ->with('new_account', $accountInfo);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mereset password: ' . $e->getMessage());
        }
    }

    public function hapusTautanOrangTua($id)
    {
        $orangTuaModel = new \Modules\CivitasAkademika\Models\OrangTuaSiswaModel();
        $orangTuaModel->delete($id);
        return redirect()->back()->with('success', 'Tautan akun orang tua berhasil dilepas.');
    }
}
