<?php

namespace Modules\Keuangan\Controllers;

use App\Controllers\BaseController;
use Modules\Keuangan\Models\SetupSiswaModel;
use Modules\Keuangan\Models\JenisPembayaranModel;
use Modules\Keuangan\Models\TagihanModel;
use Modules\MasterData\Models\TahunAjaranModel;

class GenerateTagihan extends BaseController
{
    protected $setupSiswaModel;
    protected $jenisPembayaranModel;
    protected $tagihanModel;
    protected $tahunAjaranModel;

    public function __construct()
    {
        $this->setupSiswaModel = new SetupSiswaModel();
        $this->jenisPembayaranModel = new JenisPembayaranModel();
        $this->tagihanModel = new TagihanModel();
        $this->tahunAjaranModel = new TahunAjaranModel();
    }

    private function getActiveTahun()
    {
        $activeTa = $this->tahunAjaranModel->where('is_active', 1)->first();
        return $activeTa ? $activeTa['tahun'] : date('Y') . '/' . (date('Y')+1);
    }

    public function index()
    {
        $bulan_list = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        $data = [
            'title' => 'Terbitkan Tagihan Bulanan',
            'bulan_list' => $bulan_list,
            'tahun_aktif' => $this->getActiveTahun()
        ];
        return view('Modules\Keuangan\Views\generate_tagihan\index', $data);
    }

    public function process()
    {
        $bulan = $this->request->getPost('bulan');
        $tahun = $this->request->getPost('tahun');
        $tahunAjaran = $this->getActiveTahun();

        $bulan_names = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        $nama_bulan = $bulan_names[$bulan];
        $jatuh_tempo = date("Y-m-t", strtotime("$tahun-$bulan-01")); // akhir bulan
        
        // Ambil semua master tagihan yang bersifat Bulanan
        $jenisBulanan = $this->jenisPembayaranModel->where('is_active', 1)->where('tipe_siklus', 'Bulanan')->findAll();
        if(empty($jenisBulanan)) {
            return redirect()->back()->with('error', 'Tidak ada komponen tagihan Bulanan yang aktif.');
        }
        $jenisIds = array_column($jenisBulanan, 'id');
        $mapJenis = [];
        foreach($jenisBulanan as $j) $mapJenis[$j['id']] = $j;

        // Ambil semua setup siswa yang jenisnya termasuk Bulanan, DAN siswanya berstatus Aktif
        $semuaSetup = $this->setupSiswaModel
            ->select('keu_setup_siswa.*')
            ->join('siswa', 'siswa.id = keu_setup_siswa.siswa_id')
            ->whereIn('keu_setup_siswa.jenis_pembayaran_id', $jenisIds)
            ->where('siswa.status_siswa', 'Aktif')
            ->findAll();

        $inserts = [];
        $skipped = 0;
        
        foreach($semuaSetup as $setup) {
            $j = $mapJenis[$setup['jenis_pembayaran_id']];
            $judul = $j['nama_pembayaran'] . " " . $nama_bulan . " " . $tahun;
            
            // Cek apakah tagihan ini sudah ada (mencegah double generate)
            $exists = $this->tagihanModel
                ->where('siswa_id', $setup['siswa_id'])
                ->where('jenis_pembayaran_id', $setup['jenis_pembayaran_id'])
                ->where('judul_tagihan', $judul)
                ->first();
                
            if(!$exists) {
                $inserts[] = [
                    'siswa_id' => $setup['siswa_id'],
                    'jenis_pembayaran_id' => $setup['jenis_pembayaran_id'],
                    'judul_tagihan' => $judul,
                    'nominal_tagihan' => $j['nominal'], // mengambil harga terkini dari master
                    'nominal_dibayar' => 0,
                    'status' => 'Belum Bayar',
                    'jatuh_tempo' => $jatuh_tempo,
                    'tahun_ajaran' => $tahunAjaran
                ];
            } else {
                $skipped++;
            }
        }

        if(count($inserts) > 0) {
            // Kita pecah insertnya per 100 baris agar ringan kalau datanya besar
            $chunks = array_chunk($inserts, 100);
            foreach($chunks as $chunk) {
                $this->tagihanModel->insertBatch($chunk);
            }
            return redirect()->back()->with('success', 'Berhasil menerbitkan ' . count($inserts) . ' tagihan untuk bulan ' . $nama_bulan . '. (Dilewati: ' . $skipped . ' karena sudah ada)');
        }

        return redirect()->back()->with('info', 'Tidak ada tagihan baru yang diterbitkan. Semua tagihan untuk bulan tersebut sudah ter-generate (Dilewati: ' . $skipped . ').');
    }

    public function rollback()
    {
        $bulan = $this->request->getPost('bulan_rollback');
        $tahun = $this->request->getPost('tahun_rollback');

        $bulan_names = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        $nama_bulan = $bulan_names[$bulan] ?? '';

        // Kita cari tagihan yang mengandung kata bulan & tahun tersebut, dan yang berstatus 'Belum Bayar'
        $likeString = "%" . $nama_bulan . " " . $tahun;
        
        $tagihanAkanDihapus = $this->tagihanModel
            ->like('judul_tagihan', $likeString, 'before') // "... Januari 2026"
            ->where('status', 'Belum Bayar')
            ->findAll();

        if (empty($tagihanAkanDihapus)) {
            return redirect()->back()->with('error', 'Tidak ada tagihan berstatus "Belum Bayar" yang dapat dibatalkan untuk bulan ' . $nama_bulan . ' ' . $tahun . '.');
        }

        $ids = array_column($tagihanAkanDihapus, 'id');
        
        // Hapus massal
        $this->tagihanModel->whereIn('id', $ids)->delete();

        return redirect()->back()->with('success', 'Berhasil membatalkan (menghapus) ' . count($ids) . ' tagihan untuk bulan ' . $nama_bulan . ' ' . $tahun . '. Catatan: Tagihan yang sudah dicicil/lunas tidak ikut dihapus.');
    }
}
