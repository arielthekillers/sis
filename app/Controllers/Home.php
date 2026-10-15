<?php

namespace App\Controllers;

use Modules\CivitasAkademika\Models\SiswaModel;
use Modules\Keuangan\Models\PembayaranModel;
use Modules\Keuangan\Models\TagihanModel;

class Home extends BaseController
{
    public function index(): string
    {
        $siswaModel = new SiswaModel();
        $pembayaranModel = new PembayaranModel();
        $tagihanModel = new TagihanModel();

        // 1. Total Siswa Aktif
        $totalSiswa = $siswaModel->where('status_siswa', 'Aktif')->countAllResults();

        // 2. Pendapatan Bulan Ini
        $bulanIni = date('m');
        $tahunIni = date('Y');
        $pendapatan = $pembayaranModel
            ->selectSum('total_bayar')
            ->where('MONTH(tanggal_bayar)', $bulanIni)
            ->where('YEAR(tanggal_bayar)', $tahunIni)
            ->first();
        $totalPendapatan = $pendapatan['total_bayar'] ?? 0;

        // 3. Total Tunggakan (Belum Dibayar)
        // Hitung sum(nominal_tagihan - nominal_dibayar) dari tagihan yang status != Lunas
        $tunggakan = $tagihanModel
            ->selectSum('nominal_tagihan')
            ->selectSum('nominal_dibayar')
            ->whereIn('status', ['Belum Bayar', 'Mencicil'])
            ->first();
        $totalTunggakan = ($tunggakan['nominal_tagihan'] ?? 0) - ($tunggakan['nominal_dibayar'] ?? 0);

        // 4. Pengeluaran Bulan Ini
        $pengeluaranModel = new \Modules\Keuangan\Models\PengeluaranModel();
        $pengeluaran = $pengeluaranModel
            ->selectSum('nominal')
            ->where('MONTH(tanggal)', $bulanIni)
            ->where('YEAR(tanggal)', $tahunIni)
            ->first();
        $totalPengeluaran = $pengeluaran['nominal'] ?? 0;

        // 5. Riwayat Transaksi Terbaru (5 Terakhir)
        $transaksiTerbaru = $pembayaranModel
            ->select('keu_pembayaran.*, siswa.nama_lengkap')
            ->join('siswa', 'siswa.id = keu_pembayaran.siswa_id')
            ->orderBy('keu_pembayaran.tanggal_bayar', 'DESC')
            ->limit(5)
            ->findAll();

        $data = [
            'totalSiswa'       => $totalSiswa,
            'totalPendapatan'  => $totalPendapatan,
            'totalPengeluaran' => $totalPengeluaran,
            'totalTunggakan'   => $totalTunggakan,
            'transaksiTerbaru' => $transaksiTerbaru
        ];

        return view('home', $data);
    }
}
