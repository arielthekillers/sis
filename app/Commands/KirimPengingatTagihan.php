<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\WapifyService;
use Modules\Keuangan\Models\TagihanModel;
use Modules\CivitasAkademika\Models\SiswaModel;

class KirimPengingatTagihan extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'WhatsApp';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'wa:kirim-tagihan';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Kirim otomatis pesan pengingat tagihan bulanan ke wali murid dengan proteksi Anti-Banned.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'wa:kirim-tagihan [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [
        '--force'      => 'Abaikan validasi tanggal pengingat bulanan (paksa kirim hari ini).',
        '--bulan'      => 'Bulan tagihan (01 - 12). Default: bulan berjalan.',
        '--tahun'      => 'Tahun tagihan (YYYY). Default: tahun berjalan.',
        '--limit'      => 'Batasi jumlah pesan yang dikirim per eksekusi.',
        '--skip-check' => 'Lewati pengecekan nomor aktif ke server Wapify sebelum kirim.',
        '--dry-run'    => 'Simulasi pengiriman tanpa benar-benar mengirim pesan ke WhatsApp.',
    ];

    /**
     * Actually run the command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        helper(['app_setting', 'rupiah', 'tanggal_indo']);

        CLI::write("==================================================", 'cyan');
        CLI::write("   PENGINGAT TAGIHAN WHATSAPP (ANTI-BANNED ENGINE)   ", 'yellow');
        CLI::write("==================================================", 'cyan');

        $isForce     = CLI::getOption('force') !== null;
        $dryRun      = CLI::getOption('dry-run') !== null;
        $skipCheck   = CLI::getOption('skip-check') !== null;
        $bulan       = CLI::getOption('bulan') ?? date('m');
        $tahun       = CLI::getOption('tahun') ?? date('Y');
        $limit       = CLI::getOption('limit') ? (int) CLI::getOption('limit') : null;

        $reminderDay = (int) app_setting('wa_reminder_day', 10);
        $todayDay    = (int) date('j');

        $isActive = (bool) app_setting('wapify_is_active', 0);
        if (!$isActive && !$dryRun) {
            CLI::error("[PERINGATAN] Integrasi Wapify saat ini NON-AKTIF di Konfigurasi Sistem.");
            CLI::write("Aktifkan terlebih dahulu di menu Konfigurasi Sistem > WhatsApp Gateway.\n");
            return;
        }

        // Cek tanggal pengingat
        if ($todayDay !== $reminderDay && !$isForce) {
            CLI::write("Hari ini tanggal {$todayDay}. Tanggal broadcast yang dikonfigurasi adalah tanggal {$reminderDay}.", 'light_gray');
            CLI::write("Pengiriman dilewati secara otomatis untuk mematuhi jadwal sekolah.");
            CLI::write("Tips: Gunakan opsi --force untuk memaksa pengiriman hari ini.\n", 'dark_gray');
            return;
        }

        CLI::write("Mode: " . ($dryRun ? "[SIMULASI / DRY-RUN]" : "[PENGIRIMAN AKTIF]"), $dryRun ? 'yellow' : 'green');
        CLI::write("Periode Tagihan: Bulan {$bulan} / Tahun {$tahun}");

        $bulanNames = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        $namaBulan = $bulanNames[$bulan] ?? 'Bulan ' . $bulan;
        $periodeString = "%" . $namaBulan . " " . $tahun;

        $db = \Config\Database::connect();
        $builder = $db->table('keu_tagihan')
            ->select('
                keu_tagihan.id as tagihan_id,
                keu_tagihan.judul_tagihan,
                keu_tagihan.nominal_tagihan,
                keu_tagihan.nominal_dibayar,
                (keu_tagihan.nominal_tagihan - keu_tagihan.nominal_dibayar) as sisa_tagihan,
                keu_tagihan.status as status_tagihan,
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
            ->like('keu_tagihan.judul_tagihan', $periodeString, 'before')
            ->orderBy('kelas.nama_kelas', 'ASC')
            ->orderBy('siswa.nama_lengkap', 'ASC');

        $rows = $builder->get()->getResultArray();

        if (empty($rows)) {
            CLI::write("\n[INFO] Tidak ada tagihan tertunggak untuk periode {$namaBulan} {$tahun}.\n", 'green');
            return;
        }

        // Kelompokkan per siswa
        $siswaTunggakan = [];
        foreach ($rows as $row) {
            $sId = $row['siswa_id'];
            if (!isset($siswaTunggakan[$sId])) {
                $siswaTunggakan[$sId] = [
                    'siswa_id'       => $sId,
                    'nama_lengkap'   => $row['nama_lengkap'],
                    'nis'            => $row['nis'],
                    'no_hp_ortu'     => trim($row['no_hp_ortu'] ?? ''),
                    'nama_kelas'     => $row['nama_kelas'] ?: '-',
                    'total_tunggakan'=> 0,
                    'items'          => [],
                ];
            }

            $siswaTunggakan[$sId]['items'][] = [
                'tagihan_id'     => $row['tagihan_id'],
                'judul_tagihan'  => $row['judul_tagihan'],
                'sisa'           => (float) $row['sisa_tagihan'],
            ];
            $siswaTunggakan[$sId]['total_tunggakan'] += (float) $row['sisa_tagihan'];
        }

        $list = array_values($siswaTunggakan);
        if ($limit && $limit > 0) {
            $list = array_slice($list, 0, $limit);
        }

        $totalSiswa = count($list);
        CLI::write("\nDitemukan {$totalSiswa} siswa memiliki tunggakan pada periode ini.\n", 'white');

        $wapify = new WapifyService();
        $delayMin = (int) app_setting('wa_delay_min', 8);
        $delayMax = (int) app_setting('wa_delay_max', 15);
        if ($delayMin < 3) $delayMin = 3;
        if ($delayMax < $delayMin) $delayMax = $delayMin + 5;

        $sentCount    = 0;
        $skipCount    = 0;
        $failCount    = 0;

        foreach ($list as $idx => $s) {
            $num = $idx + 1;
            CLI::write("--------------------------------------------------", 'dark_gray');
            CLI::write("[{$num}/{$totalSiswa}] Siswa: {$s['nama_lengkap']} ({$s['nama_kelas']})", 'light_cyan');

            if (empty($s['no_hp_ortu'])) {
                CLI::write("  -> [SKIP] Nomor HP orang tua kosong.", 'yellow');
                $skipCount++;
                continue;
            }

            $phone = $s['no_hp_ortu'];
            CLI::write("  -> Nomor Tujuan: {$phone}");

            // Cek keaktifan nomor WhatsApp
            if (!$skipCheck && !$dryRun) {
                CLI::write("  -> Memeriksa validitas nomor WA...", 'dark_gray');
                $checkRes = $wapify->checkContact($phone);
                if ($checkRes['status'] !== 'success' || empty($checkRes['is_valid'])) {
                    CLI::write("  -> [SKIP] Nomor {$phone} TIDAK TERDAFTAR di WhatsApp.", 'red');
                    $skipCount++;
                    continue;
                }
                CLI::write("  -> Nomor terdaftar di WhatsApp. Siap mengirim...", 'green');
            }

            // Susun rincian tagihan
            $rincianText = "";
            $noItem = 1;
            foreach ($s['items'] as $item) {
                $rincianText .= "{$noItem}. *{$item['judul_tagihan']}*: " . rupiah($item['sisa']) . "\n";
                $noItem++;
            }

            // Variasi salam (Spintax)
            $salamList = ['Assalamu\'alaikum,', 'Selamat Pagi,', 'Salam Sejahtera,', 'Halo Bpk/Ibu,'];
            $randomSalam = $salamList[array_rand($salamList)];

            // Info Bank
            $infoBank = "-";
            if (app_setting('bank_account_number')) {
                $infoBank = app_setting('bank_name') . ' ' . app_setting('bank_account_number') . ' a.n. ' . app_setting('bank_account_holder');
            }

            $kodeUnik = 'TAG-' . date('ym') . '-' . substr(md5($s['siswa_id'] . time()), 0, 6);

            $template = app_setting('wa_reminder_template');
            if (empty($template)) {
                $template = "{salam} Bpk/Ibu Wali Murid dari *{nama_siswa}* ({kelas}).\n\n"
                          . "Kami informasikan rincian tagihan di *{nama_sekolah}* periode *{periode}*:\n\n"
                          . "{rincian_tagihan}\n"
                          . "💰 *TOTAL TAGIHAN: {total_tagihan}*\n\n"
                          . "💳 Pembayaran dapat ditransfer ke:\n{info_bank}\n\n"
                          . "_Ref: {kode_unik}_";
            }

            $replacements = [
                '{salam}'           => $randomSalam,
                '{nama_siswa}'      => $s['nama_lengkap'],
                '{kelas}'           => $s['nama_kelas'],
                '{nama_sekolah}'    => app_setting('school_name', 'Sekolah'),
                '{periode}'         => $namaBulan . ' ' . $tahun,
                '{rincian_tagihan}' => trim($rincianText),
                '{total_tagihan}'   => rupiah($s['total_tunggakan']),
                '{info_bank}'       => $infoBank,
                '{kode_unik}'       => $kodeUnik,
            ];

            $pesanFinal = str_replace(array_keys($replacements), array_values($replacements), $template);

            if ($dryRun) {
                CLI::write("  -> [DRY-RUN] Pesan siap dikirim:\n" . substr($pesanFinal, 0, 150) . "...", 'yellow');
                $sentCount++;
            } else {
                $res = $wapify->sendMessage($phone, $pesanFinal);
                if ($res['status'] === 'success') {
                    CLI::write("  -> [BERHASIL] Pesan terkirim ke {$phone}.", 'green');
                    $sentCount++;
                } else {
                    CLI::write("  -> [GAGAL] " . ($res['message'] ?? 'Gagal mengirim pesan.'), 'red');
                    $failCount++;
                }
            }

            // Anti-banned jitter delay jika masih ada pesan berikutnya
            if ($num < $totalSiswa) {
                $jitter = rand($delayMin, $delayMax);
                CLI::write("  -> [Anti-Banned] Menunggu jeda acak {$jitter} detik...", 'dark_gray');
                if (!$dryRun) {
                    sleep($jitter);
                }
            }
        }

        CLI::write("\n==================================================", 'cyan');
        CLI::write("   RINGKASAN EKSEKUSI PENGINGAT TAGIHAN", 'yellow');
        CLI::write("==================================================", 'cyan');
        CLI::write("Total Siswa Diproses : {$totalSiswa}");
        CLI::write("Berhasil Terkirim    : {$sentCount}", 'green');
        CLI::write("Dilewati (Skip)      : {$skipCount}", 'yellow');
        CLI::write("Gagal Terkirim       : {$failCount}", 'red');
        CLI::write("==================================================\n", 'cyan');
    }
}
