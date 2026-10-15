<?php

namespace Modules\MasterData\Controllers;

use App\Controllers\BaseController;
use Modules\MasterData\Models\MataPelajaranModel;

class MataPelajaran extends BaseController
{
    protected $mataPelajaranModel;

    public function __construct()
    {
        $this->mataPelajaranModel = new MataPelajaranModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Mata Pelajaran',
            'mata_pelajaran' => $this->mataPelajaranModel->orderBy('kelompok', 'ASC')->orderBy('nama_mapel', 'ASC')->findAll()
        ];
        return view('Modules\MasterData\Views\mata_pelajaran\index', $data);
    }

    public function save()
    {
        $id = $this->request->getPost('id');
        
        $rules = [
            'kode_mapel' => 'required|is_unique[mata_pelajaran.kode_mapel,id,'.$id.']',
            'nama_mapel' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Data tidak valid atau Kode Mapel sudah digunakan.');
        }

        $saveData = [
            'kode_mapel' => $this->request->getPost('kode_mapel'),
            'nama_mapel' => $this->request->getPost('nama_mapel'),
            'kelompok'   => $this->request->getPost('kelompok')
        ];

        if ($id) {
            $this->mataPelajaranModel->update($id, $saveData);
            $message = 'Mata Pelajaran berhasil diperbarui.';
        } else {
            $this->mataPelajaranModel->insert($saveData);
            $message = 'Mata Pelajaran berhasil ditambahkan.';
        }

        return redirect()->to('master-data/mata-pelajaran')->with('success', $message);
    }

    public function delete($id)
    {
        $this->mataPelajaranModel->delete($id);
        return redirect()->to('master-data/mata-pelajaran')->with('success', 'Mata Pelajaran berhasil dihapus.');
    }
}
