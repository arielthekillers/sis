<?php

namespace Modules\Akademik\Controllers;

use App\Controllers\BaseController;
use Modules\Akademik\Models\RombelModel;
use Modules\Akademik\Models\RombelSiswaModel;
use Modules\CivitasAkademika\Models\SiswaModel;
use Modules\MasterData\Models\TahunAjaranModel;

class AnggotaRombel extends BaseController
{
    protected $rombelModel;
    protected $rombelSiswaModel;
    protected $siswaModel;
    protected $tahunAjaranModel;

    public function __construct()
    {
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
        $tahun = $this->getActiveTahun();
        $rombel = $this->rombelModel->where('tahun_ajaran', $tahun)->orderBy('tingkat', 'ASC')->orderBy('nama_kelas', 'ASC')->findAll();

        // Hitung jumlah siswa per rombel
        foreach ($rombel as &$r) {
            $r['jumlah_siswa'] = $this->rombelSiswaModel->where('rombel_id', $r['id'])->countAllResults();
        }

        $data = [
            'title' => 'Penempatan Anggota Rombel',
            'tahun_aktif' => $tahun,
            'rombel' => $rombel
        ];
        return view('Modules\Akademik\Views\anggota_rombel\index', $data);
    }

    public function detail($rombelId)
    {
        $rombel = $this->rombelModel->find($rombelId);
        if (!$rombel) {
            return redirect()->to('akademik/anggota-rombel')->with('error', 'Data Rombel tidak ditemukan.');
        }

        $anggota = $this->rombelSiswaModel->getSiswaByRombel($rombelId);
        
        // Ambil semua siswa aktif yang belum ada di rombel INI
        $anggotaIds = array_column($anggota, 'id');
        
        $siswaBuilder = $this->siswaModel->where('status_siswa', 'Aktif')->where('is_active', 1);
        if (!empty($anggotaIds)) {
            $siswaBuilder->whereNotIn('id', $anggotaIds);
        }
        $calonSiswa = $siswaBuilder->orderBy('nama_lengkap', 'ASC')->findAll();

        $data = [
            'title' => 'Anggota Kelas: ' . $rombel['nama_kelas'],
            'rombel' => $rombel,
            'anggota' => $anggota,
            'calon_siswa' => $calonSiswa
        ];
        return view('Modules\Akademik\Views\anggota_rombel\detail', $data);
    }

    public function addSiswa()
    {
        $rombelId = $this->request->getPost('rombel_id');
        $siswaIds = $this->request->getPost('siswa_ids'); // Array

        if (empty($siswaIds)) {
            return redirect()->back()->with('error', 'Tidak ada siswa yang dipilih.');
        }

        $inserted = 0;
        foreach ($siswaIds as $sId) {
            // Cek duplikasi
            $exists = $this->rombelSiswaModel->where('rombel_id', $rombelId)->where('siswa_id', $sId)->first();
            if (!$exists) {
                $this->rombelSiswaModel->insert([
                    'rombel_id' => $rombelId,
                    'siswa_id'  => $sId
                ]);
                $inserted++;
            }
        }

        return redirect()->back()->with('success', $inserted . ' Siswa berhasil ditambahkan ke kelas.');
    }

    public function removeSiswa($rsId)
    {
        $this->rombelSiswaModel->delete($rsId);
        return redirect()->back()->with('success', 'Siswa berhasil dikeluarkan dari kelas.');
    }
}
