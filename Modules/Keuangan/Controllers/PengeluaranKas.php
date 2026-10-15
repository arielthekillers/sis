<?php

namespace Modules\Keuangan\Controllers;

use App\Controllers\BaseController;
use Modules\Keuangan\Models\PengeluaranModel;
use Modules\Keuangan\Models\KategoriPengeluaranModel;
use App\Libraries\DocumentUploader;

class PengeluaranKas extends BaseController
{
    protected $pengeluaranModel;
    protected $kategoriModel;

    public function __construct()
    {
        $this->pengeluaranModel = new PengeluaranModel();
        $this->kategoriModel    = new KategoriPengeluaranModel();
    }

    public function index()
    {
        $bulan      = $this->request->getGet('bulan') ?? date('m');
        $tahun      = $this->request->getGet('tahun') ?? date('Y');
        $kategoriId = $this->request->getGet('kategori_id');

        $query = $this->pengeluaranModel
            ->select('keu_pengeluaran.*, keu_kategori_pengeluaran.nama_kategori')
            ->join('keu_kategori_pengeluaran', 'keu_kategori_pengeluaran.id = keu_pengeluaran.kategori_id', 'left')
            ->where('MONTH(keu_pengeluaran.tanggal)', $bulan)
            ->where('YEAR(keu_pengeluaran.tanggal)', $tahun);

        if (!empty($kategoriId)) {
            $query->where('keu_pengeluaran.kategori_id', $kategoriId);
        }

        $pengeluaran = $query->orderBy('keu_pengeluaran.tanggal', 'DESC')
                             ->orderBy('keu_pengeluaran.id', 'DESC')
                             ->paginate(15, 'pengeluaran');

        // Ringkasan Statistik Bulan Tersebut
        $statsBuilder = $this->pengeluaranModel
            ->selectSum('nominal', 'total_nominal')
            ->selectMax('nominal', 'max_nominal')
            ->selectCount('id', 'total_transaksi')
            ->where('MONTH(tanggal)', $bulan)
            ->where('YEAR(tanggal)', $tahun);

        if (!empty($kategoriId)) {
            $statsBuilder->where('kategori_id', $kategoriId);
        }

        $stats = $statsBuilder->first();

        // Breakdown per Kategori
        $breakdown = $this->pengeluaranModel
            ->select('keu_kategori_pengeluaran.nama_kategori, SUM(keu_pengeluaran.nominal) as total, COUNT(keu_pengeluaran.id) as count')
            ->join('keu_kategori_pengeluaran', 'keu_kategori_pengeluaran.id = keu_pengeluaran.kategori_id', 'left')
            ->where('MONTH(keu_pengeluaran.tanggal)', $bulan)
            ->where('YEAR(keu_pengeluaran.tanggal)', $tahun)
            ->groupBy('keu_pengeluaran.kategori_id')
            ->orderBy('total', 'DESC')
            ->findAll();

        $kategoriList = $this->kategoriModel->orderBy('nama_kategori', 'ASC')->findAll();

        $bulan_list = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        $data = [
            'title'          => 'Pengeluaran Kas Operasional',
            'pengeluaran'    => $pengeluaran,
            'kategoriList'   => $kategoriList,
            'bulan_list'     => $bulan_list,
            'bulan_aktif'    => $bulan,
            'tahun_aktif'    => $tahun,
            'kategori_aktif' => $kategoriId,
            'totalNominal'   => $stats['total_nominal'] ?? 0,
            'maxNominal'     => $stats['max_nominal'] ?? 0,
            'totalTransaksi' => $stats['total_transaksi'] ?? 0,
            'breakdown'      => $breakdown,
            'pager'          => $this->pengeluaranModel->pager,
        ];

        return view('Modules\Keuangan\Views\pengeluaran\index', $data);
    }

    public function save()
    {
        $id = $this->request->getPost('id');

        $rules = [
            'tanggal'     => 'required|valid_date',
            'kategori_id' => 'required|is_natural_no_zero',
            'nominal'     => 'required',
            'keperluan'   => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Data tidak valid. Mohon periksa kembali kolom yang bertanda bintang.');
        }

        $rawNominal = $this->request->getPost('nominal');
        $nominal = (float) str_replace(['Rp', '.', ' ', ','], '', $rawNominal);

        $tanggal = $this->request->getPost('tanggal');

        $saveData = [
            'kategori_id'       => $this->request->getPost('kategori_id'),
            'tanggal'           => $tanggal,
            'nominal'           => $nominal,
            'penerima'          => $this->request->getPost('penerima') ?: null,
            'keperluan'         => $this->request->getPost('keperluan'),
            'metode_pembayaran' => $this->request->getPost('metode_pembayaran') ?: 'Tunai',
            'user_id'           => auth()->id() ?? null,
        ];

        // Handle upload bukti transaksi
        $fileBukti = $this->request->getFile('bukti_transaksi');
        if ($fileBukti && $fileBukti->isValid() && !$fileBukti->hasMoved()) {
            $docUploader = new DocumentUploader('uploads/pengeluaran/');
            $docUploader->setAllowedMimeTypes([
                'image/jpeg',
                'image/png',
                'image/webp',
                'application/pdf'
            ]);

            $uploadResult = $docUploader->process($fileBukti);
            if ($uploadResult['status'] === 'success') {
                $saveData['bukti_transaksi'] = $uploadResult['filename'];

                // Hapus file lama jika ada pergantian bukti
                if ($id) {
                    $old = $this->pengeluaranModel->find($id);
                    if ($old && !empty($old['bukti_transaksi'])) {
                        $oldPath = FCPATH . 'uploads/pengeluaran/' . $old['bukti_transaksi'];
                        if (is_file($oldPath)) {
                            @unlink($oldPath);
                        }
                    }
                }
            } else {
                return redirect()->back()->withInput()->with('error', 'Gagal mengunggah bukti: ' . $uploadResult['message']);
            }
        }

        if ($id) {
            $this->pengeluaranModel->update($id, $saveData);
            $message = 'Transaksi pengeluaran berhasil diperbarui.';
        } else {
            $saveData['kode_transaksi'] = $this->pengeluaranModel->generateKodeTransaksi($tanggal);
            $this->pengeluaranModel->insert($saveData);
            $message = 'Pengeluaran kas berhasil dicatat dengan kode: ' . $saveData['kode_transaksi'];
        }

        return redirect()->to('keuangan/pengeluaran?bulan=' . date('m', strtotime($tanggal)) . '&tahun=' . date('Y', strtotime($tanggal)))
                         ->with('success', $message);
    }

    public function delete($id)
    {
        $pengeluaran = $this->pengeluaranModel->find($id);
        if (!$pengeluaran) {
            return redirect()->back()->with('error', 'Data pengeluaran tidak ditemukan.');
        }

        // Hapus file bukti jika ada
        if (!empty($pengeluaran['bukti_transaksi'])) {
            $filePath = FCPATH . 'uploads/pengeluaran/' . $pengeluaran['bukti_transaksi'];
            if (is_file($filePath)) {
                @unlink($filePath);
            }
        }

        $this->pengeluaranModel->delete($id);

        return redirect()->back()->with('success', 'Transaksi pengeluaran ' . $pengeluaran['kode_transaksi'] . ' berhasil dihapus.');
    }

    public function saveKategori()
    {
        $id = $this->request->getPost('id');
        $nama = trim($this->request->getPost('nama_kategori'));

        if (empty($nama)) {
            return redirect()->back()->with('error', 'Nama kategori pengeluaran tidak boleh kosong.');
        }

        $data = [
            'nama_kategori' => $nama,
            'deskripsi'     => $this->request->getPost('deskripsi') ?: null,
        ];

        if ($id) {
            $this->kategoriModel->update($id, $data);
            $pesan = 'Kategori pengeluaran berhasil diperbarui.';
        } else {
            $this->kategoriModel->insert($data);
            $pesan = 'Kategori pengeluaran baru berhasil ditambahkan.';
        }

        return redirect()->back()->with('success', $pesan);
    }

    public function deleteKategori($id)
    {
        // Cek apakah ada pengeluaran yang menggunakan kategori ini
        $usedCount = $this->pengeluaranModel->where('kategori_id', $id)->countAllResults();
        if ($usedCount > 0) {
            return redirect()->back()->with('error', 'Kategori ini tidak dapat dihapus karena masih digunakan oleh ' . $usedCount . ' data transaksi pengeluaran.');
        }

        $this->kategoriModel->delete($id);
        return redirect()->back()->with('success', 'Kategori pengeluaran berhasil dihapus.');
    }
}
