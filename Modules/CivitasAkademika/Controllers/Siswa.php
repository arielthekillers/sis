<?php

namespace Modules\CivitasAkademika\Controllers;

use App\Controllers\BaseController;
use Modules\CivitasAkademika\Models\SiswaModel;

class Siswa extends BaseController
{
    protected $siswaModel;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Data Siswa',
            'siswa' => $this->siswaModel->orderBy('nama_lengkap', 'ASC')->paginate(10, 'siswa'),
            'pager' => $this->siswaModel->pager
        ];
        return view('Modules\CivitasAkademika\Views\siswa\index', $data);
    }

    public function save()
    {
        $id = $this->request->getPost('id');
        
        $rules = [
            'nama_lengkap'  => 'required',
            'jenis_kelamin' => 'required|in_list[L,P]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Data tidak valid. Silakan periksa kembali form Anda.');
        }

        $saveData = [
            'nis'            => $this->request->getPost('nis') ?: null,
            'nisn'           => $this->request->getPost('nisn') ?: null,
            'nama_lengkap'   => $this->request->getPost('nama_lengkap'),
            'nama_panggilan' => $this->request->getPost('nama_panggilan') ?: null,
            'jenis_kelamin'  => $this->request->getPost('jenis_kelamin'),
            'tempat_lahir'   => $this->request->getPost('tempat_lahir') ?: null,
            'tanggal_lahir'  => $this->request->getPost('tanggal_lahir') ?: null,
            'agama'          => $this->request->getPost('agama') ?: null,
            'alamat'         => $this->request->getPost('alamat') ?: null,
            'nama_ayah'      => $this->request->getPost('nama_ayah') ?: null,
            'nama_ibu'       => $this->request->getPost('nama_ibu') ?: null,
            'no_hp_ortu'     => $this->request->getPost('no_hp_ortu') ?: null,
            'status_siswa'   => $this->request->getPost('status_siswa') ?: 'Aktif',
            'is_active'      => $this->request->getPost('is_active') !== null ? 1 : 0
        ];

        // Handle Image Upload
        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $uploader = new \App\Libraries\ImageUploader('uploads/profiles/');
            $result = $uploader->process($foto, true, ['thumb' => 150, 'md' => 500]);
            
            if ($result['status'] === 'success') {
                $saveData['foto'] = $result['files']['original'];
            } else {
                return redirect()->back()->withInput()->with('error', 'Gagal mengunggah foto: ' . $result['message']);
            }
        }

        if ($id) {
            $this->siswaModel->update($id, $saveData);
            $message = 'Data Siswa berhasil diperbarui.';
        } else {
            $this->siswaModel->insert($saveData);
            $message = 'Data Siswa berhasil ditambahkan.';
        }

        return redirect()->to('civitas-akademika/siswa')->with('success', $message);
    }

    public function delete($id)
    {
        $this->siswaModel->delete($id);
        return redirect()->to('civitas-akademika/siswa')->with('success', 'Data Siswa berhasil dihapus.');
    }

    public function profil($id)
    {
        $siswa = $this->siswaModel->find($id);
        if (!$siswa) {
            return redirect()->to('civitas-akademika/siswa')->with('error', 'Siswa tidak ditemukan.');
        }

        $historyKelas = [];
        try {
            $db = \Config\Database::connect();
            $historyKelas = $db->table('rombel_siswa')
                ->select('rombel_siswa.*, rombel.nama_rombel, tahun_ajaran.tahun, tahun_ajaran.semester')
                ->join('rombel', 'rombel.id = rombel_siswa.rombel_id', 'left')
                ->join('tahun_ajaran', 'tahun_ajaran.id = rombel.tahun_ajaran_id', 'left')
                ->where('rombel_siswa.siswa_id', $id)
                ->orderBy('tahun_ajaran.id', 'DESC')
                ->get()->getResultArray();
        } catch (\Exception $e) {}

        $historyPembayaran = [];
        try {
            $db = \Config\Database::connect();
            $historyPembayaran = $db->table('pembayaran')
                ->where('siswa_id', $id)
                ->orderBy('tanggal_bayar', 'DESC')
                ->get()->getResultArray();
        } catch (\Exception $e) {}

        $data = [
            'title' => 'Profil Siswa',
            'siswa' => $siswa,
            'historyKelas' => $historyKelas,
            'historyPembayaran' => $historyPembayaran
        ];

        return view('Modules\CivitasAkademika\Views\siswa\profil', $data);
    }
}
