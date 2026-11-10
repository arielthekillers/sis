<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><a href="<?= base_url('wali-murid/dashboard') ?>" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Portal Orang Tua</a></li>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Informasi Keuangan</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Informasi Keuangan & Tagihan
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Pantau status pembayaran SPP, administrasi, dan riwayat transaksi resmi sekolah.
<?= $this->endSection() ?>

<?= $this->section('page_actions') ?>
<a href="<?= base_url('wali-murid/dashboard') ?>" class="bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 px-4 py-2 rounded-xl text-sm font-medium hover:bg-gray-200 dark:hover:bg-slate-700 transition-colors flex items-center gap-2">
    <i class="ph ph-arrow-left"></i> Kembali ke Dashboard
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div x-data="{ activeTab: '<?= count($anakList) > 1 ? 'keluarga' : 'anak_' . $anakList[0]['siswa_id'] ?>' }" class="space-y-6">

    <!-- Card Rekening Pembayaran & Petunjuk Transfer -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Card Rekening Resmi Bank -->
        <div class="lg:col-span-2 bg-gradient-to-r from-emerald-600 to-teal-700 rounded-2xl text-white p-6 shadow-lg shadow-emerald-600/10 flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="space-y-1 text-center sm:text-left">
                <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-semibold mb-1">
                    <i class="ph ph-bank"></i> Rekening Resmi Sekolah
                </span>
                <h3 class="text-xl md:text-2xl font-black">
                    <?= esc(app_setting('bank_name', 'Bank Syariah Indonesia (BSI)')) ?>
                </h3>
                <div class="flex items-center justify-center sm:justify-start gap-2 text-emerald-100 text-sm">
                    <span class="font-mono text-lg font-bold text-white tracking-wider"><?= esc(app_setting('bank_account_number', '7123456789')) ?></span>
                    <span>a.n. <?= esc(app_setting('bank_account_name', app_setting('nama_sekolah', 'Sekolah'))) ?></span>
                </div>
            </div>

            <?php 
                $noWaKonfirmasi = app_setting('wa_payment_confirmation', app_setting('wa_center_number', '08123456789'));
                $pesanWa = urlencode("Halo Admin Keuangan Sekolah, saya ingin konfirmasi pembayaran tagihan ananda. Terima kasih.");
            ?>
            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $noWaKonfirmasi) ?>?text=<?= $pesanWa ?>" target="_blank" class="flex-shrink-0 inline-flex items-center gap-2 bg-white text-emerald-800 hover:bg-emerald-50 px-4 py-2.5 rounded-xl text-xs font-bold shadow-md transition-all">
                <i class="ph ph-whatsapp-logo text-base text-emerald-600"></i>
                <span>Konfirmasi Manual via WA</span>
            </a>
        </div>

        <!-- Banner Xendit Online Gateway -->
        <div class="bg-gradient-to-br from-indigo-900 via-indigo-950 to-slate-900 text-white rounded-2xl p-6 shadow-md border border-indigo-800/40 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between gap-2 mb-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                        <i class="ph ph-lightning"></i> Pembayaran Instan
                    </span>
                    <span class="text-[10px] font-semibold <?= $isXenditEnabled ? 'text-emerald-400' : 'text-gray-400' ?>">
                        <?= $isXenditEnabled ? '● Xendit Aktif' : '○ Nonaktif' ?>
                    </span>
                </div>
                <h4 class="text-base font-extrabold text-white">QRIS & Virtual Account</h4>
                <p class="text-xs text-indigo-200 mt-1 leading-relaxed">
                    <?php if($isXenditEnabled): ?>
                        Bayar tagihan langsung terbit kuitansi otomatis tanpa perlu konfirmasi manual.
                    <?php else: ?>
                        Gunakan tombol konfirmasi WhatsApp untuk verifikasi transfer ke admin sekolah.
                    <?php endif; ?>
                </p>
            </div>
            <div class="pt-3 border-t border-white/10 flex items-center gap-2 text-[10px] text-indigo-300">
                <i class="ph ph-shield-check text-sm text-emerald-400"></i>
                <span>Terverifikasi Otomatis oleh Sistem</span>
            </div>
        </div>
    </div>

    <!-- Tab Pilihan (Keluarga vs Per Anak) -->
    <div class="flex space-x-2 border-b border-gray-200 dark:border-slate-700 pb-2 overflow-x-auto">
        <?php if(count($anakList) > 1): ?>
            <button 
                @click="activeTab = 'keluarga'" 
                :class="activeTab === 'keluarga' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-300 hover:bg-gray-200'"
                class="px-4 py-2 rounded-xl text-xs md:text-sm font-bold transition-all flex items-center gap-2 whitespace-nowrap"
            >
                <i class="ph ph-users-three"></i> Ringkasan Seluruh Anak (Total Keluarga)
            </button>
        <?php endif; ?>

        <?php foreach($anakList as $a): ?>
            <button 
                @click="activeTab = 'anak_<?= $a['siswa_id'] ?>'" 
                :class="activeTab === 'anak_<?= $a['siswa_id'] ?>' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-300 hover:bg-gray-200'"
                class="px-4 py-2 rounded-xl text-xs md:text-sm font-bold transition-all flex items-center gap-2 whitespace-nowrap"
            >
                <i class="ph ph-student"></i> <?= esc($a['nama_panggilan'] ?: $a['nama_lengkap']) ?> (<?= esc($a['kelas']['nama_kelas'] ?? '-') ?>)
            </button>
        <?php endforeach; ?>
    </div>

    <!-- KONTEN TAB: RINGKASAN KELUARGA (GABUNGAN) -->
    <?php if(count($anakList) > 1): ?>
        <div x-show="activeTab === 'keluarga'" x-cloak class="space-y-6">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b border-gray-100 dark:border-slate-700/60">
                    <div>
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white">Total Kewajiban Tagihan Keluarga</h4>
                        <p class="text-xs text-gray-500 dark:text-slate-400">Total akumulasi tagihan berjalan untuk seluruh putra-putri Anda.</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-gray-400 block mb-0.5">Total Tagihan:</span>
                        <span class="text-2xl font-black text-blue-600 dark:text-blue-400"><?= rupiah($totalTagihanKeluarga) ?></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                    <?php foreach($anakList as $a): ?>
                        <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-5 border border-gray-100 dark:border-slate-700">
                            <div class="flex items-center justify-between mb-3">
                                <h5 class="font-bold text-gray-900 dark:text-white text-base">
                                    <?= esc($a['nama_lengkap']) ?>
                                </h5>
                                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full <?= ($a['sisa_tagihan'] <= 0) ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' ?>">
                                    <?= ($a['sisa_tagihan'] <= 0) ? 'Lunas' : rupiah($a['sisa_tagihan']) ?>
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mb-3">
                                Kelas: <strong><?= esc($a['kelas']['nama_kelas'] ?? '-') ?> (<?= esc($a['kelas']['tingkat'] ?? '-') ?>)</strong>
                            </p>
                            <button @click="activeTab = 'anak_<?= $a['siswa_id'] ?>'" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                                <span>Lihat Rincian Tagihan & Riwayat Bayar</span>
                                <i class="ph ph-arrow-right"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- KONTEN TAB: PER ANAK -->
    <?php foreach($anakList as $a): ?>
        <div x-show="activeTab === 'anak_<?= $a['siswa_id'] ?>'" x-cloak class="space-y-6">

            <!-- Tagihan Belum Lunas -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="ph ph-clock text-amber-500 text-lg"></i>
                            Tagihan Berjalan: <?= esc($a['nama_lengkap']) ?>
                        </h4>
                        <p class="text-xs text-gray-500 dark:text-slate-400">Daftar kewajiban yang belum dibayar atau masih dicicil.</p>
                    </div>
                    <span class="text-xs font-bold px-3 py-1 rounded-xl bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                        Sisa: <?= rupiah($a['sisa_tagihan']) ?>
                    </span>
                </div>

                <?php if(empty($a['tagihan'])): ?>
                    <div class="py-8 text-center bg-gray-50 dark:bg-slate-700/30 rounded-xl p-4">
                        <i class="ph ph-check-circle text-3xl text-emerald-500 mx-auto mb-1"></i>
                        <p class="text-xs font-bold text-gray-700 dark:text-slate-300">Tidak ada tagihan tertunggak untuk ananda.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="text-gray-400 dark:text-slate-500 border-b border-gray-100 dark:border-slate-700">
                                <tr>
                                    <th class="pb-3 font-semibold">Nama Pos Tagihan</th>
                                    <th class="pb-3 font-semibold">Jatuh Tempo</th>
                                    <th class="pb-3 font-semibold text-right">Nominal</th>
                                    <th class="pb-3 font-semibold text-right">Dibayar</th>
                                    <th class="pb-3 font-semibold text-right">Sisa Tagihan</th>
                                    <th class="pb-3 font-semibold text-center">Status</th>
                                    <th class="pb-3 font-semibold text-center">Aksi Bayar</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700/60 font-medium">
                                <?php foreach($a['tagihan'] as $t): ?>
                                    <?php $sisaRow = $t['nominal_tagihan'] - $t['nominal_dibayar']; ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors">
                                        <td class="py-3 font-bold text-gray-900 dark:text-white"><?= esc($t['nama_pos']) ?></td>
                                        <td class="py-3 text-gray-500 dark:text-slate-400"><?= tanggal_indo($t['jatuh_tempo']) ?></td>
                                        <td class="py-3 text-right text-gray-700 dark:text-slate-300"><?= rupiah($t['nominal_tagihan']) ?></td>
                                        <td class="py-3 text-right text-emerald-600 dark:text-emerald-400"><?= rupiah($t['nominal_dibayar']) ?></td>
                                        <td class="py-3 text-right font-bold text-rose-600 dark:text-rose-400"><?= rupiah($sisaRow) ?></td>
                                        <td class="py-3 text-center">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $t['status'] === 'Mencicil' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700' ?>">
                                                <?= esc($t['status']) ?>
                                            </span>
                                        </td>
                                        <td class="py-3 text-center">
                                            <?php if($isXenditEnabled): ?>
                                                <form action="<?= base_url('wali-murid/bayar-xendit') ?>" method="POST" class="inline" onsubmit="this.querySelector('button').disabled = true;">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="tagihan_id" value="<?= $t['id'] ?>">
                                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/20 transition-all hover:scale-105 whitespace-nowrap">
                                                        <i class="ph ph-credit-card"></i>
                                                        <span>Bayar Online</span>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-[11px] text-gray-400 italic">Transfer Bank</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Riwayat Pembayaran Resmi yang Selesai -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="ph ph-receipt text-emerald-600 text-lg"></i>
                            Riwayat Pembayaran Berhasil
                        </h4>
                        <p class="text-xs text-gray-500 dark:text-slate-400">Transaksi resmi yang telah diverifikasi oleh bagian keuangan.</p>
                    </div>
                </div>

                <?php if(empty($a['riwayat_bayar'])): ?>
                    <div class="py-6 text-center text-xs text-gray-400">
                        Belum ada riwayat transaksi pembayaran tercatat.
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="text-gray-400 dark:text-slate-500 border-b border-gray-100 dark:border-slate-700">
                                <tr>
                                    <th class="pb-3 font-semibold">No. Transaksi</th>
                                    <th class="pb-3 font-semibold">Tanggal Bayar</th>
                                    <th class="pb-3 font-semibold">Metode</th>
                                    <th class="pb-3 font-semibold text-right">Total Bayar</th>
                                    <th class="pb-3 font-semibold text-center">Kuitansi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700/60 font-medium">
                                <?php foreach($a['riwayat_bayar'] as $rb): ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors">
                                        <td class="py-3 font-mono font-bold text-gray-900 dark:text-white"><?= esc($rb['nomor_transaksi']) ?></td>
                                        <td class="py-3 text-gray-500 dark:text-slate-400"><?= tanggal_indo($rb['tanggal_bayar'], true) ?></td>
                                        <td class="py-3 text-gray-700 dark:text-slate-300"><?= esc($rb['metode_pembayaran'] ?? 'Transfer') ?></td>
                                        <td class="py-3 text-right font-bold text-emerald-600 dark:text-emerald-400"><?= rupiah($rb['total_bayar']) ?></td>
                                        <td class="py-3 text-center">
                                            <a href="<?= base_url('keuangan/kuitansi/' . $rb['id']) ?>" target="_blank" class="inline-flex items-center gap-1 text-blue-600 dark:text-blue-400 hover:underline font-bold text-[11px]">
                                                <i class="ph ph-printer"></i> Unduh Slip
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    <?php endforeach; ?>

</div>
<?= $this->endSection() ?>

