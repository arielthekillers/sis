<?php

namespace Modules\CivitasAkademika\Controllers;

use App\Controllers\BaseController;
use Modules\CivitasAkademika\Models\GuruStafModel;

class GuruStaf extends BaseController
{
    protected $guruStafModel;

    public function __construct()
    {
        $this->guruStafModel = new GuruStafModel();
    }

    public function index()
    {
        $userModel = new \CodeIgniter\Shield\Models\UserModel();
        $availableUsers = $userModel
            ->select('users.*, auth_identities.secret as email')
            ->join('auth_identities', 'auth_identities.user_id = users.id AND auth_identities.type = "email_password"', 'left')
            ->findAll();

        $data = [
            'title'          => 'Data Guru & Staf',
            'guru_staf'      => $this->guruStafModel->orderBy('nama_lengkap', 'ASC')->paginate(10, 'guru'),
            'pager'          => $this->guruStafModel->pager,
            'availableUsers' => $availableUsers
        ];
        return view('Modules\CivitasAkademika\Views\guru_staf\index', $data);
    }

    public function save()
    {
        $id = $this->request->getPost('id');
        
        $rules = [
            'nama_lengkap'   => 'required',
            'nama_panggilan' => 'required',
            'sapaan'         => 'required',
            'jenis_kelamin'  => 'required|in_list[L,P]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Data tidak valid. Periksa isian form Anda.');
        }

        $saveData = [
            'user_id'        => $this->request->getPost('user_id') ?: null,
            'nip'            => $this->request->getPost('nip') ?: null,
            'nama_lengkap'   => $this->request->getPost('nama_lengkap'),
            'nama_panggilan' => $this->request->getPost('nama_panggilan'),
            'sapaan'         => $this->request->getPost('sapaan'),
            'jenis_kelamin'  => $this->request->getPost('jenis_kelamin'),
            'no_hp'          => $this->request->getPost('no_hp') ?: null,
            'status_pegawai' => $this->request->getPost('status_pegawai') ?: 'Tetap',
            'jabatan'        => $this->request->getPost('jabatan') ?: 'Guru Kelas',
            'tahun_masuk'    => $this->request->getPost('tahun_masuk') ?: null,
            'is_active'      => $this->request->getPost('is_active') !== null ? 1 : 0
        ];

        // Handle Image Upload
        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $uploader = new \App\Libraries\ImageUploader('uploads/profiles/');
            // Crop as square, resize to thumb (150x150) and md (500x500)
            $result = $uploader->process($foto, true, ['thumb' => 150, 'md' => 500]);
            
            if ($result['status'] === 'success') {
                $saveData['foto'] = $result['files']['original'];
                
                // Jika update dan ada foto lama, bisa dihapus di sini
                // if ($id) {
                //     $oldData = $this->guruStafModel->find($id);
                //     // logika hapus file lama...
                // }
            } else {
                return redirect()->back()->withInput()->with('error', 'Gagal mengunggah foto: ' . $result['message']);
            }
        }

        if ($id) {
            $this->guruStafModel->update($id, $saveData);
            $message = 'Data Guru/Staf berhasil diperbarui.';
        } else {
            // New record is active by default unless checkbox is unchecked
            $this->guruStafModel->insert($saveData);
            $message = 'Data Guru/Staf berhasil ditambahkan.';
        }

        return redirect()->to('civitas-akademika/guru-staf')->with('success', $message);
    }

    public function delete($id)
    {
        $this->guruStafModel->delete($id);
        return redirect()->to('civitas-akademika/guru-staf')->with('success', 'Data Guru/Staf berhasil dihapus.');
    }

    /**
     * Halaman Profil Guru / Staf
     */
    public function profil($id)
    {
        $guru = $this->guruStafModel->find($id);
        if (!$guru) {
            return redirect()->to('civitas-akademika/guru-staf')->with('error', 'Data Guru/Staf tidak ditemukan.');
        }

        $userModel = new \CodeIgniter\Shield\Models\UserModel();

        // 1. Ambil data akun login jika sudah terhubung
        /** @var \CodeIgniter\Shield\Entities\User|null $linkedUser */
        $linkedUser = null;
        $userGroups = [];
        if (!empty($guru['user_id'])) {
            $usersProvider = auth()->getProvider();
            $linkedUser = $usersProvider->findById($guru['user_id']);

            if ($linkedUser) {
                $userGroups = $linkedUser->getGroups();

                // Pastikan akun selalu memiliki initial_password
                if (empty($linkedUser->initial_password)) {
                    $pool = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ@#';
                    $autoPassword = substr(str_shuffle(str_repeat($pool, 4)), 0, 8);
                    
                    $linkedUser->fill([
                        'password' => $autoPassword,
                        'initial_password' => $autoPassword
                    ]);
                    $usersProvider->save($linkedUser);
                    \Config\Database::connect()->table('users')->where('id', $linkedUser->id)->update(['initial_password' => $autoPassword]);
                    $linkedUser->initial_password = $autoPassword;
                }
            }
        }

        // 2. Ambil daftar user yang belum terhubung ke guru/staf manapun (untuk opsi tautkan akun)
        $alreadyLinkedUserIds = array_filter(array_column($this->guruStafModel->findAll(), 'user_id'));
        $builder = $userModel
            ->select('users.*, auth_identities.secret as email')
            ->join('auth_identities', 'auth_identities.user_id = users.id AND auth_identities.type = "email_password"', 'left');
        
        if (!empty($alreadyLinkedUserIds)) {
            $builder->whereNotIn('users.id', $alreadyLinkedUserIds);
        }
        $availableUsers = $builder->findAll();

        // 3. Ambil daftar kelas yang diampu guru ini (sebagai wali kelas)
        $rombelModel = new \Modules\Akademik\Models\RombelModel();
        $allRombel = $rombelModel->orderBy('tahun_ajaran', 'DESC')->orderBy('tingkat', 'ASC')->findAll();
        $kelasDiampu = [];
        foreach ($allRombel as $r) {
            $waliArr = json_decode($r['wali_kelas'] ?? '[]', true) ?: [];
            if (in_array($id, $waliArr) || in_array((string)$id, $waliArr)) {
                // Hitung jumlah murid di rombel ini
                $countSiswa = (new \Modules\Akademik\Models\RombelSiswaModel())
                    ->where('rombel_id', $r['id'])
                    ->countAllResults();
                $r['total_siswa'] = $countSiswa;
                $kelasDiampu[] = $r;
            }
        }

        // 4. Hitung total jurnal perkembangan yang telah dicatat oleh guru ini
        $perkembanganModel = new \Modules\CivitasAkademika\Models\PerkembanganSiswaModel();
        $totalJurnal = $perkembanganModel->where('guru_id', $id)->countAllResults();

        // Ambil daftar grup/role yang tersedia dari Shield
        $authGroups = config('AuthGroups')->groups ?? [];

        $data = [
            'title'          => 'Profil Guru: ' . $guru['sapaan'] . ' ' . $guru['nama_lengkap'],
            'guru'           => $guru,
            'linkedUser'     => $linkedUser,
            'userGroups'     => $userGroups,
            'availableUsers' => $availableUsers,
            'kelasDiampu'    => $kelasDiampu,
            'totalJurnal'    => $totalJurnal,
            'availableGroups'=> $authGroups
        ];

        return view('Modules\CivitasAkademika\Views\guru_staf\profil', $data);
    }

    /**
     * Buat Akun Baru & Langsung Tautkan ke Guru/Staf
     */
    public function buatDanTautkanUser()
    {
        $guruId = (int) $this->request->getPost('guru_id');
        $guru = $this->guruStafModel->find($guruId);
        if (!$guru) {
            return redirect()->back()->with('error', 'Data Guru/Staf tidak ditemukan.');
        }

        $rules = [
            'username' => 'required|min_length[3]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[auth_identities.secret]',
            'group'    => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors()[0] ?? 'Data form tidak valid.');
        }

        $username = trim($this->request->getPost('username'));
        $email    = trim($this->request->getPost('email'));
        $group    = $this->request->getPost('group');
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
            $savedUser->addGroup($group);

            // Update user_id di profil guru
            $this->guruStafModel->update($guruId, ['user_id' => $userId]);

            $groupTitle = config('AuthGroups')->groups[$group]['title'] ?? ucfirst($group);

            $accountInfo = [
                'username'   => $username,
                'email'      => $email,
                'password'   => $password,
                'group'      => $groupTitle,
                'nama_guru'  => $guru['sapaan'] . ' ' . $guru['nama_lengkap'],
                'no_hp'      => $guru['no_hp'] ?? '',
                'login_url'  => base_url('login')
            ];

            return redirect()->to('civitas-akademika/guru-staf/profil/' . $guruId)
                ->with('success', 'Akun login berhasil dibuat dan langsung ditautkan ke guru!')
                ->with('new_account', $accountInfo);

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat akun: ' . $e->getMessage());
        }
    }

    /**
     * Tautkan Akun Yang Sudah Ada ke Guru
     */
    public function tautkanUser()
    {
        $guruId = (int) $this->request->getPost('guru_id');
        $userId = (int) $this->request->getPost('user_id');

        if (!$guruId || !$userId) {
            return redirect()->back()->with('error', 'Pilih akun yang valid.');
        }

        $this->guruStafModel->update($guruId, ['user_id' => $userId]);
        return redirect()->back()->with('success', 'Akun login berhasil ditautkan ke profil pendidik.');
    }

    /**
     * Reset Password Guru/Staf
     */
    public function resetPassword()
    {
        $guruId = (int) $this->request->getPost('guru_id');
        $userId = (int) $this->request->getPost('user_id');
        $password = $this->request->getPost('password');

        $guru = $this->guruStafModel->find($guruId);
        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
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

            $userGroups = $user->getGroups();
            $groupTitles = [];
            foreach ($userGroups as $grp) {
                $groupTitles[] = config('AuthGroups')->groups[$grp]['title'] ?? ucfirst($grp);
            }
            $roleTitle = implode(', ', $groupTitles) ?: 'Wali Kelas / Guru';

            $accountInfo = [
                'username'   => $user->username,
                'email'      => $userEmail,
                'password'   => $password,
                'group'      => $roleTitle,
                'nama_guru'  => $guru['sapaan'] . ' ' . $guru['nama_lengkap'],
                'no_hp'      => $guru['no_hp'] ?? '',
                'login_url'  => base_url('login')
            ];

            return redirect()->to('civitas-akademika/guru-staf/profil/' . $guruId)
                ->with('success', 'Password akun berhasil direset!')
                ->with('new_account', $accountInfo);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mereset password: ' . $e->getMessage());
        }
    }

    /**
     * Lepas Tautan Akun dari Guru
     */
    public function hapusTautanUser($id)
    {
        $this->guruStafModel->update($id, ['user_id' => null]);
        return redirect()->back()->with('success', 'Tautan akun login berhasil dilepas.');
    }
}
