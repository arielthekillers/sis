<?php

namespace Modules\Keuangan\Controllers;

use App\Controllers\BaseController;
use Modules\Keuangan\Models\JenisPembayaranModel;

class JenisPembayaran extends BaseController
{
    protected $jenisPembayaranModel;

    public function __construct()
    {
        $this->jenisPembayaranModel = new JenisPembayaranModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Master Jenis Tagihan',
            'jenis' => $this->jenisPembayaranModel->orderBy('nama_pembayaran', 'ASC')->findAll()
        ];
        return view('Modules\Keuangan\Views\jenis_pembayaran\index', $data);
    }

    public function save()
    {
        $id = $this->request->getPost('id');
        $nominal = str_replace('.', '', $this->request->getPost('nominal'));
        
        $data = [
            'nama_pembayaran' => $this->request->getPost('nama_pembayaran'),
            'nominal'         => $nominal,
            'tipe_siklus'     => $this->request->getPost('tipe_siklus'),
            'is_active'       => $this->request->getPost('is_active') ? 1 : 0,
        ];

        if ($id) {
            $this->jenisPembayaranModel->update($id, $data);
            $msg = 'Jenis pembayaran berhasil diupdate.';
        } else {
            $this->jenisPembayaranModel->insert($data);
            $msg = 'Jenis pembayaran berhasil ditambahkan.';
        }

        return redirect()->to('keuangan/jenis-pembayaran')->with('success', $msg);
    }

    public function delete($id)
    {
        // TODO: Cek apakah jenis pembayaran ini sudah dipakai di tagihan/setup siswa
        // Sementara soft-delete secara logic atau allow hard delete
        $this->jenisPembayaranModel->delete($id);
        return redirect()->to('keuangan/jenis-pembayaran')->with('success', 'Jenis pembayaran berhasil dihapus.');
    }
}
