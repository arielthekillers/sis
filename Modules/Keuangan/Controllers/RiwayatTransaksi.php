<?php

namespace Modules\Keuangan\Controllers;

use App\Controllers\BaseController;
use Modules\Keuangan\Models\PembayaranModel;

class RiwayatTransaksi extends BaseController
{
    protected $pembayaranModel;

    public function __construct()
    {
        $this->pembayaranModel = new PembayaranModel();
    }

    public function index()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $riwayat = $this->pembayaranModel
            ->select('keu_pembayaran.*, siswa.nama_lengkap, siswa.nis')
            ->join('siswa', 'siswa.id = keu_pembayaran.siswa_id')
            ->where('MONTH(keu_pembayaran.tanggal_bayar)', $bulan)
            ->where('YEAR(keu_pembayaran.tanggal_bayar)', $tahun)
            ->orderBy('keu_pembayaran.tanggal_bayar', 'DESC')
            ->paginate(10, 'transaksi');

        $bulan_list = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        $data = [
            'title' => 'Riwayat Transaksi (Kuitansi)',
            'riwayat' => $riwayat,
            'bulan_aktif' => $bulan,
            'tahun_aktif' => $tahun,
            'bulan_list' => $bulan_list,
            'pager' => $this->pembayaranModel->pager
        ];

        return view('Modules\Keuangan\Views\riwayat_transaksi\index', $data);
    }
}
