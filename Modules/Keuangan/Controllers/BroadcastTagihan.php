<?php

namespace Modules\Keuangan\Controllers;

use App\Controllers\BaseController;
use Modules\Keuangan\Models\TagihanModel;
use Modules\CivitasAkademika\Models\SiswaModel;
use App\Libraries\WapifyService;

class BroadcastTagihan extends BaseController
{
    protected TagihanModel $tagihanModel;
    protected SiswaModel $siswaModel;
    protected $db;

    public function __construct()
    {
        $this->tagihanModel = new TagihanModel();
        $this->siswaModel   = new SiswaModel();
        $this->db           = \Config\Database::connect();
    }

    /**
     * Halaman Dashboard Broadcast Pengingat Tagihan
     */
    public function index()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $bulanNames = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        $namaBulan = $bulanNames[$bulan] ?? '';
        $periodeString = "%" . $namaBulan . " " . $tahun;

        $tanggalBatas = date("Y-m-t", strtotime("$tahun-$bulan-01"));

        // Ambil seluruh tagihan yang belum lunas (Belum Bayar / Mencicil) hingga periode ini (akumulasi tunggakan)
        $builder = $this->db->table('keu_tagihan')
            ->select('
                keu_tagihan.id as tagihan_id,
                keu_tagihan.judul_tagihan,
                keu_tagihan.nominal_tagihan,
                keu_tagihan.nominal_dibayar,
                (keu_tagihan.nominal_tagihan - keu_tagihan.nominal_dibayar) as sisa_tagihan,
                keu_tagihan.status as status_tagihan,
                keu_tagihan.jatuh_tempo,
                siswa.id as siswa_id,
                siswa.nama_lengkap,
                siswa.nis,
                siswa.no_hp_ortu,
                kelas.nama_kelas
            ')
            ->join('siswa', 'siswa.id = keu_tagihan.siswa_id')
            ->join('rombel_siswa', 'rombel_siswa.siswa_id = siswa.id', 'left')
            ->join('kelas', 'kelas.id = rombel_siswa.rombel_id', 'left')
            ->whereIn('keu_tagihan.status', ['Belum Bayar', 'Mencicil'])
            ->groupStart()
                ->where('keu_tagihan.jatuh_tempo <=', $tanggalBatas)
                ->orWhere('keu_tagihan.jatuh_tempo IS NULL')
            ->groupEnd()
            ->orderBy('kelas.nama_kelas', 'ASC')
            ->orderBy('siswa.nama_lengkap', 'ASC')
            ->orderBy('keu_tagihan.jatuh_tempo', 'ASC')
            ->orderBy('keu_tagihan.id', 'ASC');

        $tagihanRows = $builder->get()->getResultArray();

        // Kelompokkan tagihan per siswa agar 1 orang tua hanya menerima 1 pesan WhatsApp yang merangkum semua tagihannya
        $siswaTunggakan = [];
        $totalNominalTertunggak = 0;
        $totalNomorValid = 0;
        $totalNomorKosong = 0;

        foreach ($tagihanRows as $row) {
            $sId = $row['siswa_id'];
            if (!isset($siswaTunggakan[$sId])) {
                $hasPhone = !empty(trim($row['no_hp_ortu'] ?? ''));
                if ($hasPhone) {
                    $totalNomorValid++;
                } else {
                    $totalNomorKosong++;
                }

                $siswaTunggakan[$sId] = [
                    'siswa_id'       => $sId,
                    'nama_lengkap'   => $row['nama_lengkap'],
                    'nis'            => $row['nis'],
                    'no_hp_ortu'     => $row['no_hp_ortu'],
                    'nama_kelas'     => $row['nama_kelas'] ?: '-',
                    'total_tunggakan'=> 0,
                    'items'          => [],
                ];
            }

            $siswaTunggakan[$sId]['items'][] = [
                'tagihan_id'     => $row['tagihan_id'],
                'judul_tagihan'  => $row['judul_tagihan'],
                'nominal_tagihan'=> (float) $row['nominal_tagihan'],
                'nominal_dibayar'=> (float) $row['nominal_dibayar'],
                'sisa'           => (float) $row['sisa_tagihan'],
                'status'         => $row['status_tagihan'],
            ];

            $siswaTunggakan[$sId]['total_tunggakan'] += (float) $row['sisa_tagihan'];
            $totalNominalTertunggak += (float) $row['sisa_tagihan'];
        }

        $data = [
            'title'                  => 'Broadcast Pengingat Tagihan WhatsApp',
            'bulanList'              => $bulanNames,
            'bulanAktif'             => $bulan,
            'tahunAktif'             => $tahun,
            'namaBulan'              => $namaBulan,
            'siswaList'              => array_values($siswaTunggakan),
            'totalSiswa'             => count($siswaTunggakan),
            'totalNominalTertunggak' => $totalNominalTertunggak,
            'totalNomorValid'        => $totalNomorValid,
            'totalNomorKosong'       => $totalNomorKosong,
            'waIsActive'             => (bool) app_setting('wapify_is_active', 0),
            'waDelayMin'             => (int) app_setting('wa_delay_min', 8),
            'waDelayMax'             => (int) app_setting('wa_delay_max', 15),
            'reminderDay'            => app_setting('wa_reminder_day', 10),
        ];

        return view('Modules\Keuangan\Views\broadcast_tagihan\index', $data);
    }

    /**
     * AJAX Cek Kontak WhatsApp via Wapify
     */
    public function checkContactAjax()
    {
        $phone = $this->request->getPost('phone');
        if (empty($phone)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Nomor telepon tidak boleh kosong.'
            ]);
        }

        $wapify = new WapifyService();
        $result = $wapify->checkContact($phone);

        return $this->response->setJSON($result);
    }

    /**
     * AJAX Kirim Pengingat ke 1 Siswa (dipanggil berurutan oleh Runner Frontend)
     */
    public function sendReminderAjax()
    {
        $siswaId = $this->request->getPost('siswa_id');
        $bulan   = $this->request->getPost('bulan') ?? date('m');
        $tahun   = $this->request->getPost('tahun') ?? date('Y');
        $phone   = $this->request->getPost('phone');

        if (empty($siswaId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID Siswa tidak valid.']);
        }

        $siswa = $this->siswaModel->find($siswaId);
        if (!$siswa) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data siswa tidak ditemukan.']);
        }

        $targetPhone = !empty($phone) ? $phone : ($siswa['no_hp_ortu'] ?? '');
        if (empty($targetPhone)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Nomor WhatsApp orang tua belum terisi.']);
        }

        // Cek nama kelas
        $rs = $this->db->table('rombel_siswa')
            ->select('kelas.nama_kelas')
            ->join('kelas', 'kelas.id = rombel_siswa.rombel_id')
            ->where('rombel_siswa.siswa_id', $siswaId)
            ->get()->getRowArray();
        $namaKelas = $rs['nama_kelas'] ?? '-';

        // Ambil tagihan yang belum lunas
        $bulanNames = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        $tanggalBatas = date("Y-m-t", strtotime("$tahun-$bulan-01"));

        $tagihanList = $this->tagihanModel
            ->where('siswa_id', $siswaId)
            ->whereIn('status', ['Belum Bayar', 'Mencicil'])
            ->groupStart()
                ->where('jatuh_tempo <=', $tanggalBatas)
                ->orWhere('jatuh_tempo IS NULL')
            ->groupEnd()
            ->orderBy('jatuh_tempo', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        if (empty($tagihanList)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak ada tagihan tertunggak untuk siswa ini pada periode tersebut.']);
        }

        $totalTunggakan = 0;
        $rincianText = "";
        $no = 1;
        foreach ($tagihanList as $t) {
            $sisa = (float) $t['nominal_tagihan'] - (float) $t['nominal_dibayar'];
            $totalTunggakan += $sisa;
            $rincianText .= "{$no}. *{$t['judul_tagihan']}*: " . rupiah($sisa) . "\n";
            $no++;
        }

        // Susun teks pesan berbasis template
        $template = app_setting('wa_reminder_template');
        if (empty($template)) {
            $template = "{salam} Bpk/Ibu Wali Murid dari *{nama_siswa}* ({kelas}).\n\n"
                      . "Kami informasikan rincian tagihan administrasi sekolah di *{nama_sekolah}* untuk periode *{periode}*:\n\n"
                      . "{rincian_tagihan}\n"
                      . "💰 *TOTAL TAGIHAN: {total_tagihan}*\n\n"
                      . "💳 Pembayaran dapat ditransfer melalui:\n"
                      . "{info_bank}\n"
                      . "atau dapat dibayarkan langsung secara tunai di loket kasir/tata usaha sekolah.\n\n"
                      . "📲 Konfirmasi bukti transfer pembayaran melalui WhatsApp ke:\n"
                      . "*{nomor_konfirmasi}*\n\n"
                      . "ℹ️ _Apabila Bapak/Ibu telah melakukan pembayaran sebelumnya, mohon abaikan pesan ini._\n\n"
                      . "_Ref: {kode_unik}_";
        }

        // Variasi salam acak (Anti-Banned: variasi teks agar hash pesan berbeda)
        $salamList = ['Assalamu\'alaikum,', 'Selamat Pagi,', 'Salam Sejahtera,', 'Halo Bpk/Ibu,'];
        $randomSalam = $salamList[array_rand($salamList)];

        // Info Rekening Bank
        $infoBank = "-";
        if (app_setting('bank_account_number')) {
            $infoBank = app_setting('bank_name') . ' ' . app_setting('bank_account_number') . ' a.n. ' . app_setting('bank_account_holder');
        }

        $nomorKonfirmasi = app_setting('payment_confirmation_phone', app_setting('school_whatsapp', '-'));
        $kodeUnik = 'TAG-' . date('ym') . '-' . substr(md5($siswaId . time()), 0, 6);

        $replacements = [
            '{salam}'            => $randomSalam,
            '{nama_siswa}'       => $siswa['nama_lengkap'],
            '{kelas}'            => $namaKelas,
            '{nama_sekolah}'     => app_setting('school_name', 'Sekolah'),
            '{periode}'          => $namaBulan . ' ' . $tahun,
            '{rincian_tagihan}'  => trim($rincianText),
            '{total_tagihan}'    => rupiah($totalTunggakan),
            '{info_bank}'        => $infoBank,
            '{nomor_konfirmasi}' => $nomorKonfirmasi,
            '{kode_unik}'        => $kodeUnik,
        ];

        $pesanFinal = str_replace(array_keys($replacements), array_values($replacements), $template);

        // Eksekusi kirim via Wapify
        $wapify = new WapifyService();
        $res = $wapify->sendMessage($targetPhone, $pesanFinal);

        if ($res['status'] === 'success') {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Pengingat berhasil dikirim ke ' . $targetPhone,
                'data'    => $res['data'] ?? []
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => $res['message'] ?? 'Gagal mengirim pesan via Wapify.'
        ]);
    }

    /**
     * AJAX Update Nomor WhatsApp Orang Tua Siswa secara Cepat
     */
    public function updatePhoneAjax()
    {
        $siswaId = $this->request->getPost('siswa_id');
        $phone   = trim($this->request->getPost('phone') ?? '');

        if (empty($siswaId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID Siswa tidak valid.']);
        }

        if (empty($phone)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Nomor WhatsApp tidak boleh kosong.']);
        }

        $siswa = $this->siswaModel->find($siswaId);
        if (!$siswa) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data siswa tidak ditemukan.']);
        }

        $this->siswaModel->update($siswaId, ['no_hp_ortu' => $phone]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Nomor WhatsApp wali murid berhasil diperbarui!',
            'phone'   => $phone
        ]);
    }

    /**
     * AJAX Ambil Teks Pesan Terformat & Link WhatsApp Web Manual
     */
    public function getMessageAjax()
    {
        $siswaId = $this->request->getGet('siswa_id');
        $bulan   = $this->request->getGet('bulan') ?? date('m');
        $tahun   = $this->request->getGet('tahun') ?? date('Y');

        if (empty($siswaId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID Siswa tidak valid.']);
        }

        $siswa = $this->siswaModel->find($siswaId);
        if (!$siswa) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data siswa tidak ditemukan.']);
        }

        $phone = trim($siswa['no_hp_ortu'] ?? '');
        if (empty($phone)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Nomor WhatsApp orang tua belum terdaftar.', 'no_phone' => true]);
        }

        // Cek nama kelas
        $rs = $this->db->table('rombel_siswa')
            ->select('kelas.nama_kelas')
            ->join('kelas', 'kelas.id = rombel_siswa.rombel_id')
            ->where('rombel_siswa.siswa_id', $siswaId)
            ->get()->getRowArray();
        $namaKelas = $rs['nama_kelas'] ?? '-';

        $bulanNames = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        $namaBulan = $bulanNames[$bulan] ?? '';
        $tanggalBatas = date("Y-m-t", strtotime("$tahun-$bulan-01"));

        $tagihanList = $this->tagihanModel
            ->where('siswa_id', $siswaId)
            ->whereIn('status', ['Belum Bayar', 'Mencicil'])
            ->groupStart()
                ->where('jatuh_tempo <=', $tanggalBatas)
                ->orWhere('jatuh_tempo IS NULL')
            ->groupEnd()
            ->orderBy('jatuh_tempo', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        if (empty($tagihanList)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak ada tagihan tertunggak untuk siswa ini.']);
        }

        $totalTunggakan = 0;
        $rincianText = "";
        $no = 1;
        foreach ($tagihanList as $t) {
            $sisa = (float) $t['nominal_tagihan'] - (float) $t['nominal_dibayar'];
            $totalTunggakan += $sisa;
            $rincianText .= "{$no}. *{$t['judul_tagihan']}*: " . rupiah($sisa) . "\n";
            $no++;
        }

        $template = app_setting('wa_reminder_template');
        if (empty($template)) {
            $template = "{salam} Bpk/Ibu Wali Murid dari *{nama_siswa}* ({kelas}).\n\n"
                      . "Kami informasikan rincian tagihan administrasi sekolah di *{nama_sekolah}* untuk periode *{periode}*:\n\n"
                      . "{rincian_tagihan}\n"
                      . "💰 *TOTAL TAGIHAN: {total_tagihan}*\n\n"
                      . "💳 Pembayaran dapat ditransfer melalui:\n"
                      . "{info_bank}\n"
                      . "atau dapat dibayarkan langsung secara tunai di loket kasir/tata usaha sekolah.\n\n"
                      . "📲 Konfirmasi bukti transfer pembayaran melalui WhatsApp ke:\n"
                      . "*{nomor_konfirmasi}*\n\n"
                      . "ℹ️ _Apabila Bapak/Ibu telah melakukan pembayaran sebelumnya, mohon abaikan pesan ini._\n\n"
                      . "_Ref: {kode_unik}_";
        }

        $salamList = ['Assalamu\'alaikum,', 'Selamat Pagi,', 'Salam Sejahtera,', 'Halo Bpk/Ibu,'];
        $randomSalam = $salamList[array_rand($salamList)];

        $infoBank = "-";
        if (app_setting('bank_account_number')) {
            $infoBank = app_setting('bank_name') . ' ' . app_setting('bank_account_number') . ' a.n. ' . app_setting('bank_account_holder');
        }

        $nomorKonfirmasi = app_setting('payment_confirmation_phone', app_setting('school_whatsapp', '-'));
        $kodeUnik = 'TAG-' . date('ym') . '-' . substr(md5($siswaId . time()), 0, 6);

        $replacements = [
            '{salam}'            => $randomSalam,
            '{nama_siswa}'       => $siswa['nama_lengkap'],
            '{kelas}'            => $namaKelas,
            '{nama_sekolah}'     => app_setting('school_name', 'Sekolah'),
            '{periode}'          => $namaBulan . ' ' . $tahun,
            '{rincian_tagihan}'  => trim($rincianText),
            '{total_tagihan}'    => rupiah($totalTunggakan),
            '{info_bank}'        => $infoBank,
            '{nomor_konfirmasi}' => $nomorKonfirmasi,
            '{kode_unik}'        => $kodeUnik,
        ];

        $pesanFinal = str_replace(array_keys($replacements), array_values($replacements), $template);

        // Normalisasi nomor telepon ke standar internasional (628xxxx)
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        } elseif (str_starts_with($cleanPhone, '8')) {
            $cleanPhone = '62' . $cleanPhone;
        }

        $waLink = 'https://api.whatsapp.com/send?phone=' . $cleanPhone . '&text=' . rawurlencode($pesanFinal);

        return $this->response->setJSON([
            'status'  => 'success',
            'phone'   => $cleanPhone,
            'message' => $pesanFinal,
            'wa_link' => $waLink
        ]);
    }
}
