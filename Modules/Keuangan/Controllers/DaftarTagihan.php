<?php

namespace Modules\Keuangan\Controllers;

use App\Controllers\BaseController;
use Modules\Keuangan\Models\TagihanModel;

class DaftarTagihan extends BaseController
{
    protected $tagihanModel;

    public function __construct()
    {
        $this->tagihanModel = new TagihanModel();
    }

    public function index()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        $bulan_names = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        
        $nama_bulan = $bulan_names[$bulan] ?? '';
        
        $likeString = "%" . $nama_bulan . " " . $tahun;
        
        $tagihanList = $this->tagihanModel
            ->select('keu_tagihan.*, siswa.nama_lengkap, siswa.nis, keu_jenis_pembayaran.nama_pembayaran')
            ->join('siswa', 'siswa.id = keu_tagihan.siswa_id')
            ->join('keu_jenis_pembayaran', 'keu_jenis_pembayaran.id = keu_tagihan.jenis_pembayaran_id')
            ->like('keu_tagihan.judul_tagihan', $likeString, 'before')
            ->orderBy('siswa.nama_lengkap', 'ASC')
            ->paginate(10, 'tagihan');
        
        $db = \Config\Database::connect();
        $stats = $db->table('keu_tagihan')
            ->selectSum('nominal_tagihan', 'total_nominal')
            ->selectSum('nominal_dibayar', 'total_dibayar')
            ->select('SUM(CASE WHEN status = "Belum Bayar" THEN 1 ELSE 0 END) as belum_bayar_count')
            ->select('COUNT(id) as total_tagihan')
            ->like('judul_tagihan', $likeString, 'before')
            ->get()->getRowArray();
        
        $data = [
            'title' => 'Laporan Daftar Tagihan',
            'bulan_list' => $bulan_names,
            'bulan_aktif' => $bulan,
            'tahun_aktif' => $tahun,
            'tagihanList' => $tagihanList,
            'totalTagihan' => $stats['total_tagihan'] ?? 0,
            'totalNominal' => $stats['total_nominal'] ?? 0,
            'totalTerbayar' => $stats['total_dibayar'] ?? 0,
            'belumBayarCount' => $stats['belum_bayar_count'] ?? 0,
            'nama_bulan' => $nama_bulan,
            'pager' => $this->tagihanModel->pager
        ];
        
        return view('Modules\Keuangan\Views\daftar_tagihan\index', $data);
    }

    public function updateNominal()
    {
        $id = $this->request->getPost('tagihan_id');
        $nominalBaru = (float) $this->request->getPost('nominal_baru');

        $tagihan = $this->tagihanModel->find($id);
        if (!$tagihan) {
            return redirect()->back()->with('error', 'Data tagihan tidak ditemukan.');
        }

        $dibayar = (float) $tagihan['nominal_dibayar'];
        
        // Auto-Adjust Status
        if ($dibayar >= $nominalBaru) {
            $statusBaru = 'Lunas';
        } elseif ($dibayar > 0) {
            $statusBaru = 'Mencicil';
        } else {
            $statusBaru = 'Belum Bayar';
        }

        $this->tagihanModel->update($id, [
            'nominal_tagihan' => $nominalBaru,
            'status' => $statusBaru
        ]);

        return redirect()->back()->with('success', 'Nominal tagihan berhasil diperbarui menjadi Rp ' . number_format($nominalBaru, 0, ',', '.') . '. Status otomatis disesuaikan menjadi: ' . $statusBaru);
    }
}
