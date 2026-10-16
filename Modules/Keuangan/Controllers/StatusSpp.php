<?php

namespace Modules\Keuangan\Controllers;

use App\Controllers\BaseController;
use Modules\CivitasAkademika\Models\SiswaModel;
use Modules\Keuangan\Models\TagihanModel;
use Modules\Akademik\Models\RombelSiswaModel;
use Modules\Akademik\Models\RombelModel;

class StatusSpp extends BaseController
{
    protected SiswaModel $siswaModel;
    protected TagihanModel $tagihanModel;
    protected $db;

    public function __construct()
    {
        $this->siswaModel   = new SiswaModel();
        $this->tagihanModel = new TagihanModel();
        $this->db           = \Config\Database::connect();
    }

    /**
     * Halaman Cek Status SPP & Pembayaran Siswa untuk Guru / Admin
     */
    public function index($siswaIdParam = null)
    {
        $siswaId = $siswaIdParam ?: $this->request->getVar('siswa_id');
        $searchQ = trim($this->request->getVar('q') ?? '');
        $requestedYear = $this->request->getVar('tahun');

        // Ambil daftar seluruh siswa aktif untuk pencarian / dropdown cepat
        $allSiswa = $this->db->table('siswa')
            ->select('siswa.id, siswa.nis, siswa.nisn, siswa.nama_lengkap, siswa.foto, kelas.nama_kelas')
            ->join('rombel_siswa', 'rombel_siswa.siswa_id = siswa.id', 'left')
            ->join('kelas', 'kelas.id = rombel_siswa.rombel_id', 'left')
            ->where('siswa.status_siswa', 'Aktif')
            ->groupBy('siswa.id')
            ->orderBy('siswa.nama_lengkap', 'ASC')
            ->get()->getResultArray();

        // Jika ada keyword pencarian tapi siswaId belum dipilih, cari yang cocok
        if (empty($siswaId) && !empty($searchQ)) {
            foreach ($allSiswa as $s) {
                if (stripos($s['nama_lengkap'], $searchQ) !== false || stripos($s['nis'] ?? '', $searchQ) !== false) {
                    $siswaId = $s['id'];
                    break;
                }
            }
        }

        // Jika masih belum ada siswaId, default ke siswa pertama
        if (empty($siswaId) && !empty($allSiswa)) {
            $siswaId = $allSiswa[0]['id'];
        }

        $siswa = null;
        $rombel = '-';
        $tagihanList = [];
        $pembayaranList = [];
        $stats = [
            'totalNominal'     => 0,
            'totalTerbayar'    => 0,
            'totalSisa'        => 0,
            'countBelumBayar'  => 0,
            'countMencicil'    => 0,
            'countLunas'       => 0,
        ];
        $isAman = true;

        // Setup 3 tahun (Tahun lalu, Tahun ini, Tahun depan)
        $curYear = (int) date('Y');
        $availableYears = [$curYear - 1, $curYear, $curYear + 1];
        $selectedYear = (int) ($requestedYear ?: $curYear);
        if (!in_array($selectedYear, $availableYears)) {
            $selectedYear = $curYear;
        }

        $matriksBulan = [];
        $matriksSummary = [
            'countLunas'       => 0,
            'countBelumLunas'  => 0,
            'countMencicil'    => 0,
            'countBelumTerbit' => 0,
            'totalNominal'     => 0,
            'totalTerbayar'    => 0,
            'totalSisa'        => 0,
        ];

        $waOrtuLink = '';

        if (!empty($siswaId)) {
            $siswa = $this->siswaModel->find($siswaId);
            if ($siswa) {
                // Cari data rombel
                $rs = $this->db->table('rombel_siswa')
                    ->select('kelas.nama_kelas, kelas.tingkat')
                    ->join('kelas', 'kelas.id = rombel_siswa.rombel_id')
                    ->where('rombel_siswa.siswa_id', $siswaId)
                    ->orderBy('rombel_siswa.id', 'DESC')
                    ->get()->getRowArray();
                $rombel = $rs['nama_kelas'] ?? '-';

                // Ambil semua tagihan SPP siswa
                $tagihanList = $this->db->table('keu_tagihan')
                    ->select('keu_tagihan.*, keu_jenis_pembayaran.nama_pembayaran, MAX(keu_pembayaran.tanggal_bayar) as tanggal_bayar, MAX(keu_pembayaran.metode_pembayaran) as metode_pembayaran')
                    ->join('keu_jenis_pembayaran', 'keu_jenis_pembayaran.id = keu_tagihan.jenis_pembayaran_id', 'left')
                    ->join('keu_pembayaran_detail', 'keu_pembayaran_detail.tagihan_id = keu_tagihan.id', 'left')
                    ->join('keu_pembayaran', 'keu_pembayaran.id = keu_pembayaran_detail.pembayaran_id', 'left')
                    ->where('keu_tagihan.siswa_id', $siswaId)
                    ->groupBy('keu_tagihan.id')
                    ->orderBy('keu_tagihan.jatuh_tempo', 'DESC')
                    ->orderBy('keu_tagihan.id', 'DESC')
                    ->get()->getResultArray();

                $totalNominal     = 0;
                $totalTerbayar    = 0;
                $countBelumBayar  = 0;
                $countMencicil    = 0;
                $countLunas       = 0;

                foreach ($tagihanList as &$t) {
                    $nom = (float) $t['nominal_tagihan'];
                    $bay = (float) $t['nominal_dibayar'];
                    $t['sisa'] = max(0, $nom - $bay);

                    $totalNominal  += $nom;
                    $totalTerbayar += $bay;

                    if ($t['status'] === 'Lunas') {
                        $countLunas++;
                    } elseif ($t['status'] === 'Mencicil') {
                        $countMencicil++;
                    } else {
                        $countBelumBayar++;
                    }

                    // Ekstrak bulan & tahun
                    [$mNum, $yNum] = $this->parseBulanTahun($t);
                    $t['parsed_bulan'] = $mNum;
                    $t['parsed_tahun'] = $yNum;
                }
                unset($t);

                $totalSisa = max(0, $totalNominal - $totalTerbayar);
                $isAman = ($totalSisa <= 0);

                $stats = [
                    'totalNominal'     => $totalNominal,
                    'totalTerbayar'    => $totalTerbayar,
                    'totalSisa'        => $totalSisa,
                    'countBelumBayar'  => $countBelumBayar,
                    'countMencicil'    => $countMencicil,
                    'countLunas'       => $countLunas,
                ];

                // ----------------------------------------------------
                // Buat Tabel Matriks 12 Bulan (Januari s/d Desember)
                // ----------------------------------------------------
                $namaBulan = [
                    1  => 'Januari',
                    2  => 'Februari',
                    3  => 'Maret',
                    4  => 'April',
                    5  => 'Mei',
                    6  => 'Juni',
                    7  => 'Juli',
                    8  => 'Agustus',
                    9  => 'September',
                    10 => 'Oktober',
                    11 => 'November',
                    12 => 'Desember',
                ];

                for ($i = 1; $i <= 12; $i++) {
                    $matriksBulan[$i] = [
                        'nomor'             => $i,
                        'nama_bulan'        => $namaBulan[$i],
                        'ada_tagihan'       => false,
                        'items'             => [],
                        'total_nominal'     => 0,
                        'total_dibayar'     => 0,
                        'total_sisa'        => 0,
                        'status'            => 'Belum Terbit',
                        'keterangan'        => 'Belum Terbit',
                        'tanggal_bayar'     => null,
                        'metode_pembayaran' => null,
                    ];
                }

                // Petakan tagihan pada tahun terpilih
                foreach ($tagihanList as $t) {
                    if ($t['parsed_tahun'] === $selectedYear) {
                        $m = $t['parsed_bulan'];
                        if (isset($matriksBulan[$m])) {
                            $matriksBulan[$m]['ada_tagihan'] = true;
                            $matriksBulan[$m]['items'][]     = $t;
                            $matriksBulan[$m]['total_nominal'] += (float) $t['nominal_tagihan'];
                            $matriksBulan[$m]['total_dibayar'] += (float) $t['nominal_dibayar'];
                            $matriksBulan[$m]['total_sisa']    += (float) $t['sisa'];

                            if (!empty($t['tanggal_bayar'])) {
                                $matriksBulan[$m]['tanggal_bayar'] = $t['tanggal_bayar'];
                            }
                            if (!empty($t['metode_pembayaran'])) {
                                $matriksBulan[$m]['metode_pembayaran'] = $t['metode_pembayaran'];
                            }
                        }
                    }
                }

                // Hitung status tiap bulan dan rekap tahunan
                foreach ($matriksBulan as &$mb) {
                    if ($mb['ada_tagihan']) {
                        $matriksSummary['totalNominal']  += $mb['total_nominal'];
                        $matriksSummary['totalTerbayar'] += $mb['total_dibayar'];
                        $matriksSummary['totalSisa']     += $mb['total_sisa'];

                        if ($mb['total_sisa'] <= 0 && $mb['total_nominal'] > 0) {
                            $mb['status']     = 'Lunas';
                            $mb['keterangan'] = 'Lunas';
                            $matriksSummary['countLunas']++;
                        } elseif ($mb['total_dibayar'] > 0 && $mb['total_sisa'] > 0) {
                            $mb['status']     = 'Mencicil';
                            $mb['keterangan'] = 'Mencicil';
                            $matriksSummary['countMencicil']++;
                        } else {
                            $mb['status']     = 'Belum Lunas';
                            $mb['keterangan'] = 'Belum Lunas';
                            $matriksSummary['countBelumLunas']++;
                        }
                    } else {
                        $mb['status']     = 'Belum Terbit';
                        $mb['keterangan'] = 'Belum Terbit';
                        $matriksSummary['countBelumTerbit']++;
                    }
                }

                // Ambil riwayat kwitansi pembayaran di kasir
                $pembayaranList = $this->db->table('keu_pembayaran')
                    ->where('siswa_id', $siswaId)
                    ->orderBy('tanggal_bayar', 'DESC')
                    ->get()->getResultArray();

                // Generate WhatsApp Link untuk orang tua jika guru ingin mengirim status
                $phone = preg_replace('/[^0-9]/', '', $siswa['no_hp_ortu'] ?? '');
                if (!empty($phone)) {
                    if (str_starts_with($phone, '0')) {
                        $phone = '62' . substr($phone, 1);
                    }
                    $statusText = $isAman
                        ? "Seluruh kewajiban pembayaran SPP sekolah ananda saat ini sudah *LUNAS (AMAN)*. Terima kasih atas kerja sama Bapak/Ibu."
                        : "Saat ini terdapat kewajiban pembayaran SPP ananda yang belum selesai sebesar *" . rupiah($totalSisa) . "*.\n\nBapak/Ibu dapat melakukan pembayaran di loket kasir sekolah atau transfer ke rekening resmi sekolah.";

                    $waMsg = "Halo Bapak/Ibu wali dari ananda *{$siswa['nama_lengkap']}* (NIS: {$siswa['nis']}),\n\n"
                        . "Berikut kami informasikan status pembayaran SPP ananda di " . app_setting('school_name', 'Sekolah') . ":\n\n"
                        . $statusText . "\n\n"
                        . "Rincian tabel SPP dapat dilihat mandiri di: " . base_url('spp') . "?nis=" . urlencode($siswa['nis'] ?? '') . "\n\n"
                        . "Salam hangat,\nTata Usaha / Guru " . app_setting('school_name', 'Sekolah');

                    $waOrtuLink = "https://api.whatsapp.com/send?phone=" . $phone . "&text=" . rawurlencode($waMsg);
                }
            }
        }

        $data = [
            'title'          => 'Status SPP Siswa',
            'allSiswa'       => $allSiswa,
            'selectedSiswaId'=> $siswaId,
            'siswa'          => $siswa,
            'rombel'         => $rombel,
            'isAman'         => $isAman,
            'stats'          => $stats,
            'availableYears' => $availableYears,
            'selectedYear'   => $selectedYear,
            'matriksBulan'   => $matriksBulan,
            'matriksSummary' => $matriksSummary,
            'tagihanList'    => $tagihanList,
            'pembayaranList' => $pembayaranList,
            'waOrtuLink'     => $waOrtuLink,
        ];

        return view('Modules\Keuangan\Views\status_spp\index', $data);
    }

    /**
     * Helper untuk mengekstrak nomor bulan (1-12) dan tahun (YYYY)
     */
    private function parseBulanTahun(array $tagihan): array
    {
        $year  = null;
        $month = null;

        if (!empty($tagihan['jatuh_tempo']) && $tagihan['jatuh_tempo'] !== '0000-00-00') {
            $ts = strtotime($tagihan['jatuh_tempo']);
            if ($ts) {
                $year  = (int) date('Y', $ts);
                $month = (int) date('n', $ts);
            }
        }

        $bulanMap = [
            'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4,
            'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8,
            'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12,
        ];

        $judulLower = strtolower($tagihan['judul_tagihan'] ?? '');
        foreach ($bulanMap as $namaBulan => $nomorBulan) {
            if (str_contains($judulLower, $namaBulan)) {
                $month = $nomorBulan;
                break;
            }
        }

        if (!$year && preg_match('/\b(20\d{2})\b/', $tagihan['judul_tagihan'] ?? '', $matches)) {
            $year = (int) $matches[1];
        }

        if (!$year) {
            $year = (int) date('Y');
        }
        if (!$month) {
            $month = (int) date('n');
        }

        return [$month, $year];
    }
}
