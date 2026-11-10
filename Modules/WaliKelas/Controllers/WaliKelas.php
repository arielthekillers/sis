<?php

namespace Modules\WaliKelas\Controllers;

use App\Controllers\BaseController;
use Modules\Akademik\Models\RombelModel;
use Modules\Akademik\Models\RombelSiswaModel;
use Modules\CivitasAkademika\Models\GuruStafModel;
use Modules\CivitasAkademika\Models\SiswaModel;
use Modules\CivitasAkademika\Models\PerkembanganSiswaModel;
use Modules\MasterData\Models\TahunAjaranModel;

class WaliKelas extends BaseController
{
    protected $rombelModel;
    protected $rombelSiswaModel;
    protected $guruStafModel;
    protected $siswaModel;
    protected $perkembanganModel;
    protected $tahunAjaranModel;

    public function __construct()
    {
        $this->rombelModel = new RombelModel();
        $this->rombelSiswaModel = new RombelSiswaModel();
        $this->guruStafModel = new GuruStafModel();
        $this->siswaModel = new SiswaModel();
        $this->perkembanganModel = new PerkembanganSiswaModel();
        $this->tahunAjaranModel = new TahunAjaranModel();
    }

    /**
     * Dapatkan profil guru yang sedang login
     */
    private function getCurrentGuru()
    {
        $userId = auth()->id();
        return $this->guruStafModel->where('user_id', $userId)->first();
    }

    /**
     * Dapatkan tahun ajaran aktif
     */
    private function getActiveTahun()
    {
        $ta = $this->tahunAjaranModel->where('is_active', 1)->first();
        return $ta ? $ta['tahun'] : (date('Y') . '/' . (date('Y') + 1));
    }

    /**
     * Dapatkan daftar rombel yang diampu oleh guru ini
     */
    private function getRombelDiampu($guruId, $isSuperAdmin, $tahun = null)
    {
        $tahun = $tahun ?: $this->getActiveTahun();
        $allRombel = $this->rombelModel->where('tahun_ajaran', $tahun)->orderBy('tingkat', 'ASC')->findAll();
        $kelasBinaan = [];

        foreach ($allRombel as $r) {
            $r['nama_rombel'] = $r['nama_kelas'] ?? ($r['nama_rombel'] ?? '');
            $waliArr = json_decode($r['wali_kelas'] ?? '[]', true) ?: [];
            if ($isSuperAdmin || ($guruId && (in_array($guruId, $waliArr) || in_array((string)$guruId, $waliArr)))) {
                // Hitung total murid
                $countSiswa = $this->rombelSiswaModel->where('rombel_id', $r['id'])->countAllResults();
                $r['total_siswa'] = $countSiswa;

                // Hitung siswa laki-laki & perempuan
                $db = \Config\Database::connect();
                $counts = $db->table('rombel_siswa')
                    ->select('siswa.jenis_kelamin, COUNT(*) as count')
                    ->join('siswa', 'siswa.id = rombel_siswa.siswa_id')
                    ->where('rombel_siswa.rombel_id', $r['id'])
                    ->where('siswa.deleted_at IS NULL')
                    ->groupBy('siswa.jenis_kelamin')
                    ->get()->getResultArray();

                $r['total_l'] = 0;
                $r['total_p'] = 0;
                foreach ($counts as $c) {
                    if ($c['jenis_kelamin'] === 'L') $r['total_l'] = (int) $c['count'];
                    if ($c['jenis_kelamin'] === 'P') $r['total_p'] = (int) $c['count'];
                }

                $kelasBinaan[] = $r;
            }
        }

        return $kelasBinaan;
    }

    /**
     * Beranda Guru / Dashboard
     */
    public function index()
    {
        return $this->dashboard();
    }

    public function dashboard()
    {
        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser->inGroup('superadmin', 'direksi');
        $guru = $this->getCurrentGuru();
        $tahun = $this->getActiveTahun();

        if (!$isSuperAdmin && !$guru) {
            $data = [
                'title'       => 'Portal Pendidik',
                'guru'        => null,
                'kelasBinaan' => [],
                'unlinked'    => true
            ];
            return view('Modules\WaliKelas\Views\unlinked', $data);
        }

        $guruId = $guru ? $guru['id'] : null;
        $kelasBinaan = $this->getRombelDiampu($guruId, $isSuperAdmin, $tahun);
        $rombelIds = array_column($kelasBinaan, 'id');

        // Hitung total murid unik yang diampu
        $totalMurid = 0;
        if (!empty($rombelIds)) {
            $db = \Config\Database::connect();
            $totalMurid = $db->table('rombel_siswa')
                ->whereIn('rombel_id', $rombelIds)
                ->countAllResults();
        }

        // Hitung total jurnal perkembangan
        $totalCatatan = 0;
        $catatanBulanIni = 0;
        $recentCatatan = [];

        if ($guruId || $isSuperAdmin) {
            $catatanQuery = $this->perkembanganModel
                ->select('perkembangan_siswa.*, siswa.nama_lengkap, siswa.nama_panggilan, siswa.foto as foto_siswa, siswa.jenis_kelamin, kelas.nama_kelas as nama_rombel, kelas.nama_kelas')
                ->join('siswa', 'siswa.id = perkembangan_siswa.siswa_id')
                ->join('kelas', 'kelas.id = perkembangan_siswa.kelas_id', 'left');

            if (!$isSuperAdmin && $guruId) {
                $catatanQuery->where('perkembangan_siswa.guru_id', $guruId);
            } elseif (!empty($rombelIds)) {
                $catatanQuery->whereIn('perkembangan_siswa.kelas_id', $rombelIds);
            }

            $totalCatatan = (clone $catatanQuery)->countAllResults();

            $bulanIni = date('Y-m');
            $catatanBulanIni = (clone $catatanQuery)
                ->like('perkembangan_siswa.tanggal', $bulanIni, 'after')
                ->countAllResults();

            $recentCatatan = (clone $catatanQuery)
                ->orderBy('perkembangan_siswa.tanggal', 'DESC')
                ->orderBy('perkembangan_siswa.id', 'DESC')
                ->findAll(5);
        }

        $data = [
            'title'           => 'Beranda Guru & Wali Kelas',
            'guru'            => $guru,
            'tahun_aktif'     => $tahun,
            'kelasBinaan'     => $kelasBinaan,
            'totalMurid'      => $totalMurid,
            'totalCatatan'    => $totalCatatan,
            'catatanBulanIni' => $catatanBulanIni,
            'recentCatatan'   => $recentCatatan,
            'isSuperAdmin'    => $isSuperAdmin,
            'unlinked'        => false
        ];

        return view('Modules\WaliKelas\Views\dashboard', $data);
    }

    /**
     * Halaman Detail Kelas Binaan Tertentu
     */
    public function kelas($kelasId)
    {
        $tahun = $this->getActiveTahun();
        $rombel = $this->rombelModel->find($kelasId);

        if (!$rombel) {
            return redirect()->to('wali-kelas/dashboard')->with('error', 'Kelas tidak ditemukan.');
        }

        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser->inGroup('superadmin', 'direksi');
        $guru = $this->getCurrentGuru();

        if (!$isSuperAdmin) {
            $waliArr = json_decode($rombel['wali_kelas'] ?? '[]', true) ?: [];
            if (!$guru || (!in_array($guru['id'], $waliArr) && !in_array((string)$guru['id'], $waliArr))) {
                return redirect()->to('wali-kelas/dashboard')->with('error', 'Anda tidak memiliki akses ke kelas ini.');
            }
        }

        $siswaList = $this->rombelSiswaModel
            ->select('rombel_siswa.*, siswa.nis, siswa.nisn, siswa.nama_lengkap, siswa.nama_panggilan, siswa.jenis_kelamin, siswa.foto, siswa.status_siswa, siswa.no_hp_ortu, siswa.alamat')
            ->join('siswa', 'siswa.id = rombel_siswa.siswa_id')
            ->where('rombel_siswa.rombel_id', $kelasId)
            ->where('siswa.deleted_at IS NULL')
            ->orderBy('siswa.nama_lengkap', 'ASC')
            ->findAll();

        foreach ($siswaList as &$s) {
            $s['total_catatan'] = $this->perkembanganModel
                ->where('siswa_id', $s['siswa_id'])
                ->where('kelas_id', $kelasId)
                ->countAllResults();
        }

        $data = [
            'title'       => 'Kelas ' . $rombel['nama_rombel'],
            'rombel'      => $rombel,
            'siswaList'   => $siswaList,
            'tahun_aktif' => $tahun,
            'guru'        => $guru
        ];

        return view('Modules\WaliKelas\Views\kelas', $data);
    }

    /**
     * Halaman Daftar Seluruh Murid yang Diampu Guru
     */
    public function daftarMurid()
    {
        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser->inGroup('superadmin', 'direksi');
        $guru = $this->getCurrentGuru();
        $tahun = $this->getActiveTahun();

        if (!$isSuperAdmin && !$guru) {
            return view('Modules\WaliKelas\Views\unlinked', ['title' => 'Murid Binaan', 'guru' => null, 'kelasBinaan' => [], 'unlinked' => true]);
        }

        $guruId = $guru ? $guru['id'] : null;
        $kelasBinaan = $this->getRombelDiampu($guruId, $isSuperAdmin, $tahun);
        $rombelIds = array_column($kelasBinaan, 'id');

        $selectedRombelId = (int) $this->request->getGet('rombel_id');
        if ($selectedRombelId && in_array($selectedRombelId, $rombelIds)) {
            $filterRombelIds = [$selectedRombelId];
        } else {
            $filterRombelIds = $rombelIds;
        }

        $muridList = [];
        if (!empty($filterRombelIds)) {
            $muridList = $this->rombelSiswaModel
                ->select('rombel_siswa.*, kelas.nama_kelas as nama_rombel, kelas.nama_kelas, kelas.tingkat, siswa.nis, siswa.nisn, siswa.nama_lengkap, siswa.nama_panggilan, siswa.jenis_kelamin, siswa.foto, siswa.status_siswa, siswa.no_hp_ortu, siswa.alamat, siswa.nama_ayah, siswa.nama_ibu')
                ->join('kelas', 'kelas.id = rombel_siswa.rombel_id')
                ->join('siswa', 'siswa.id = rombel_siswa.siswa_id')
                ->whereIn('rombel_siswa.rombel_id', $filterRombelIds)
                ->where('siswa.deleted_at IS NULL')
                ->orderBy('kelas.tingkat', 'ASC')
                ->orderBy('siswa.nama_lengkap', 'ASC')
                ->findAll();

            foreach ($muridList as &$m) {
                $m['total_catatan'] = $this->perkembanganModel
                    ->where('siswa_id', $m['siswa_id'])
                    ->where('kelas_id', $m['rombel_id'])
                    ->countAllResults();
            }
        }

        $data = [
            'title'            => 'Daftar Murid Binaan',
            'guru'             => $guru,
            'tahun_aktif'      => $tahun,
            'kelasBinaan'      => $kelasBinaan,
            'muridList'        => $muridList,
            'selectedRombelId' => $selectedRombelId
        ];

        return view('Modules\WaliKelas\Views\murid', $data);
    }

    /**
     * Halaman Profil Lengkap & Riwayat Perkembangan Murid
     */
    public function profilMurid($siswaId)
    {
        $siswa = $this->siswaModel->find($siswaId);
        if (!$siswa) {
            return redirect()->to('wali-kelas/murid')->with('error', 'Data siswa tidak ditemukan.');
        }

        $guru = $this->getCurrentGuru();
        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser->inGroup('superadmin', 'direksi');

        // Validasi akses ke murid ini
        $db = \Config\Database::connect();
        $rombelSiswa = $db->table('rombel_siswa')
            ->select('rombel_siswa.*, kelas.nama_kelas as nama_rombel, kelas.nama_kelas, kelas.tingkat, kelas.tahun_ajaran, kelas.wali_kelas')
            ->join('kelas', 'kelas.id = rombel_siswa.rombel_id')
            ->where('rombel_siswa.siswa_id', $siswaId)
            ->get()->getResultArray();

        $allowed = $isSuperAdmin;
        $guruId = $guru ? $guru['id'] : null;

        if (!$allowed && $guruId) {
            foreach ($rombelSiswa as $rs) {
                $waliArr = json_decode($rs['wali_kelas'] ?? '[]', true) ?: [];
                if (in_array($guruId, $waliArr) || in_array((string)$guruId, $waliArr)) {
                    $allowed = true;
                    break;
                }
            }
        }

        if (!$allowed) {
            return redirect()->to('wali-kelas/murid')->with('error', 'Anda tidak memiliki hak akses ke data murid ini.');
        }

        // Ambil riwayat catatan perkembangan siswa
        $catatanList = $this->perkembanganModel->getBySiswa($siswaId, false);

        // Ambil data orang tua siswa yang terhubung
        $orangTuaList = $db->table('orang_tua_siswa')
            ->select('orang_tua_siswa.*, users.username, users.initial_password, auth_identities.secret as email')
            ->join('users', 'users.id = orang_tua_siswa.user_id')
            ->join('auth_identities', 'auth_identities.user_id = users.id AND auth_identities.type = "email_password"', 'left')
            ->where('orang_tua_siswa.siswa_id', $siswaId)
            ->where('orang_tua_siswa.deleted_at IS NULL')
            ->get()->getResultArray();

        // Ambil daftar kelas yang sedang diampu guru ini untuk modal tambah jurnal
        $kelasBinaan = $this->getRombelDiampu($guruId, $isSuperAdmin);

        $data = [
            'title'        => 'Profil Murid: ' . $siswa['nama_lengkap'],
            'siswa'        => $siswa,
            'rombelSiswa'  => $rombelSiswa,
            'catatanList'  => $catatanList,
            'orangTuaList' => $orangTuaList,
            'kelasBinaan'  => $kelasBinaan,
            'guru'         => $guru
        ];

        return view('Modules\WaliKelas\Views\murid_profil', $data);
    }

    /**
     * Perbarui Kontak Murid (No HP Ortu & Alamat) oleh Wali Kelas
     */
    public function updateKontakMurid()
    {
        $siswaId = (int) $this->request->getPost('siswa_id');
        $siswa = $this->siswaModel->find($siswaId);
        if (!$siswa) {
            return redirect()->back()->with('error', 'Siswa tidak ditemukan.');
        }

        $updateData = [
            'no_hp_ortu' => trim($this->request->getPost('no_hp_ortu')),
            'alamat'     => trim($this->request->getPost('alamat')),
        ];

        $this->siswaModel->update($siswaId, $updateData);
        return redirect()->back()->with('success', 'Data kontak dan alamat ananda berhasil diperbarui.');
    }

    /**
     * Halaman Kelola Semua Jurnal Perkembangan Siswa
     */
    public function perkembangan()
    {
        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser->inGroup('superadmin', 'direksi');
        $guru = $this->getCurrentGuru();
        $tahun = $this->getActiveTahun();

        if (!$isSuperAdmin && !$guru) {
            return view('Modules\WaliKelas\Views\unlinked', ['title' => 'Jurnal Perkembangan', 'guru' => null, 'kelasBinaan' => [], 'unlinked' => true]);
        }

        $guruId = $guru ? $guru['id'] : null;
        $kelasBinaan = $this->getRombelDiampu($guruId, $isSuperAdmin, $tahun);
        $rombelIds = array_column($kelasBinaan, 'id');

        $selectedRombelId = (int) $this->request->getGet('rombel_id');
        $selectedKategori = $this->request->getGet('kategori');

        $query = $this->perkembanganModel
            ->select('perkembangan_siswa.*, siswa.nama_lengkap, siswa.nama_panggilan, siswa.nis, siswa.foto as foto_siswa, kelas.nama_kelas as nama_rombel, kelas.nama_kelas, kelas.tingkat')
            ->join('siswa', 'siswa.id = perkembangan_siswa.siswa_id')
            ->join('kelas', 'kelas.id = perkembangan_siswa.kelas_id', 'left');

        if ($selectedRombelId) {
            $query->where('perkembangan_siswa.kelas_id', $selectedRombelId);
        } elseif (!empty($rombelIds)) {
            $query->whereIn('perkembangan_siswa.kelas_id', $rombelIds);
        }

        if ($selectedKategori) {
            $query->where('perkembangan_siswa.kategori', $selectedKategori);
        }

        $perkembanganList = $query->orderBy('perkembangan_siswa.tanggal', 'DESC')
            ->orderBy('perkembangan_siswa.id', 'DESC')
            ->findAll();

        // Ambil juga daftar murid di rombel binaan untuk pilihan dropdown tambah catatan
        $muridList = [];
        if (!empty($rombelIds)) {
            $muridList = $this->rombelSiswaModel
                ->select('rombel_siswa.rombel_id, siswa.id, siswa.nama_lengkap, siswa.nama_panggilan, kelas.nama_kelas as nama_rombel, kelas.nama_kelas')
                ->join('kelas', 'kelas.id = rombel_siswa.rombel_id')
                ->join('siswa', 'siswa.id = rombel_siswa.siswa_id')
                ->whereIn('rombel_siswa.rombel_id', $rombelIds)
                ->where('siswa.deleted_at IS NULL')
                ->orderBy('siswa.nama_lengkap', 'ASC')
                ->findAll();
        }

        $data = [
            'title'            => 'Kelola Jurnal Perkembangan Murid',
            'guru'             => $guru,
            'tahun_aktif'      => $tahun,
            'kelasBinaan'      => $kelasBinaan,
            'perkembanganList' => $perkembanganList,
            'muridList'        => $muridList,
            'selectedRombelId' => $selectedRombelId,
            'selectedKategori' => $selectedKategori
        ];

        return view('Modules\WaliKelas\Views\perkembangan', $data);
    }

    /**
     * Halaman Profil Guru & Pengaturan Akun Login
     */
    public function profil()
    {
        $currentUser = auth()->user();
        $guru = $this->getCurrentGuru();

        if (!$guru) {
            return redirect()->to('wali-kelas/dashboard')->with('error', 'Profil guru tidak ditemukan.');
        }

        $kelasBinaan = $this->getRombelDiampu($guru['id'], false);

        $data = [
            'title'       => 'Profil & Akun Pendidik',
            'guru'        => $guru,
            'user'        => $currentUser,
            'kelasBinaan' => $kelasBinaan,
        ];

        return view('Modules\WaliKelas\Views\profil', $data);
    }

    /**
     * Simpan Perubahan Biodata Guru (Nama Panggilan, No HP, Foto Profil)
     */
    public function updateProfil()
    {
        $guru = $this->getCurrentGuru();
        if (!$guru) {
            return redirect()->to('wali-kelas/dashboard')->with('error', 'Profil guru tidak ditemukan.');
        }

        $rules = [
            'nama_panggilan' => 'required|min_length[2]',
            'no_hp'          => 'permit_empty|min_length[8]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors()[0] ?? 'Form tidak valid.');
        }

        $updateData = [
            'nama_panggilan' => trim($this->request->getPost('nama_panggilan')),
            'no_hp'          => trim($this->request->getPost('no_hp')),
        ];

        // Handle foto profil
        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $uploader = new \App\Libraries\ImageUploader('uploads/profiles/');
            $result = $uploader->process($foto, true, ['thumb' => 150, 'md' => 500]);

            if ($result['status'] === 'success') {
                $updateData['foto'] = $result['files']['original'];
            } else {
                return redirect()->back()->withInput()->with('error', 'Gagal upload foto: ' . $result['message']);
            }
        }

        $this->guruStafModel->update($guru['id'], $updateData);
        return redirect()->back()->with('success', 'Biodata profil pendidik berhasil diperbarui!');
    }

    /**
     * Ubah Password Akun Guru
     */
    public function updatePassword()
    {
        $currentUser = auth()->user();
        if (!$currentUser) {
            return redirect()->to('login');
        }

        $passwordBaru = $this->request->getPost('password_baru');
        $konfirmasi   = $this->request->getPost('konfirmasi_password');

        if (empty($passwordBaru) || strlen($passwordBaru) < 8) {
            return redirect()->back()->with('error', 'Password baru minimal 8 karakter.');
        }

        if ($passwordBaru !== $konfirmasi) {
            return redirect()->back()->with('error', 'Konfirmasi password tidak cocok dengan password baru.');
        }

        try {
            $usersProvider = auth()->getProvider();
            $currentUser->fill(['password' => $passwordBaru]);
            $usersProvider->save($currentUser);

            // Perbarui juga initial_password
            \Config\Database::connect()->table('users')->where('id', $currentUser->id)->update(['initial_password' => $passwordBaru]);

            return redirect()->back()->with('success', 'Password akun berhasil diubah! Gunakan password baru untuk login berikutnya.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengubah password: ' . $e->getMessage());
        }
    }

    /**
     * Halaman Detail & Input Catatan Perkembangan Siswa Tertentu (Legacy Route Support)
     */
    public function siswa($siswaId, $kelasId)
    {
        return $this->profilMurid($siswaId);
    }

    /**
     * Simpan / Perbarui Catatan Perkembangan
     */
    public function savePerkembangan()
    {
        $rules = [
            'siswa_id' => 'required|numeric',
            'kelas_id' => 'required|numeric',
            'tanggal'  => 'required|valid_date',
            'kategori' => 'required',
            'judul'    => 'required|min_length[3]',
            'catatan'  => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Form tidak valid: ' . ($this->validator->getErrors()[0] ?? ''));
        }

        $id = $this->request->getPost('id');
        $guru = $this->getCurrentGuru();
        $guruId = $guru ? $guru['id'] : ($this->request->getPost('guru_id') ?: 1);

        $saveData = [
            'siswa_id' => (int) $this->request->getPost('siswa_id'),
            'kelas_id' => (int) $this->request->getPost('kelas_id'),
            'guru_id'  => (int) $guruId,
            'tanggal'  => $this->request->getPost('tanggal'),
            'kategori' => $this->request->getPost('kategori'),
            'judul'    => $this->request->getPost('judul'),
            'catatan'  => $this->request->getPost('catatan'),
            'status'   => $this->request->getPost('status') ?: 'published',
        ];

        // Handle upload foto kegiatan
        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $uploader = new \App\Libraries\ImageUploader('uploads/perkembangan/');
            $result = $uploader->process($foto, false, ['thumb' => 200, 'md' => 800]);

            if ($result['status'] === 'success') {
                $saveData['foto'] = $result['files']['original'];
            } else {
                return redirect()->back()->withInput()->with('error', 'Gagal mengunggah foto: ' . $result['message']);
            }
        }

        if ($id) {
            $this->perkembanganModel->update($id, $saveData);
            $msg = 'Catatan perkembangan berhasil diperbarui.';
        } else {
            $this->perkembanganModel->insert($saveData);
            $msg = 'Catatan perkembangan berhasil ditambahkan.';
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Hapus Catatan Perkembangan
     */
    public function deletePerkembangan($id)
    {
        $this->perkembanganModel->delete($id);
        return redirect()->back()->with('success', 'Catatan perkembangan berhasil dihapus.');
    }
}
