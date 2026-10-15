<?php

namespace Modules\CivitasAkademika\Controllers;

use App\Controllers\BaseController;
use Modules\CivitasAkademika\Models\GuruStafModel;

class GuruStaf extends BaseController
{
    protected $guruStafModel;

    public function __construct()
    {
        $this->guruStafModel = new GuruStafModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Data Guru & Staf',
            'guru_staf' => $this->guruStafModel->orderBy('nama_lengkap', 'ASC')->paginate(10, 'guru'),
            'pager' => $this->guruStafModel->pager
        ];
        return view('Modules\CivitasAkademika\Views\guru_staf\index', $data);
    }

    public function save()
    {
        $id = $this->request->getPost('id');
        
        $rules = [
            'nama_lengkap'   => 'required',
            'nama_panggilan' => 'required',
            'sapaan'         => 'required',
            'jenis_kelamin'  => 'required|in_list[L,P]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Data tidak valid. Periksa isian form Anda.');
        }

        $saveData = [
            'nip'            => $this->request->getPost('nip') ?: null,
            'nama_lengkap'   => $this->request->getPost('nama_lengkap'),
            'nama_panggilan' => $this->request->getPost('nama_panggilan'),
            'sapaan'         => $this->request->getPost('sapaan'),
            'jenis_kelamin'  => $this->request->getPost('jenis_kelamin'),
            'no_hp'          => $this->request->getPost('no_hp') ?: null,
            'status_pegawai' => $this->request->getPost('status_pegawai') ?: null,
            'tahun_masuk'    => $this->request->getPost('tahun_masuk') ?: null,
            'is_active'      => $this->request->getPost('is_active') !== null ? 1 : 0
        ];

        // Handle Image Upload
        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $uploader = new \App\Libraries\ImageUploader('uploads/profiles/');
            // Crop as square, resize to thumb (150x150) and md (500x500)
            $result = $uploader->process($foto, true, ['thumb' => 150, 'md' => 500]);
            
            if ($result['status'] === 'success') {
                $saveData['foto'] = $result['files']['original'];
                
                // Jika update dan ada foto lama, bisa dihapus di sini
                // if ($id) {
                //     $oldData = $this->guruStafModel->find($id);
                //     // logika hapus file lama...
                // }
            } else {
                return redirect()->back()->withInput()->with('error', 'Gagal mengunggah foto: ' . $result['message']);
            }
        }

        if ($id) {
            $this->guruStafModel->update($id, $saveData);
            $message = 'Data Guru/Staf berhasil diperbarui.';
        } else {
            // New record is active by default unless checkbox is unchecked
            $this->guruStafModel->insert($saveData);
            $message = 'Data Guru/Staf berhasil ditambahkan.';
        }

        return redirect()->to('civitas-akademika/guru-staf')->with('success', $message);
    }

    public function delete($id)
    {
        $this->guruStafModel->delete($id);
        return redirect()->to('civitas-akademika/guru-staf')->with('success', 'Data Guru/Staf berhasil dihapus.');
    }
}
