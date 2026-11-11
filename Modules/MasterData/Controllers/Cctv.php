<?php

namespace Modules\MasterData\Controllers;

use App\Controllers\BaseController;
use Modules\MasterData\Models\CctvModel;

class Cctv extends BaseController
{
    protected CctvModel $cctvModel;

    public function __construct()
    {
        $this->cctvModel = new CctvModel();
    }

    /**
     * Tampilan Utama Manajemen CCTV Sekolah
     */
    public function index()
    {
        $keyword = trim((string)$this->request->getGet('q'));
        $statusFilter = trim((string)$this->request->getGet('status'));

        $builder = $this->cctvModel;

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('kode_kamera', $keyword)
                ->orLike('nama_kamera', $keyword)
                ->orLike('lokasi', $keyword)
                ->orLike('ip_public', $keyword)
                ->groupEnd();
        }

        if ($statusFilter !== '' && in_array($statusFilter, ['aktif', 'nonaktif', 'maintenance'], true)) {
            $builder->where('status', $statusFilter);
        }

        $cameras = $builder->orderBy('urutan', 'ASC')->orderBy('id', 'ASC')->findAll();

        // Hitung Metrik KPI
        $allCameras = $this->cctvModel->findAll();
        $totalCamera = count($allCameras);
        $totalAktif = 0;
        $totalFullday = 0;
        $totalNonaktif = 0;

        foreach ($allCameras as $c) {
            if ($c['status'] === 'aktif') $totalAktif++;
            if ($c['status'] !== 'aktif') $totalNonaktif++;
            if (($c['target_program'] ?? 'Fullday') === 'Fullday') $totalFullday++;
        }

        $data = [
            'title'         => 'Manajemen Kamera CCTV',
            'cameras'       => $cameras,
            'totalCamera'   => $totalCamera,
            'totalAktif'    => $totalAktif,
            'totalFullday'  => $totalFullday,
            'totalNonaktif' => $totalNonaktif,
            'keyword'       => $keyword,
            'statusFilter'  => $statusFilter,
        ];

        return view('Modules\MasterData\Views\cctv\index', $data);
    }

    /**
     * Tambah Kamera CCTV Baru
     */
    public function store()
    {
        $rules = [
            'kode_kamera' => 'required|min_length[2]|max_length[50]|is_unique[cctv_kamera.kode_kamera]',
            'nama_kamera' => 'required|min_length[3]|max_length[150]',
            'status'      => 'required|in_list[aktif,nonaktif,maintenance]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors()[0] ?? 'Data tidak valid.');
        }

        $data = [
            'kode_kamera'        => strtoupper(trim($this->request->getPost('kode_kamera'))),
            'nama_kamera'        => trim($this->request->getPost('nama_kamera')),
            'lokasi'             => trim($this->request->getPost('lokasi')),
            'ip_public'          => trim($this->request->getPost('ip_public')),
            'port'               => (int)($this->request->getPost('port') ?: 554),
            'stream_url'         => trim($this->request->getPost('stream_url')),
            'tipe_stream'        => $this->request->getPost('tipe_stream') ?: 'placeholder',
            'jadwal_operasional' => trim($this->request->getPost('jadwal_operasional')) ?: '07.30 - 16.30 WIB',
            'resolusi'           => trim($this->request->getPost('resolusi')) ?: '1080p FHD',
            'target_program'     => $this->request->getPost('target_program') ?: 'Fullday',
            'keterangan'         => trim($this->request->getPost('keterangan')),
            'status'             => $this->request->getPost('status') ?: 'aktif',
            'urutan'             => (int)($this->request->getPost('urutan') ?: 1),
        ];

        $this->cctvModel->insert($data);

        return redirect()->to(base_url('master-data/cctv'))->with('success', 'Kamera CCTV baru berhasil ditambahkan!');
    }

    /**
     * Update Data Kamera CCTV
     */
    public function update($id = null)
    {
        $camera = $this->cctvModel->find($id);
        if (!$camera) {
            return redirect()->to(base_url('master-data/cctv'))->with('error', 'Kamera CCTV tidak ditemukan.');
        }

        $kodeKamera = strtoupper(trim($this->request->getPost('kode_kamera')));
        $rules = [
            'kode_kamera' => "required|min_length[2]|max_length[50]|is_unique[cctv_kamera.kode_kamera,id,{$id}]",
            'nama_kamera' => 'required|min_length[3]|max_length[150]',
            'status'      => 'required|in_list[aktif,nonaktif,maintenance]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors()[0] ?? 'Data tidak valid.');
        }

        $data = [
            'kode_kamera'        => $kodeKamera,
            'nama_kamera'        => trim($this->request->getPost('nama_kamera')),
            'lokasi'             => trim($this->request->getPost('lokasi')),
            'ip_public'          => trim($this->request->getPost('ip_public')),
            'port'               => (int)($this->request->getPost('port') ?: 554),
            'stream_url'         => trim($this->request->getPost('stream_url')),
            'tipe_stream'        => $this->request->getPost('tipe_stream') ?: 'placeholder',
            'jadwal_operasional' => trim($this->request->getPost('jadwal_operasional')) ?: '07.30 - 16.30 WIB',
            'resolusi'           => trim($this->request->getPost('resolusi')) ?: '1080p FHD',
            'target_program'     => $this->request->getPost('target_program') ?: 'Fullday',
            'keterangan'         => trim($this->request->getPost('keterangan')),
            'status'             => $this->request->getPost('status') ?: 'aktif',
            'urutan'             => (int)($this->request->getPost('urutan') ?: 1),
        ];

        $this->cctvModel->update($id, $data);

        return redirect()->to(base_url('master-data/cctv'))->with('success', 'Data kamera CCTV berhasil diperbarui!');
    }

    /**
     * Hapus Data Kamera CCTV
     */
    public function delete($id = null)
    {
        $camera = $this->cctvModel->find($id);
        if (!$camera) {
            return redirect()->to(base_url('master-data/cctv'))->with('error', 'Kamera CCTV tidak ditemukan.');
        }

        $this->cctvModel->delete($id);

        return redirect()->to(base_url('master-data/cctv'))->with('success', 'Kamera CCTV berhasil dihapus!');
    }

    /**
     * Toggle Cepat Status Kamera (Aktif / Nonaktif)
     */
    public function toggleStatus($id = null)
    {
        $camera = $this->cctvModel->find($id);
        if (!$camera) {
            return redirect()->to(base_url('master-data/cctv'))->with('error', 'Kamera CCTV tidak ditemukan.');
        }

        $newStatus = ($camera['status'] === 'aktif') ? 'nonaktif' : 'aktif';
        $this->cctvModel->update($id, ['status' => $newStatus]);

        return redirect()->to(base_url('master-data/cctv'))->with('success', "Status kamera {$camera['kode_kamera']} diubah menjadi " . ucfirst($newStatus) . "!");
    }
}

