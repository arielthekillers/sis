<?php

namespace Modules\Keuangan\Controllers;

use App\Controllers\BaseController;
use Modules\Keuangan\Models\TagihanModel;
use Modules\Keuangan\Models\PembayaranModel;
use Modules\Keuangan\Models\PembayaranDetailModel;
use Modules\CivitasAkademika\Models\SiswaModel;
use Modules\Akademik\Models\RombelSiswaModel;
use Modules\Akademik\Models\RombelModel;
use Modules\Keuangan\Models\DepositHistoryModel;

class Kasir extends BaseController
{
    protected $tagihanModel;
    protected $pembayaranModel;
    protected $pembayaranDetailModel;
    protected $siswaModel;
    protected $rombelSiswaModel;
    protected $rombelModel;
    protected $depositHistoryModel;

    public function __construct()
    {
        $this->tagihanModel = new TagihanModel();
        $this->pembayaranModel = new PembayaranModel();
        $this->pembayaranDetailModel = new PembayaranDetailModel();
        $this->siswaModel = new SiswaModel();
        $this->rombelSiswaModel = new RombelSiswaModel();
        $this->rombelModel = new RombelModel();
        $this->depositHistoryModel = new DepositHistoryModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Kasir Pembayaran',
        ];
        return view('Modules\Keuangan\Views\kasir\index', $data);
    }

    public function searchAjax()
    {
        $keyword = $this->request->getGet('q');
        $siswa = [];

        if (!empty($keyword)) {
            $siswa = $this->siswaModel
                ->like('nama_lengkap', $keyword)
                ->orLike('nis', $keyword)
                ->limit(10)
                ->find();
                
            foreach($siswa as &$s) {
                $rs = $this->rombelSiswaModel->where('siswa_id', $s['id'])->orderBy('id', 'DESC')->first();
                if ($rs) {
                    $rombel = $this->rombelModel->find($rs['rombel_id']);
                    $s['kelas_sekarang'] = $rombel ? $rombel['nama_kelas'] : '-';
                } else {
                    $s['kelas_sekarang'] = '-';
                }
            }
        }

        return $this->response->setJSON($siswa);
    }

    public function bayar($siswaId)
    {
        $siswa = $this->siswaModel->find($siswaId);
        if (!$siswa) return redirect()->to('keuangan/kasir')->with('error', 'Siswa tidak ditemukan.');

        // Ambil semua tagihan yang belum lunas
        $tagihan = $this->tagihanModel
            ->select('keu_tagihan.*, keu_jenis_pembayaran.nama_pembayaran')
            ->join('keu_jenis_pembayaran', 'keu_jenis_pembayaran.id = keu_tagihan.jenis_pembayaran_id')
            ->where('keu_tagihan.siswa_id', $siswaId)
            ->whereIn('keu_tagihan.status', ['Belum Bayar', 'Mencicil'])
            ->orderBy('keu_tagihan.jatuh_tempo', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Pembayaran: ' . $siswa['nama_lengkap'],
            'siswa' => $siswa,
            'tagihan' => $tagihan
        ];
        return view('Modules\Keuangan\Views\kasir\bayar', $data);
    }

    public function process()
    {
        $siswaId = $this->request->getPost('siswa_id');
        $metode = $this->request->getPost('metode_pembayaran');
        $bayarData = $this->request->getPost('bayar'); // Format array: bayar[tagihan_id] = nominal
        
        $uangDiterima = (float)str_replace(['Rp', '.', ',', ' '], '', $this->request->getPost('uang_diterima') ?? 0);
        $gunakanDeposit = $this->request->getPost('gunakan_deposit') == '1';
        $simpanDeposit = $this->request->getPost('simpan_deposit') == '1';

        if (empty($bayarData)) {
            return redirect()->back()->with('error', 'Tidak ada tagihan yang dipilih untuk dibayar.');
        }

        $siswa = $this->siswaModel->find($siswaId);
        $saldoSiswa = $siswa['saldo_deposit'] ?? 0;

        $totalBayar = 0;
        $validBayar = [];

        foreach ($bayarData as $tagihanId => $nominal) {
            $nominal = (float)str_replace(['Rp', '.', ',', ' '], '', $nominal);
            if ($nominal > 0) {
                $tagihan = $this->tagihanModel->find($tagihanId);
                if ($tagihan) {
                    $sisaTunggakan = $tagihan['nominal_tagihan'] - $tagihan['nominal_dibayar'];
                    // Mencegah bayar lebih dari sisa
                    if ($nominal > $sisaTunggakan) {
                        $nominal = $sisaTunggakan;
                    }
                    $totalBayar += $nominal;
                    $validBayar[] = [
                        'tagihan' => $tagihan,
                        'nominal_alokasi' => $nominal
                    ];
                }
            }
        }

        if ($totalBayar <= 0) {
            return redirect()->back()->with('error', 'Total pembayaran tidak valid.');
        }

        // Hitung Deposit
        $depositTerpakai = 0;
        if ($gunakanDeposit && $saldoSiswa > 0) {
            $depositTerpakai = min($totalBayar, $saldoSiswa);
        }

        $uangHarusDibayar = $totalBayar - $depositTerpakai;
        
        if ($uangDiterima < $uangHarusDibayar && $uangHarusDibayar > 0) {
            if ($uangDiterima == 0 && $depositTerpakai == 0) {
                 return redirect()->back()->with('error', 'Silakan masukkan nominal uang diterima yang sesuai.');
            }
            return redirect()->back()->with('error', 'Uang yang diterima kurang dari sisa total yang harus dibayar.');
        }

        $kembalian = 0;
        if ($uangDiterima > $uangHarusDibayar) {
            $kembalian = $uangDiterima - $uangHarusDibayar;
        }

        $pesanKembalian = '';

        // 1. Buat Kuitansi / Struk (keu_pembayaran)
        $prefix = app_setting('receipt_prefix', 'KW');
        $noKwitansi = $prefix . '-' . date('Ymd-His') . '-' . rand(100, 999);

        // 2. Catat Transaksi Deposit Jika Ada
        if ($depositTerpakai > 0) {
            $this->depositHistoryModel->insert([
                'siswa_id' => $siswaId,
                'jenis_transaksi' => 'Keluar',
                'nominal' => $depositTerpakai,
                'keterangan' => 'Pemotongan otomatis untuk bayar tagihan kasir (Ref: ' . $noKwitansi . ')',
                'kasir_id' => session()->get('user_id') ?? 1
            ]);
            $saldoSiswa -= $depositTerpakai;
        }

        if ($kembalian > 0) {
            if ($simpanDeposit) {
                $this->depositHistoryModel->insert([
                    'siswa_id' => $siswaId,
                    'jenis_transaksi' => 'Masuk',
                    'nominal' => $kembalian,
                    'keterangan' => 'Simpan kembalian kasir ke deposit (Ref: ' . $noKwitansi . ')',
                    'kasir_id' => session()->get('user_id') ?? 1
                ]);
                $saldoSiswa += $kembalian;
                $pesanKembalian = ' Kembalian sebesar Rp ' . number_format($kembalian, 0, ',', '.') . ' otomatis disimpan ke saldo deposit.';
            } else {
                $pesanKembalian = ' Jangan lupa berikan kembalian tunai sebesar Rp ' . number_format($kembalian, 0, ',', '.');
            }
        }

        // Update Saldo Siswa
        $this->siswaModel->update($siswaId, ['saldo_deposit' => $saldoSiswa]);

        // 3. Simpan Kuitansi
        $this->pembayaranModel->insert([
            'no_kwitansi' => $noKwitansi,
            'siswa_id' => $siswaId,
            'tanggal_bayar' => date('Y-m-d H:i:s'),
            'total_bayar' => $totalBayar,
            'metode_pembayaran' => $metode,
            'kasir_id' => session()->get('user_id') ?? 1
        ]);
        
        $pembayaranId = $this->pembayaranModel->insertID();

        // 2. Alokasikan Kuitansi ke Detail Tagihan & Update Tagihannya
        foreach ($validBayar as $vb) {
            // Insert Detail
            $this->pembayaranDetailModel->insert([
                'pembayaran_id' => $pembayaranId,
                'tagihan_id' => $vb['tagihan']['id'],
                'nominal_alokasi' => $vb['nominal_alokasi']
            ]);

            // Update Tagihan
            $dibayarSekarang = $vb['tagihan']['nominal_dibayar'] + $vb['nominal_alokasi'];
            $statusBaru = ($dibayarSekarang >= $vb['tagihan']['nominal_tagihan']) ? 'Lunas' : 'Mencicil';
            
            $this->tagihanModel->update($vb['tagihan']['id'], [
                'nominal_dibayar' => $dibayarSekarang,
                'status' => $statusBaru
            ]);
        }

        return redirect()->to('keuangan/kasir/cetak/' . $pembayaranId)->with('success', 'Pembayaran berhasil diproses dengan Kuitansi ' . $noKwitansi . '.' . $pesanKembalian);
    }

    public function cetak($id)
    {
        $pembayaran = $this->pembayaranModel->find($id);
        if (!$pembayaran) return redirect()->to('keuangan/kasir')->with('error', 'Kuitansi tidak ditemukan.');

        $siswa = $this->siswaModel->find($pembayaran['siswa_id']);
        
        $rs = $this->rombelSiswaModel->where('siswa_id', $siswa['id'])->orderBy('id', 'DESC')->first();
        $kelas = '-';
        if ($rs) {
            $rombel = $this->rombelModel->find($rs['rombel_id']);
            $kelas = $rombel ? $rombel['nama_kelas'] : '-';
        }

        $details = $this->pembayaranDetailModel
            ->select('keu_pembayaran_detail.*, keu_tagihan.judul_tagihan')
            ->join('keu_tagihan', 'keu_tagihan.id = keu_pembayaran_detail.tagihan_id')
            ->where('pembayaran_id', $id)
            ->findAll();

        $data = [
            'title' => 'Cetak Kuitansi',
            'pembayaran' => $pembayaran,
            'siswa' => $siswa,
            'kelas' => $kelas,
            'details' => $details
        ];
        
        return view('Modules\Keuangan\Views\kasir\cetak', $data);
    }

    public function batal($id)
    {
        $pembayaran = $this->pembayaranModel->find($id);
        if (!$pembayaran) return redirect()->to('keuangan/kasir')->with('error', 'Kuitansi tidak ditemukan.');

        $siswaId = $pembayaran['siswa_id'];
        $noKwitansi = $pembayaran['no_kwitansi'];
        
        $siswa = $this->siswaModel->find($siswaId);
        $saldoSiswa = $siswa['saldo_deposit'] ?? 0;

        // 1. Rollback Tagihan
        $details = $this->pembayaranDetailModel->where('pembayaran_id', $id)->findAll();
        foreach ($details as $d) {
            $tagihan = $this->tagihanModel->find($d['tagihan_id']);
            if ($tagihan) {
                $dibayarSekarang = $tagihan['nominal_dibayar'] - $d['nominal_alokasi'];
                if ($dibayarSekarang < 0) $dibayarSekarang = 0; // fallback safety
                
                $statusBaru = 'Belum Bayar';
                if ($dibayarSekarang > 0 && $dibayarSekarang < $tagihan['nominal_tagihan']) {
                    $statusBaru = 'Mencicil';
                } elseif ($dibayarSekarang >= $tagihan['nominal_tagihan']) {
                    $statusBaru = 'Lunas';
                }
                
                $this->tagihanModel->update($tagihan['id'], [
                    'nominal_dibayar' => $dibayarSekarang,
                    'status' => $statusBaru
                ]);
            }
        }

        // 2. Rollback Deposit (Cari history berdasarkan referensi Kuitansi)
        $historyDeposit = $this->depositHistoryModel
            ->like('keterangan', 'Ref: ' . $noKwitansi)
            ->findAll();

        foreach ($historyDeposit as $hd) {
            if ($hd['jenis_transaksi'] == 'Keluar') {
                // Tadi pakai deposit (Keluar), sekarang kembalikan ke saldo
                $saldoSiswa += $hd['nominal'];
            } elseif ($hd['jenis_transaksi'] == 'Masuk') {
                // Tadi nyimpan kembalian (Masuk), sekarang tarik dari saldo
                $saldoSiswa -= $hd['nominal'];
            }
            // Hapus log history tersebut
            $this->depositHistoryModel->delete($hd['id']);
        }
        
        // Update Saldo Akhir Siswa
        $this->siswaModel->update($siswaId, ['saldo_deposit' => $saldoSiswa]);

        // 3. Hapus Kuitansi dan Detail
        $this->pembayaranDetailModel->where('pembayaran_id', $id)->delete();
        $this->pembayaranModel->delete($id);

        return redirect()->to('keuangan/kasir')->with('success', 'Kuitansi ' . $noKwitansi . ' berhasil dibatalkan. Data tagihan dan deposit telah di-rollback dengan aman.');
    }

    /**
     * Kirim Bukti Kuitansi via WhatsApp (Wapify)
     */
    public function kirimWa($id)
    {
        $pembayaran = $this->pembayaranModel->find($id);
        if (!$pembayaran) {
            return redirect()->back()->with('error', 'Kuitansi tidak ditemukan.');
        }

        $siswa = $this->siswaModel->find($pembayaran['siswa_id']);
        $noHp = $this->request->getPost('no_hp') ?: ($siswa['no_hp_ortu'] ?? null);

        if (empty($noHp)) {
            return redirect()->back()->with('error', 'Nomor WhatsApp belum diisi atau nomor orang tua belum terdaftar.');
        }

        $details = $this->pembayaranDetailModel
            ->select('keu_pembayaran_detail.*, keu_tagihan.judul_tagihan')
            ->join('keu_tagihan', 'keu_tagihan.id = keu_pembayaran_detail.tagihan_id')
            ->where('pembayaran_id', $id)
            ->findAll();

        $appName = app_setting('app_name', 'SIS');
        $schoolName = app_setting('school_name', 'Sekolah');

        $rincian = "";
        $no = 1;
        foreach ($details as $d) {
            $rincian .= "{$no}. *{$d['judul_tagihan']}*: " . rupiah($d['nominal_alokasi']) . "\n";
            $no++;
        }

        $pesan = "📢 *BUKTI PEMBAYARAN RESMI*\n"
               . "*{$schoolName}*\n"
               . "------------------------------------\n\n"
               . "Yth. Bpk/Ibu Wali Murid dari *{$siswa['nama_lengkap']}*,\n\n"
               . "Terima kasih, pembayaran administrasi sekolah telah berhasil kami terima dengan rincian berikut:\n\n"
               . "📄 *No. Kuitansi:* `{$pembayaran['no_kwitansi']}`\n"
               . "📅 *Tanggal Bayar:* " . date('d M Y H:i', strtotime($pembayaran['tanggal_bayar'])) . "\n"
               . "💳 *Metode:* {$pembayaran['metode_pembayaran']}\n\n"
               . "📋 *Rincian Tagihan:*\n"
               . $rincian . "\n"
               . "💰 *TOTAL DITERIMA: " . rupiah($pembayaran['total_bayar']) . "*\n\n"
               . "Petugas: " . app_setting('cashier_name', 'Kasir') . "\n\n"
               . "ℹ️ _" . app_setting('receipt_footer', 'Kuitansi ini adalah bukti pembayaran yang sah.') . "_\n"
               . "------------------------------------\n"
               . "_Pesan otomatis dikirim oleh {$appName}_";

        $wapify = new \App\Libraries\WapifyService();
        $result = $wapify->sendMessage($noHp, $pesan);

        if ($result['status'] === 'success') {
            return redirect()->back()->with('success', 'Bukti kuitansi berhasil dikirim via WhatsApp ke ' . $noHp);
        }

        return redirect()->back()->with('error', 'Gagal kirim WhatsApp: ' . ($result['message'] ?? 'Periksa konfigurasi Wapify Anda.'));
    }
}
