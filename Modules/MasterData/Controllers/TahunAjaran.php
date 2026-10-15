<?php

namespace Modules\MasterData\Controllers;

use App\Controllers\BaseController;
use Modules\MasterData\Models\TahunAjaranModel;

class TahunAjaran extends BaseController
{
    protected $tahunAjaranModel;

    public function __construct()
    {
        $this->tahunAjaranModel = new TahunAjaranModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Tahun Ajaran',
            'tahun_ajaran' => $this->tahunAjaranModel->orderBy('tahun', 'DESC')->findAll()
        ];
        return view('Modules\MasterData\Views\tahun_ajaran\index', $data);
    }

    public function save()
    {
        $id = $this->request->getPost('id');
        
        $rules = [
            'tahun'    => 'required',
            'semester' => 'required|in_list[Ganjil,Genap]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Data tidak valid. Silakan periksa kembali form Anda.');
        }

        $is_active = $this->request->getPost('is_active') ? 1 : 0;

        // Jika diset aktif, nonaktifkan yang lain dulu
        if ($is_active == 1) {
            $this->tahunAjaranModel->set(['is_active' => 0])->where('id >', 0)->update();
        }

        $saveData = [
            'tahun'     => $this->request->getPost('tahun'),
            'semester'  => $this->request->getPost('semester'),
            'is_active' => $is_active
        ];

        if ($id) {
            $this->tahunAjaranModel->update($id, $saveData);
            $message = 'Tahun Ajaran berhasil diperbarui.';
        } else {
            $this->tahunAjaranModel->insert($saveData);
            $message = 'Tahun Ajaran berhasil ditambahkan.';
        }

        return redirect()->to('master-data/tahun-ajaran')->with('success', $message);
    }

    public function delete($id)
    {
        $this->tahunAjaranModel->delete($id);
        return redirect()->to('master-data/tahun-ajaran')->with('success', 'Tahun Ajaran berhasil dihapus.');
    }
}
