<?php

namespace Modules\Keuangan\Controllers;

use App\Controllers\BaseController;
use Modules\Keuangan\Models\SetupSiswaModel;
use Modules\Keuangan\Models\JenisPembayaranModel;
use Modules\Akademik\Models\RombelModel;
use Modules\Akademik\Models\RombelSiswaModel;
use Modules\CivitasAkademika\Models\SiswaModel;
use Modules\MasterData\Models\TahunAjaranModel;

class SetupTarif extends BaseController
{
    protected $setupSiswaModel;
    protected $jenisPembayaranModel;
    protected $rombelModel;
    protected $rombelSiswaModel;
    protected $siswaModel;
    protected $tahunAjaranModel;

    public function __construct()
    {
        $this->setupSiswaModel = new SetupSiswaModel();
        $this->jenisPembayaranModel = new JenisPembayaranModel();
        $this->rombelModel = new RombelModel();
        $this->rombelSiswaModel = new RombelSiswaModel();
        $this->siswaModel = new SiswaModel();
        $this->tahunAjaranModel = new TahunAjaranModel();
    }

    private function getActiveTahun()
    {
        $activeTa = $this->tahunAjaranModel->where('is_active', 1)->first();
        return $activeTa ? $activeTa['tahun'] : date('Y') . '/' . (date('Y')+1);
    }

    public function index()
    {
        // Tampilkan daftar rombel untuk memilih siswa, atau langsung cari siswa
        $tahun = $this->getActiveTahun();
        $rombel = $this->rombelModel->where('tahun_ajaran', $tahun)->orderBy('tingkat', 'ASC')->findAll();

        $data = [
            'title' => 'Setup Tarif Siswa',
            'tahun_aktif' => $tahun,
            'rombel' => $rombel
        ];
        return view('Modules\Keuangan\Views\setup_tarif\index', $data);
    }

    public function kelas($rombelId)
    {
        $rombel = $this->rombelModel->find($rombelId);
        if (!$rombel) return redirect()->to('keuangan/setup-tarif')->with('error', 'Kelas tidak ditemukan.');

        $anggota = $this->rombelSiswaModel->getSiswaByRombel($rombelId);

        // Ambil info berapa setup yang sudah dimiliki tiap siswa
        foreach ($anggota as &$a) {
            $a['total_setup'] = $this->setupSiswaModel->where('siswa_id', $a['id'])->countAllResults();
        }

        $jenisPembayaran = $this->jenisPembayaranModel->where('is_active', 1)->orderBy('nama_pembayaran', 'ASC')->findAll();

        $data = [
            'title' => 'Tarif Kelas: ' . $rombel['nama_kelas'],
            'rombel' => $rombel,
            'siswa' => $anggota,
            'jenis_pembayaran' => $jenisPembayaran
        ];
        return view('Modules\Keuangan\Views\setup_tarif\kelas', $data);
    }

    public function detail($siswaId)
    {
        $siswa = $this->siswaModel->find($siswaId);
        if (!$siswa) return redirect()->to('keuangan/setup-tarif')->with('error', 'Siswa tidak ditemukan.');

        $setup = $this->setupSiswaModel->getSetupBySiswa($siswaId);
        $jenisPembayaran = $this->jenisPembayaranModel->where('is_active', 1)->orderBy('nama_pembayaran', 'ASC')->findAll();

        // Filter jenis pembayaran yang belum ada di setup
        $setupIds = array_column($setup, 'jenis_pembayaran_id');
        $availableJenis = array_filter($jenisPembayaran, function($j) use ($setupIds) {
            return !in_array($j['id'], $setupIds);
        });

        $data = [
            'title' => 'Setup Tarif: ' . $siswa['nama_lengkap'],
            'siswa' => $siswa,
            'setup' => $setup,
            'available_jenis' => $availableJenis
        ];
        return view('Modules\Keuangan\Views\setup_tarif\detail', $data);
    }

    public function save()
    {
        $siswaId = $this->request->getPost('siswa_id');
        $jenisId = $this->request->getPost('jenis_pembayaran_id');

        $this->setupSiswaModel->insert([
            'siswa_id' => $siswaId,
            'jenis_pembayaran_id' => $jenisId
        ]);

        return redirect()->back()->with('success', 'Komponen tagihan berhasil ditambahkan ke profil siswa.');
    }

    public function update($id)
    {
        return redirect()->back();
    }

    public function bulkSave()
    {
        $rombelId = $this->request->getPost('rombel_id');
        $jenisId = $this->request->getPost('jenis_pembayaran_id');
        $siswaIdsJson = $this->request->getPost('siswa_ids');
        $action = $this->request->getPost('bulk_action') ?? 'add';
        
        $siswaIds = json_decode($siswaIdsJson, true) ?: [];

        if (empty($siswaIds)) {
            return redirect()->back()->with('error', 'Tidak ada siswa yang dipilih.');
        }
        
        if ($action === 'remove') {
            $count = 0;
            foreach($siswaIds as $sId) {
                // Get the rows that need to be deleted
                $rows = $this->setupSiswaModel->where('siswa_id', $sId)->where('jenis_pembayaran_id', $jenisId)->findAll();
                foreach($rows as $r) {
                    $this->setupSiswaModel->delete($r['id']);
                    $count++;
                }
            }
            return redirect()->back()->with('success', "Berhasil menghapus $count komponen tagihan dari siswa terpilih.");
        }
        
        $inserts = [];
        foreach($siswaIds as $sId) {
            // Check if already exists to prevent duplicate
            $exist = $this->setupSiswaModel->where('siswa_id', $sId)->where('jenis_pembayaran_id', $jenisId)->first();
            if (!$exist) {
                $inserts[] = [
                    'siswa_id' => $sId,
                    'jenis_pembayaran_id' => $jenisId
                ];
            }
        }

        if (count($inserts) > 0) {
            $this->setupSiswaModel->insertBatch($inserts);
            return redirect()->back()->with('success', 'Berhasil menambahkan ' . count($inserts) . ' tagihan secara massal.');
        }

        return redirect()->back()->with('info', 'Semua siswa di kelas ini sudah memiliki komponen tagihan tersebut.');
    }

    public function delete($id)
    {
        $this->setupSiswaModel->delete($id);
        return redirect()->back()->with('success', 'Tarif berhasil dihapus dari profil siswa.');
    }
}
