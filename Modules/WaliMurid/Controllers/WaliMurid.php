<?php

namespace Modules\WaliMurid\Controllers;

use App\Controllers\BaseController;
use Modules\CivitasAkademika\Models\OrangTuaSiswaModel;
use Modules\CivitasAkademika\Models\SiswaModel;
use Modules\CivitasAkademika\Models\PerkembanganSiswaModel;
use Modules\CivitasAkademika\Models\GuruStafModel;
use Modules\Akademik\Models\RombelModel;
use Modules\Akademik\Models\RombelSiswaModel;
use Modules\MasterData\Models\TahunAjaranModel;
use Modules\Keuangan\Models\TagihanModel;
use Modules\Keuangan\Models\PembayaranModel;

class WaliMurid extends BaseController
{
    protected $orangTuaModel;
    protected $siswaModel;
    protected $perkembanganModel;
    protected $guruStafModel;
    protected $rombelModel;
    protected $rombelSiswaModel;
    protected $tahunAjaranModel;
    protected $tagihanModel;
    protected $pembayaranModel;

    public function __construct()
    {
        $this->orangTuaModel     = new OrangTuaSiswaModel();
        $this->siswaModel        = new SiswaModel();
        $this->perkembanganModel = new PerkembanganSiswaModel();
        $this->guruStafModel     = new GuruStafModel();
        $this->rombelModel       = new RombelModel();
        $this->rombelSiswaModel  = new RombelSiswaModel();
        $this->tahunAjaranModel  = new TahunAjaranModel();
        $this->tagihanModel      = new TagihanModel();
        $this->pembayaranModel   = new PembayaranModel();
    }

    private function getActiveTahun()
    {
        $ta = $this->tahunAjaranModel->where('is_active', 1)->first();
        return $ta ? $ta['tahun'] : (date('Y') . '/' . (date('Y') + 1));
    }

    /**
     * Dapatkan daftar anak & data pendukung orang tua yang sedang login
     */
    private function getPreparedData()
    {
        $currentUser = auth()->user();
        if (!$currentUser) {
            return null;
        }

        $userId = $currentUser->id;
        $isSuperAdmin = $currentUser->inGroup('superadmin', 'direksi');

        if ($isSuperAdmin) {
            // Superadmin bisa mengintip dengan mengambil anak pertama jika belum ditautkan
            $anakList = $this->orangTuaModel->getAnakByUserId($userId);
            if (empty($anakList)) {
                $firstSiswa = $this->siswaModel->first();
                if ($firstSiswa) {
                    $anakList = [[
                        'user_id'        => $userId,
                        'siswa_id'       => $firstSiswa['id'],
                        'hubungan'       => 'Wali',
                        'nis'            => $firstSiswa['nis'],
                        'nisn'           => $firstSiswa['nisn'],
                        'nama_lengkap'   => $firstSiswa['nama_lengkap'],
                        'nama_panggilan' => $firstSiswa['nama_panggilan'],
                        'jenis_kelamin'  => $firstSiswa['jenis_kelamin'],
                        'foto'           => $firstSiswa['foto'],
                        'status_siswa'   => $firstSiswa['status_siswa'],
                        'program'        => $firstSiswa['program'] ?? 'Fullday'
                    ]];
                }
            }
        } else {
            $anakList = $this->orangTuaModel->getAnakByUserId($userId);
        }

        if (empty($anakList)) {
            return null;
        }

        $tahunAktif = $this->getActiveTahun();

        // Cari informasi kelas saat ini & tagihan untuk setiap anak
        foreach ($anakList as &$anak) {
            $rombel = $this->rombelSiswaModel
                ->select('kelas.*')
                ->join('kelas', 'kelas.id = rombel_siswa.rombel_id')
                ->where('rombel_siswa.siswa_id', $anak['siswa_id'])
                ->where('kelas.tahun_ajaran', $tahunAktif)
                ->first();
            $anak['kelas'] = $rombel;

            // Hitung tunggakan anak ini
            $tunggakan = $this->tagihanModel
                ->selectSum('nominal_tagihan')
                ->selectSum('nominal_dibayar')
                ->where('siswa_id', $anak['siswa_id'])
                ->whereIn('status', ['Belum Bayar', 'Mencicil'])
                ->first();
            $anak['sisa_tagihan'] = ($tunggakan['nominal_tagihan'] ?? 0) - ($tunggakan['nominal_dibayar'] ?? 0);
        }

        // Tentukan anak yang sedang aktif dipilih (Child Switcher)
        $reqAnakId     = $this->request->getGet('anak_id');
        $sessionAnakId = session()->get('active_wali_anak_id');
        $activeAnakId  = $reqAnakId ? (int)$reqAnakId : ($sessionAnakId ? (int)$sessionAnakId : (int)$anakList[0]['siswa_id']);

        $activeAnak = null;
        foreach ($anakList as $a) {
            if ((int)$a['siswa_id'] === $activeAnakId) {
                $activeAnak = $a;
                break;
            }
        }
        if (!$activeAnak) {
            $activeAnak = $anakList[0];
            $activeAnakId = (int)$activeAnak['siswa_id'];
        }

        session()->set('active_wali_anak_id', $activeAnakId);

        // Ambil wali kelas dari anak yang aktif
        $waliKelasList = [];
        if (!empty($activeAnak['kelas']['wali_kelas'])) {
            $waliArr = json_decode($activeAnak['kelas']['wali_kelas'], true) ?: [];
            if (!empty($waliArr)) {
                $waliKelasList = $this->guruStafModel->whereIn('id', $waliArr)->findAll();
            }
        }

        // Cek apakah keluarga memiliki anak dengan Program Fullday
        $hasFulldayChild = false;
        foreach ($anakList as $a) {
            if (($a['program'] ?? 'Reguler') === 'Fullday') {
                $hasFulldayChild = true;
                break;
            }
        }
        if ($isSuperAdmin) {
            $hasFulldayChild = true;
        }

        // Total tagihan gabungan keluarga
        $totalTagihanKeluarga = 0;
        foreach ($anakList as $a) {
            $totalTagihanKeluarga += ($a['sisa_tagihan'] ?? 0);
        }

        $xenditService = new \App\Libraries\XenditService();
        $isXenditEnabled = $xenditService->isEnabled();

        return [
            'anakList'             => $anakList,
            'activeAnak'           => $activeAnak,
            'activeAnakId'         => $activeAnakId,
            'waliKelasList'        => $waliKelasList,
            'tahunAktif'           => $tahunAktif,
            'hasFulldayChild'      => $hasFulldayChild,
            'totalTagihanKeluarga' => $totalTagihanKeluarga,
            'currentUser'          => $currentUser,
            'isXenditEnabled'      => $isXenditEnabled,
        ];
    }

    /**
     * Dashboard Portal Wali Murid
     */
    public function index()
    {
        return $this->dashboard();
    }

    public function dashboard()
    {
        $prep = $this->getPreparedData();
        if (!$prep) {
            return view('Modules\WaliMurid\Views\unlinked', [
                'title'    => 'Portal Orang Tua',
                'unlinked' => true
            ]);
        }

        // Ambil 5 catatan perkembangan terbaru anak aktif
        $perkembangan = $this->perkembanganModel
            ->select('perkembangan_siswa.*, guru_staf.sapaan, guru_staf.nama_panggilan as guru_panggilan, guru_staf.nama_lengkap as guru_nama')
            ->join('guru_staf', 'guru_staf.id = perkembangan_siswa.guru_id', 'left')
            ->where('perkembangan_siswa.siswa_id', $prep['activeAnakId'])
            ->where('perkembangan_siswa.status', 'published')
            ->orderBy('perkembangan_siswa.tanggal', 'DESC')
            ->orderBy('perkembangan_siswa.id', 'DESC')
            ->limit(5)
            ->findAll();

        $totalCatatan = $this->perkembanganModel
            ->where('siswa_id', $prep['activeAnakId'])
            ->where('status', 'published')
            ->countAllResults();

        // Ambil tagihan berjalan anak aktif
        $tagihanAktif = $this->tagihanModel
            ->select('keu_tagihan.*, COALESCE(keu_tagihan.judul_tagihan, keu_jenis_pembayaran.nama_pembayaran) as nama_pos, keu_jenis_pembayaran.nama_pembayaran, keu_jenis_pembayaran.tipe_siklus as tipe')
            ->join('keu_jenis_pembayaran', 'keu_jenis_pembayaran.id = keu_tagihan.jenis_pembayaran_id', 'left')
            ->where('keu_tagihan.siswa_id', $prep['activeAnakId'])
            ->whereIn('keu_tagihan.status', ['Belum Bayar', 'Mencicil'])
            ->orderBy('keu_tagihan.id', 'DESC')
            ->limit(5)
            ->findAll();

        $data = array_merge($prep, [
            'title'        => 'Beranda Ananda - ' . $prep['activeAnak']['nama_lengkap'],
            'perkembangan' => $perkembangan,
            'totalCatatan' => $totalCatatan,
            'tagihanAktif' => $tagihanAktif,
            'unlinked'     => false
        ]);

        return view('Modules\WaliMurid\Views\dashboard', $data);
    }

    /**
     * Halaman Jurnal Perkembangan Ananda
     */
    public function perkembangan()
    {
        $prep = $this->getPreparedData();
        if (!$prep) {
            return view('Modules\WaliMurid\Views\unlinked', [
                'title'    => 'Perkembangan Ananda',
                'unlinked' => true
            ]);
        }

        $kategoriFilter = $this->request->getGet('kategori');

        $query = $this->perkembanganModel
            ->select('perkembangan_siswa.*, guru_staf.sapaan, guru_staf.nama_panggilan as guru_panggilan, guru_staf.nama_lengkap as guru_nama, kelas.nama_kelas')
            ->join('guru_staf', 'guru_staf.id = perkembangan_siswa.guru_id', 'left')
            ->join('kelas', 'kelas.id = perkembangan_siswa.kelas_id', 'left')
            ->where('perkembangan_siswa.siswa_id', $prep['activeAnakId'])
            ->where('perkembangan_siswa.status', 'published');

        if (!empty($kategoriFilter)) {
            $query->where('perkembangan_siswa.kategori', $kategoriFilter);
        }

        $jurnalList = $query
            ->orderBy('perkembangan_siswa.tanggal', 'DESC')
            ->orderBy('perkembangan_siswa.id', 'DESC')
            ->findAll();

        // Hitung total per kategori untuk filter pills
        $aspekList = [
            'Nilai Agama & Moral',
            'Fisik & Motorik',
            'Kognitif',
            'Bahasa',
            'Sosial & Emosional',
            'Seni & Kreativitas'
        ];

        $aspekCounts = [];
        foreach ($aspekList as $aspek) {
            $aspekCounts[$aspek] = $this->perkembanganModel
                ->where('siswa_id', $prep['activeAnakId'])
                ->where('status', 'published')
                ->where('kategori', $aspek)
                ->countAllResults();
        }

        $data = array_merge($prep, [
            'title'          => 'Perkembangan Ananda - ' . $prep['activeAnak']['nama_lengkap'],
            'jurnalList'     => $jurnalList,
            'kategoriFilter' => $kategoriFilter,
            'aspekList'      => $aspekList,
            'aspekCounts'    => $aspekCounts,
        ]);

        return view('Modules\WaliMurid\Views\perkembangan', $data);
    }

    /**
     * Halaman Rincian Keuangan & Tagihan Keluarga
     */
    public function keuangan()
    {
        $prep = $this->getPreparedData();
        if (!$prep) {
            return view('Modules\WaliMurid\Views\unlinked', [
                'title'    => 'Tagihan & SPP',
                'unlinked' => true
            ]);
        }

        $anakList = $prep['anakList'];
        foreach ($anakList as &$anak) {
            // Tagihan belum lunas
            $anak['tagihan'] = $this->tagihanModel
                ->select('keu_tagihan.*, COALESCE(keu_tagihan.judul_tagihan, keu_jenis_pembayaran.nama_pembayaran) as nama_pos, keu_jenis_pembayaran.nama_pembayaran, keu_jenis_pembayaran.tipe_siklus as tipe')
                ->join('keu_jenis_pembayaran', 'keu_jenis_pembayaran.id = keu_tagihan.jenis_pembayaran_id', 'left')
                ->where('keu_tagihan.siswa_id', $anak['siswa_id'])
                ->whereIn('keu_tagihan.status', ['Belum Bayar', 'Mencicil'])
                ->orderBy('keu_tagihan.id', 'DESC')
                ->findAll();

            // Riwayat bayar lunas
            $anak['riwayat_bayar'] = $this->pembayaranModel
                ->where('siswa_id', $anak['siswa_id'])
                ->orderBy('tanggal_bayar', 'DESC')
                ->limit(10)
                ->findAll();

            $sisa = 0;
            foreach ($anak['tagihan'] as $t) {
                $sisa += ($t['nominal_tagihan'] - $t['nominal_dibayar']);
            }
            $anak['sisa_tagihan'] = $sisa;
        }

        $data = array_merge($prep, [
            'title'    => 'Informasi Keuangan & Tagihan Sekolah',
            'anakList' => $anakList,
        ]);

        return view('Modules\WaliMurid\Views\keuangan', $data);
    }

    /**
     * Halaman CCTV Monitoring Room (Khusus Program Fullday)
     */
    public function cctv()
    {
        $prep = $this->getPreparedData();
        if (!$prep) {
            return view('Modules\WaliMurid\Views\unlinked', [
                'title'    => 'CCTV Online',
                'unlinked' => true
            ]);
        }

        $isFullday = ($prep['activeAnak']['program'] ?? 'Reguler') === 'Fullday' || $prep['hasFulldayChild'];

        // Daftar kamera monitoring yang disediakan sekolah untuk siswa Fullday
        $namaKelas = $prep['activeAnak']['kelas']['nama_kelas'] ?? 'Kinder';
        $cameras = [
            [
                'id'          => 'cam-01',
                'code'        => 'CAM-01',
                'title'       => 'Ruang Kelas Utama (' . $namaKelas . ')',
                'area'        => 'Indoor',
                'description' => 'Area kegiatan belajar, bernyanyi, dan aktivitas motorik halus bersama bunda guru.',
                'status'      => 'Online',
                'resolution'  => '1080p Full HD',
                'schedule'    => '07.30 - 16.30 WIB',
            ],
            [
                'id'          => 'cam-02',
                'code'        => 'CAM-02',
                'title'       => 'Playground & Area Bermain',
                'area'        => 'Outdoor / Semi-Indoor',
                'description' => 'Area bermain bebas, perosotan, ayunan, dan olahraga pagi ananda.',
                'status'      => 'Online',
                'resolution'  => '1080p Full HD',
                'schedule'    => '08.00 - 15.30 WIB',
            ],
            [
                'id'          => 'cam-03',
                'code'        => 'CAM-03',
                'title'       => 'Dining Hall & Ruang Makan',
                'area'        => 'Indoor',
                'description' => 'Area makan siang bersama dan waktu snack sore program Fullday.',
                'status'      => 'Online',
                'resolution'  => '1080p Full HD',
                'schedule'    => '11.00 - 15.30 WIB',
            ],
            [
                'id'          => 'cam-04',
                'code'        => 'CAM-04',
                'title'       => 'Nap Room (Ruang Tidur Siang)',
                'area'        => 'Indoor Sleep Area',
                'description' => 'Area istirahat tidur siang ananda yang nyaman dan tenang dengan pengawasan guru piket.',
                'status'      => 'Online',
                'resolution'  => '1080p Full HD',
                'schedule'    => '12.30 - 14.30 WIB',
            ],
        ];

        $data = array_merge($prep, [
            'title'     => 'CCTV Live Streaming - Program Fullday',
            'isFullday' => $isFullday,
            'cameras'   => $cameras,
        ]);

        return view('Modules\WaliMurid\Views\cctv', $data);
    }

    /**
     * Halaman Profil & Pengaturan Akun Orang Tua
     */
    public function profil()
    {
        $prep = $this->getPreparedData();
        if (!$prep) {
            return view('Modules\WaliMurid\Views\unlinked', [
                'title'    => 'Profil & Akun',
                'unlinked' => true
            ]);
        }

        // Ambil data lengkap siswa termasuk alamat dan nomor HP ortu
        $detailAnakList = [];
        foreach ($prep['anakList'] as $a) {
            $siswaRow = $this->siswaModel->find($a['siswa_id']);
            if ($siswaRow) {
                $siswaRow['hubungan'] = $a['hubungan'];
                $siswaRow['kelas']    = $a['kelas'];
                $detailAnakList[]     = $siswaRow;
            }
        }

        $data = array_merge($prep, [
            'title'          => 'Profil & Pengaturan Akun Orang Tua',
            'detailAnakList' => $detailAnakList,
        ]);

        return view('Modules\WaliMurid\Views\profil', $data);
    }

    /**
     * Update Password Akun Orang Tua
     */
    public function updatePassword()
    {
        $currentUser = auth()->user();
        if (!$currentUser) {
            return redirect()->to('login');
        }

        $rules = [
            'password_sekarang'  => 'required',
            'password_baru'      => 'required|min_length[6]',
            'konfirmasi_password'=> 'required|matches[password_baru]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Form tidak valid: ' . ($this->validator->getErrors()[0] ?? ''));
        }

        $passwordSekarang = $this->request->getPost('password_sekarang');
        $passwordBaru     = $this->request->getPost('password_baru');

        // Validasi kecocokan password sekarang
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

            // Perbarui juga initial_password
            \Config\Database::connect()->table('users')->where('id', $currentUser->id)->update(['initial_password' => $passwordBaru]);

            return redirect()->back()->with('success', 'Password akun berhasil diubah! Gunakan password baru untuk login berikutnya.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui password: ' . $e->getMessage());
        }
    }

    /**
     * Proses Pembuatan Invoice & Checkout Pembayaran Online Xendit
     */
    public function bayarXendit()
    {
        $prep = $this->getPreparedData();
        if (!$prep) {
            return redirect()->to('wali-murid/dashboard');
        }

        $tagihanId = (int) $this->request->getPost('tagihan_id');
        if (!$tagihanId) {
            return redirect()->back()->with('error', 'Tagihan tidak ditemukan.');
        }

        // Ambil data tagihan
        $tagihan = $this->tagihanModel
            ->select('keu_tagihan.*, COALESCE(keu_tagihan.judul_tagihan, keu_jenis_pembayaran.nama_pembayaran) as nama_pos')
            ->join('keu_jenis_pembayaran', 'keu_jenis_pembayaran.id = keu_tagihan.jenis_pembayaran_id', 'left')
            ->find($tagihanId);

        if (!$tagihan) {
            return redirect()->back()->with('error', 'Data tagihan tidak ditemukan di sistem.');
        }

        // Verifikasi kepemilikan tagihan terhadap anak-anak dari user yang sedang login
        $allowedSiswaIds = array_column($prep['anakList'], 'siswa_id');
        if (!in_array((int)$tagihan['siswa_id'], array_map('intval', $allowedSiswaIds), true)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak untuk membayar tagihan ini.');
        }

        $sisa = (float)$tagihan['nominal_tagihan'] - (float)$tagihan['nominal_dibayar'];
        if ($sisa <= 0) {
            return redirect()->back()->with('info', 'Tagihan ini sudah lunas.');
        }

        $xenditService = new \App\Libraries\XenditService();
        if (!$xenditService->isEnabled()) {
            return redirect()->back()->with('error', 'Pembayaran online melalui Xendit saat ini sedang tidak aktif. Silakan hubungi tata usaha atau bayar via transfer rekening sekolah.');
        }

        $siswa = $this->siswaModel->find($tagihan['siswa_id']);
        $currentUser = auth()->user();
        $parentEmail = $currentUser->email ?: ('wali.' . ($siswa['nis'] ?: $siswa['id']) . '@sekolah.id');

        $xenditTrxModel = new \Modules\Keuangan\Models\XenditTransaksiModel();

        // Cek apakah sudah ada transaksi PENDING yang belum expired untuk tagihan ini
        $existingTrx = $xenditTrxModel
            ->where('tagihan_id', $tagihanId)
            ->where('status', 'PENDING')
            ->orderBy('id', 'DESC')
            ->first();

        if ($existingTrx && !empty($existingTrx['checkout_url'])) {
            // Periksa langsung ke Xendit apakah invoice masih aktif
            $xenditInvoice = $xenditService->getInvoice($existingTrx['invoice_id']);
            if ($xenditInvoice && $xenditInvoice['status'] === 'PENDING') {
                return redirect()->to($existingTrx['checkout_url']);
            }
        }

        $externalId = 'INV-SIS-' . $tagihanId . '-' . time();
        $posName    = $tagihan['nama_pos'] ?: 'Tagihan Sekolah';

        $invoiceParams = [
            'external_id'          => $externalId,
            'amount'               => $sisa,
            'description'          => 'Pembayaran ' . $posName . ' - Ananda ' . $siswa['nama_lengkap'],
            'payer_email'          => $parentEmail,
            'customer'             => [
                'given_names'   => $siswa['nama_ayah'] ?: ($siswa['nama_ibu'] ?: $currentUser->username),
                'email'         => $parentEmail,
                'mobile_number' => $siswa['no_hp_ortu'] ?: null,
            ],
            'items'                => [
                [
                    'name'     => $posName,
                    'quantity' => 1,
                    'price'    => (float) $sisa,
                    'category' => 'Biaya Pendidikan',
                ]
            ],
            'success_redirect_url' => base_url('wali-murid/pembayaran-sukses?external_id=' . $externalId),
            'failure_redirect_url' => base_url('wali-murid/pembayaran-batal?external_id=' . $externalId),
        ];

        $res = $xenditService->createInvoice($invoiceParams);

        if ($res['status'] === 'success') {
            $xenditTrxModel->insert([
                'invoice_id'      => $res['invoice_id'],
                'external_id'     => $externalId,
                'siswa_id'        => $siswa['id'],
                'tagihan_id'      => $tagihan['id'],
                'user_id'         => $currentUser->id,
                'amount'          => $sisa,
                'status'          => 'PENDING',
                'checkout_url'    => $res['invoice_url'],
            ]);

            return redirect()->to($res['invoice_url']);
        }

        return redirect()->back()->with('error', 'Gagal memproses pembayaran Xendit: ' . $res['message']);
    }

    /**
     * Halaman Konfirmasi Pembayaran Sukses (Return URL Xendit)
     */
    public function pembayaranSukses()
    {
        $prep = $this->getPreparedData();
        if (!$prep) {
            return redirect()->to('wali-murid/dashboard');
        }

        $externalId = $this->request->getGet('external_id');
        $xenditTrxModel = new \Modules\Keuangan\Models\XenditTransaksiModel();
        $trx = $externalId ? $xenditTrxModel->getByExternalId($externalId) : null;

        $tagihan = null;
        $siswa   = null;
        if ($trx) {
            $tagihan = $this->tagihanModel
                ->select('keu_tagihan.*, COALESCE(keu_tagihan.judul_tagihan, keu_jenis_pembayaran.nama_pembayaran) as nama_pos')
                ->join('keu_jenis_pembayaran', 'keu_jenis_pembayaran.id = keu_tagihan.jenis_pembayaran_id', 'left')
                ->find($trx['tagihan_id']);
            $siswa   = $this->siswaModel->find($trx['siswa_id']);
        }

        $data = array_merge($prep, [
            'title'   => 'Konfirmasi Pembayaran Online',
            'trx'     => $trx,
            'tagihan' => $tagihan,
            'siswa'   => $siswa,
        ]);

        return view('Modules\WaliMurid\Views\pembayaran_sukses', $data);
    }

    /**
     * Halaman Pembayaran Batal
     */
    public function pembayaranBatal()
    {
        return redirect()->to(base_url('wali-murid/keuangan'))->with('info', 'Pembayaran online dibatalkan atau belum diselesaikan.');
    }
}
