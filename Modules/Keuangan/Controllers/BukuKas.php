<?php

namespace Modules\Keuangan\Controllers;

use App\Controllers\BaseController;
use Modules\Keuangan\Models\PembayaranModel;
use Modules\Keuangan\Models\PengeluaranModel;

class BukuKas extends BaseController
{
    protected $pembayaranModel;
    protected $pengeluaranModel;

    public function __construct()
    {
        $this->pembayaranModel  = new PembayaranModel();
        $this->pengeluaranModel = new PengeluaranModel();
    }

    public function index()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $tab   = $this->request->getGet('tab') ?? 'rekap'; // 'rekap' atau 'detail'

        $startDateBulan = "$tahun-$bulan-01";
        $endDateBulan   = date("Y-m-t", strtotime($startDateBulan));

        // 1. Hitung Saldo Awal (Akumulasi sebelum tanggal 1 bulan ini)
        $db = \Config\Database::connect();

        $pemasukanLalu = $db->table('keu_pembayaran')
            ->selectSum('total_bayar')
            ->where('tanggal_bayar <', $startDateBulan . ' 00:00:00')
            ->get()->getRowArray()['total_bayar'] ?? 0;

        $pengeluaranLalu = $db->table('keu_pengeluaran')
            ->selectSum('nominal')
            ->where('tanggal <', $startDateBulan)
            ->get()->getRowArray()['nominal'] ?? 0;

        $saldoAwal = (float) $pemasukanLalu - (float) $pengeluaranLalu;

        // 2. Tarik Semua Pembayaran (Pemasukan) Bulan Ini
        $pembayaran = $this->pembayaranModel
            ->select('keu_pembayaran.*, siswa.nama_lengkap as nama_siswa')
            ->join('siswa', 'siswa.id = keu_pembayaran.siswa_id', 'left')
            ->where('tanggal_bayar >=', $startDateBulan . ' 00:00:00')
            ->where('tanggal_bayar <=', $endDateBulan . ' 23:59:59')
            ->orderBy('tanggal_bayar', 'ASC')
            ->findAll();

        // 3. Tarik Semua Pengeluaran Bulan Ini
        $pengeluaran = $this->pengeluaranModel
            ->select('keu_pengeluaran.*, keu_kategori_pengeluaran.nama_kategori')
            ->join('keu_kategori_pengeluaran', 'keu_kategori_pengeluaran.id = keu_pengeluaran.kategori_id', 'left')
            ->where('tanggal >=', $startDateBulan)
            ->where('tanggal <=', $endDateBulan)
            ->orderBy('tanggal', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        // 4. Proses Rekapitulasi Harian
        $laporanHarian = [];
        $totalMasuk = 0;
        $totalKeluar = 0;
        $totalTunai = 0;
        $totalTransfer = 0;
        $totalQris = 0;

        // Group Pemasukan
        foreach ($pembayaran as $p) {
            $tgl = date('Y-m-d', strtotime($p['tanggal_bayar']));
            if (!isset($laporanHarian[$tgl])) {
                $laporanHarian[$tgl] = [
                    'tanggal'           => $tgl,
                    'tunai'             => 0,
                    'transfer'          => 0,
                    'qris'              => 0,
                    'pemasukan'         => 0,
                    'pengeluaran'       => 0,
                    'transaksi_masuk'   => 0,
                    'transaksi_keluar'  => 0,
                ];
            }

            $nominal = (float) $p['total_bayar'];
            if ($p['metode_pembayaran'] == 'Tunai') {
                $laporanHarian[$tgl]['tunai'] += $nominal;
                $totalTunai += $nominal;
            } elseif ($p['metode_pembayaran'] == 'Transfer Bank') {
                $laporanHarian[$tgl]['transfer'] += $nominal;
                $totalTransfer += $nominal;
            } elseif ($p['metode_pembayaran'] == 'QRIS') {
                $laporanHarian[$tgl]['qris'] += $nominal;
                $totalQris += $nominal;
            }

            $laporanHarian[$tgl]['pemasukan'] += $nominal;
            $laporanHarian[$tgl]['transaksi_masuk']++;
            $totalMasuk += $nominal;
        }

        // Group Pengeluaran
        foreach ($pengeluaran as $k) {
            $tgl = $k['tanggal'];
            if (!isset($laporanHarian[$tgl])) {
                $laporanHarian[$tgl] = [
                    'tanggal'           => $tgl,
                    'tunai'             => 0,
                    'transfer'          => 0,
                    'qris'              => 0,
                    'pemasukan'         => 0,
                    'pengeluaran'       => 0,
                    'transaksi_masuk'   => 0,
                    'transaksi_keluar'  => 0,
                ];
            }

            $nominal = (float) $k['nominal'];
            $laporanHarian[$tgl]['pengeluaran'] += $nominal;
            $laporanHarian[$tgl]['transaksi_keluar']++;
            $totalKeluar += $nominal;
        }

        // Urutkan tanggal secara kronologis (ASC) dan hitung Saldo Berjalan Harian
        ksort($laporanHarian);
        $runningSaldo = $saldoAwal;
        foreach ($laporanHarian as &$row) {
            $runningSaldo = $runningSaldo + $row['pemasukan'] - $row['pengeluaran'];
            $row['saldo_berjalan'] = $runningSaldo;
        }
        unset($row);

        // 5. Buat Jurnal Kas Terperinci (Gabungan Semua Transaksi Berurut)
        $jurnalDetail = [];
        foreach ($pembayaran as $p) {
            $jurnalDetail[] = [
                'waktu'       => $p['tanggal_bayar'],
                'tanggal'     => date('Y-m-d', strtotime($p['tanggal_bayar'])),
                'kode'        => $p['no_kwitansi'],
                'keterangan'  => 'Penerimaan Kasir - Siswa: ' . ($p['nama_siswa'] ?: 'Umum') . ($p['keterangan'] ? ' (' . $p['keterangan'] . ')' : ''),
                'tipe'        => 'MASUK',
                'pemasukan'   => (float) $p['total_bayar'],
                'pengeluaran' => 0,
                'metode'      => $p['metode_pembayaran'],
            ];
        }

        foreach ($pengeluaran as $k) {
            $jurnalDetail[] = [
                'waktu'       => $k['tanggal'] . ' ' . date('H:i:s', strtotime($k['created_at'] ?? '12:00:00')),
                'tanggal'     => $k['tanggal'],
                'kode'        => $k['kode_transaksi'],
                'keterangan'  => '[' . ($k['nama_kategori'] ?: 'Umum') . '] ' . $k['keperluan'] . ($k['penerima'] ? ' (Penerima: ' . $k['penerima'] . ')' : ''),
                'tipe'        => 'KELUAR',
                'pemasukan'   => 0,
                'pengeluaran' => (float) $k['nominal'],
                'metode'      => $k['metode_pembayaran'],
            ];
        }

        // Urutkan jurnal kronologis berdasarkan waktu
        usort($jurnalDetail, function($a, $b) {
            return strcmp($a['waktu'], $b['waktu']);
        });

        // Hitung saldo berjalan pada jurnal detail
        $runningJurnal = $saldoAwal;
        foreach ($jurnalDetail as &$item) {
            $runningJurnal = $runningJurnal + $item['pemasukan'] - $item['pengeluaran'];
            $item['saldo_berjalan'] = $runningJurnal;
        }
        unset($item);

        $saldoAkhir = $saldoAwal + $totalMasuk - $totalKeluar;

        $bulan_list = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        $data = [
            'title'            => 'Buku Kas Umum (Laporan Arus Kas)',
            'laporanHarian'    => array_values($laporanHarian),
            'jurnalDetail'     => $jurnalDetail,
            'bulan_aktif'      => $bulan,
            'tahun_aktif'      => $tahun,
            'bulan_list'       => $bulan_list,
            'nama_bulan'       => $bulan_list[$bulan] ?? '',
            'active_tab'       => $tab,
            'saldoAwal'        => $saldoAwal,
            'totalMasuk'       => $totalMasuk,
            'totalKeluar'      => $totalKeluar,
            'saldoAkhir'       => $saldoAkhir,
            'totalTunai'       => $totalTunai,
            'totalTransfer'    => $totalTransfer,
            'totalQris'        => $totalQris,
            'totalTransaksi'   => count($pembayaran) + count($pengeluaran),
        ];

        return view('Modules\Keuangan\Views\buku_kas\index', $data);
    }
}
