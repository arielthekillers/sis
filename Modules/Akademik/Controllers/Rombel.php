<?php

namespace Modules\Akademik\Controllers;

use App\Controllers\BaseController;
use Modules\Akademik\Models\RombelModel;
use Modules\MasterData\Models\TahunAjaranModel;
use Modules\CivitasAkademika\Models\GuruStafModel;

class Rombel extends BaseController
{
    protected $rombelModel;
    protected $tahunAjaranModel;
    protected $guruStafModel;

    public function __construct()
    {
        $this->rombelModel = new RombelModel();
        $this->tahunAjaranModel = new TahunAjaranModel();
        $this->guruStafModel = new GuruStafModel();
    }

    private function getActiveTahun()
    {
        $activeTa = $this->tahunAjaranModel->where('is_active', 1)->first();
        // Return active tahun, or default to current year if not set
        return $activeTa ? $activeTa['tahun'] : date('Y') . '/' . (date('Y')+1);
    }

    public function index()
    {
        $tahun = $this->getActiveTahun();
        $guruList = $this->guruStafModel->where('is_active', 1)->orderBy('nama_lengkap', 'ASC')->findAll();
        
        // Buat array guru dengan key ID untuk mapping nama di view
        $guruMap = [];
        foreach ($guruList as $g) {
            $guruMap[$g['id']] = $g['sapaan'] . ' ' . $g['nama_lengkap'];
        }

        $data = [
            'title' => 'Rombongan Belajar',
            'tahun_aktif' => $tahun,
            'rombel' => $this->rombelModel->where('tahun_ajaran', $tahun)->orderBy('tingkat', 'ASC')->orderBy('nama_kelas', 'ASC')->findAll(),
            'guru' => $guruList,
            'guruMap' => $guruMap
        ];
        return view('Modules\Akademik\Views\rombel\index', $data);
    }

    public function save()
    {
        $id = $this->request->getPost('id');
        $tahun = $this->getActiveTahun();
        
        $rules = [
            'tingkat'    => 'required',
            'nama_kelas' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Data tidak valid. Silakan periksa kembali form Anda.');
        }

        // Tangani array wali kelas (bisa lebih dari satu)
        $waliKelasArr = $this->request->getPost('wali_kelas'); // Array
        $waliKelasJson = null;
        if (!empty($waliKelasArr) && is_array($waliKelasArr)) {
            $waliKelasJson = json_encode($waliKelasArr);
        }

        $saveData = [
            'tingkat'      => $this->request->getPost('tingkat'),
            'nama_kelas'   => $this->request->getPost('nama_kelas'),
            'wali_kelas'   => $waliKelasJson,
            'keterangan'   => $this->request->getPost('keterangan'),
            'tahun_ajaran' => $tahun // Save specific to active year
        ];

        if ($id) {
            $this->rombelModel->update($id, $saveData);
            $message = 'Data Rombel berhasil diperbarui.';
        } else {
            $this->rombelModel->insert($saveData);
            $message = 'Data Rombel berhasil ditambahkan.';
        }

        return redirect()->to('akademik/rombel')->with('success', $message);
    }

    public function delete($id)
    {
        $this->rombelModel->delete($id);
        return redirect()->to('akademik/rombel')->with('success', 'Data Rombel berhasil dihapus.');
    }
}
