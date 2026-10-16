<?php

namespace Modules\Web\Controllers;

use App\Controllers\BaseController;
use Modules\CivitasAkademika\Models\SiswaModel;

class Spp extends BaseController
{
    protected SiswaModel $siswaModel;
    protected $db;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->db         = \Config\Database::connect();
    }

    /**
     * Halaman Publik SPP Siswa untuk Orang Tua / Wali Murid
     */
    public function index()
    {
        $nis = trim($this->request->getVar('nis') ?? '');
        $requestedYear = $this->request->getVar('tahun');

        $data = [
            'title'          => 'SPP - ' . app_setting('school_name', 'Sekolah'),
            'description'    => 'Portal informasi SPP dan status pembayaran siswa.',
            'nis'            => $nis,
            'searched'       => false,
            'notFound'       => false,
            'siswa'          => null,
            'rombel'         => null,
            'selectedYear'   => (int) ($requestedYear ?: date('Y')),
            'availableYears' => [],
            'matriksBulan'   => [],
            'matriksSummary' => [
                'countLunas'       => 0,
                'countBelumLunas'  => 0,
                'countMencicil'    => 0,
                'countBelumTerbit' => 0,
                'totalNominal'     => 0,
                'totalTerbayar'    => 0,
                'totalSisa'        => 0,
            ],
            'tagihanList'    => [],
            'stats'          => [
                'totalNominal'     => 0,
                'totalTerbayar'    => 0,
                'totalSisa'        => 0,
                'countBelumBayar'  => 0,
                'countMencicil'    => 0,
                'countLunas'       => 0,
            ],
            'bankInfo'       => [
                'name'   => app_setting('bank_name', '-'),
                'number' => app_setting('bank_account_number', '-'),
                'holder' => app_setting('bank_account_holder', '-'),
            ],
            'waKonfirmasi'   => app_setting('payment_confirmation_phone', app_setting('school_whatsapp', '')),
            'waLink'         => '',
        ];

        if (!empty($nis)) {
            $data['searched'] = true;

            // Cari siswa berdasarkan NIS atau NISN (case-insensitive)
            $siswa = $this->siswaModel
                ->groupStart()
                    ->where('nis', $nis)
                    ->orWhere('nisn', $nis)
                ->groupEnd()
                ->first();

            if (!$siswa) {
                $data['notFound'] = true;
                return view('Modules\Web\Views\spp', $data);
            }

            $data['siswa'] = $siswa;

            // Cari data rombel / kelas siswa saat ini
            $rs = $this->db->table('rombel_siswa')
                ->select('kelas.nama_kelas, kelas.tingkat')
                ->join('kelas', 'kelas.id = rombel_siswa.rombel_id')
                ->where('rombel_siswa.siswa_id', $siswa['id'])
                ->orderBy('rombel_siswa.id', 'DESC')
                ->get()->getRowArray();

            $data['rombel'] = $rs['nama_kelas'] ?? '-';

            // Ambil semua tagihan / SPP untuk siswa ini beserta riwayat pembayaran
            $tagihanList = $this->db->table('keu_tagihan')
                ->select('keu_tagihan.*, keu_jenis_pembayaran.nama_pembayaran, MAX(keu_pembayaran.tanggal_bayar) as tanggal_bayar, MAX(keu_pembayaran.metode_pembayaran) as metode_pembayaran')
                ->join('keu_jenis_pembayaran', 'keu_jenis_pembayaran.id = keu_tagihan.jenis_pembayaran_id', 'left')
                ->join('keu_pembayaran_detail', 'keu_pembayaran_detail.tagihan_id = keu_tagihan.id', 'left')
                ->join('keu_pembayaran', 'keu_pembayaran.id = keu_pembayaran_detail.pembayaran_id', 'left')
                ->where('keu_tagihan.siswa_id', $siswa['id'])
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

            // Tahun yang ditampilkan: Tahun ini, -1 (tahun lalu), dan +1 (tahun depan)
            $curYear = (int) date('Y');
            $availableYears = [$curYear - 1, $curYear, $curYear + 1];
            $data['availableYears'] = $availableYears;

            // Tentukan tahun terpilih (default tahun berjalan)
            $selectedYear = (int) ($requestedYear ?: $curYear);
            if (!in_array($selectedYear, $availableYears)) {
                $selectedYear = $curYear;
            }
            $data['selectedYear'] = $selectedYear;

            $totalSisa = max(0, $totalNominal - $totalTerbayar);
            $data['tagihanList'] = $tagihanList;
            $data['stats'] = [
                'totalNominal'     => $totalNominal,
                'totalTerbayar'    => $totalTerbayar,
                'totalSisa'        => $totalSisa,
                'countBelumBayar'  => $countBelumBayar,
                'countMencicil'    => $countMencicil,
                'countLunas'       => $countLunas,
            ];

            // --------------------------------------------------------
            // Buat Tabel Matriks 12 Bulan (Januari s/d Desember)
            // --------------------------------------------------------
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

            $matriksBulan = [];
            for ($i = 1; $i <= 12; $i++) {
                $matriksBulan[$i] = [
                    'nomor'             => $i,
                    'nama_bulan'        => $namaBulan[$i],
                    'ada_tagihan'       => false,
                    'items'             => [],
                    'total_nominal'     => 0,
                    'total_dibayar'     => 0,
                    'total_sisa'        => 0,
                    'status'            => 'Belum Terbit', // 'Lunas', 'Mencicil', 'Belum Lunas', 'Belum Terbit'
                    'keterangan'        => 'Belum Terbit',
                    'tanggal_bayar'     => null,
                    'metode_pembayaran' => null,
                ];
            }

            // Petakan tagihan pada tahun terpilih ke 12 bulan
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

            // Evaluasi status dan ringkasan tahunan
            $matriksSummary = [
                'countLunas'       => 0,
                'countBelumLunas'  => 0,
                'countMencicil'    => 0,
                'countBelumTerbit' => 0,
                'totalNominal'     => 0,
                'totalTerbayar'    => 0,
                'totalSisa'        => 0,
            ];

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

            $data['matriksBulan']   = $matriksBulan;
            $data['matriksSummary'] = $matriksSummary;

            // Siapkan link WhatsApp Konfirmasi
            $phone = preg_replace('/[^0-9]/', '', $data['waKonfirmasi']);
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            }

            $pesanKonfirmasi = "Halo Admin/Bendahara " . app_setting('school_name', 'Sekolah') . ",\n\n"
                . "Saya wali murid dari ananda *{$siswa['nama_lengkap']}* (NIS: {$siswa['nis']})\n"
                . "Kelas: {$data['rombel']}\n\n"
                . "Saya ingin melakukan konfirmasi bukti pembayaran SPP sekolah ananda. Berikut kami lampirkan bukti transfernya. Terima kasih.";

            $data['waLink'] = 'https://api.whatsapp.com/send?phone=' . $phone . '&text=' . rawurlencode($pesanKonfirmasi);
        }

        return view('Modules\Web\Views\spp', $data);
    }

    /**
     * Helper untuk mengekstrak nomor bulan (1-12) dan tahun (YYYY)
     */
    private function parseBulanTahun(array $tagihan): array
    {
        $year  = null;
        $month = null;

        // 1. Ekstrak dari jatuh_tempo (YYYY-MM-DD)
        if (!empty($tagihan['jatuh_tempo']) && $tagihan['jatuh_tempo'] !== '0000-00-00') {
            $ts = strtotime($tagihan['jatuh_tempo']);
            if ($ts) {
                $year  = (int) date('Y', $ts);
                $month = (int) date('n', $ts);
            }
        }

        // 2. Ekstrak dari nama bulan dalam judul_tagihan (misal: "SPP Reguler Oktober 2026")
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

        // 3. Ekstrak 4-digit tahun dari judul_tagihan jika tahun jatuh_tempo belum ada
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
