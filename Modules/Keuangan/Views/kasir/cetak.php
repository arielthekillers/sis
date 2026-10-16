<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><a href="<?= base_url('keuangan/kasir') ?>" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Kasir Pembayaran</a></li>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Cetak Kuitansi</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Kuitansi Pembayaran
<?= $this->endSection() ?>

<?= $this->section('page_actions') ?>
<button @click="$dispatch('open-wa-modal')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center gap-2">
    <i class="ph ph-whatsapp-logo text-base"></i> Kirim WhatsApp
</button>
<button onclick="window.print()" class="bg-blue-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors shadow-sm flex items-center gap-2">
    <i class="ph ph-printer"></i> Cetak PDF
</button>
<button onclick="confirmAction('Batalkan Kuitansi?', 'Semua tagihan dan deposit terkait transaksi ini akan di-rollback ke keadaan semula!', 'Ya, Batalkan', () => { window.location.href='<?= base_url('keuangan/kasir/batal/'.$pembayaran['id']) ?>' })" class="bg-red-50 dark:bg-red-500/10 text-red-600 border border-red-200 dark:border-red-500/20 px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors">
    <i class="ph ph-trash"></i> Batalkan Kuitansi
</button>
<a href="<?= base_url('keuangan/kasir') ?>" class="bg-white dark:bg-slate-700 text-gray-700 dark:text-slate-200 border border-gray-200 dark:border-slate-600 px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-slate-600 transition-colors">
    Kembali ke Kasir
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
@media print {
    body * {
        visibility: hidden;
    }
    #print-area, #print-area * {
        visibility: visible;
    }
    #print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .print-hide {
        display: none !important;
    }
}
</style>

<div x-data="{ waModal: false, waPhone: '<?= esc($siswa['no_hp_ortu'] ?? '') ?>', isSendingWa: false }"
     @open-wa-modal.window="waModal = true"
     class="max-w-3xl mx-auto">
    <div id="print-area" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-8">
        
        <!-- Kop Surat -->
        <div class="flex items-center justify-between border-b-2 border-gray-200 dark:border-slate-700 pb-6 mb-6">
            <div class="flex items-center gap-4">
                <img src="<?= base_url(app_setting('school_logo', 'uploads/logo.png')) ?>" alt="<?= esc(app_setting('app_name', 'RSK APPS')) ?>" class="h-16 w-auto object-contain">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white"><?= esc(app_setting('school_name', 'Rumah Semut Kecil')) ?></h2>
                    <p class="text-sm font-medium text-blue-600 dark:text-blue-400"><?= esc(app_setting('app_tagline', 'Preschool & Kindergarten')) ?></p>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">
                        <?= esc(app_setting('school_address', '')) ?>
                        <?= app_setting('school_city') ? '· ' . esc(app_setting('school_city')) : '' ?>
                        <?= app_setting('school_phone') ? '· Telp: ' . esc(app_setting('school_phone')) : '' ?>
                    </p>
                    <?php if (app_setting('school_website')): ?>
                    <p class="text-xs text-gray-400 dark:text-slate-500 mt-0.5"><?= esc(str_replace(['https://', 'http://'], '', app_setting('school_website'))) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="text-right">
                <h1 class="text-3xl font-black text-gray-200 dark:text-slate-700 uppercase tracking-wider">Kuitansi</h1>
                <p class="font-semibold text-gray-800 dark:text-slate-200 mt-1"><?= esc($pembayaran['no_kwitansi']) ?></p>
            </div>
        </div>
        
        <!-- Info Pembayaran -->
        <div class="grid grid-cols-2 gap-8 mb-8">
            <div>
                <p class="text-sm text-gray-500 dark:text-slate-400 mb-1">Telah terima dari:</p>
                <p class="font-bold text-gray-900 dark:text-white text-lg"><?= esc($siswa['nama_lengkap']) ?></p>
                <p class="text-sm text-gray-700 dark:text-slate-300">NIS: <?= esc($siswa['nis'] ?: '-') ?> | Kelas: <?= esc($kelas) ?></p>
            </div>
            <div class="text-right">
                <table class="ml-auto text-sm text-gray-600 dark:text-slate-300">
                    <tr>
                        <td class="py-1 pr-4 text-gray-500 dark:text-slate-400">Tanggal:</td>
                        <td class="py-1 font-semibold text-gray-900 dark:text-white"><?= date('d M Y H:i', strtotime($pembayaran['tanggal_bayar'])) ?></td>
                    </tr>
                    <tr>
                        <td class="py-1 pr-4 text-gray-500 dark:text-slate-400">Metode:</td>
                        <td class="py-1 font-semibold text-gray-900 dark:text-white"><?= esc($pembayaran['metode_pembayaran']) ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Tabel Rincian -->
        <table class="w-full text-left border-collapse mb-8">
            <thead>
                <tr class="bg-gray-50 dark:bg-slate-700/50">
                    <th class="py-3 px-4 text-sm font-semibold text-gray-700 dark:text-slate-200 border-y border-gray-200 dark:border-slate-600 w-16">No</th>
                    <th class="py-3 px-4 text-sm font-semibold text-gray-700 dark:text-slate-200 border-y border-gray-200 dark:border-slate-600">Untuk Pembayaran</th>
                    <th class="py-3 px-4 text-sm font-semibold text-gray-700 dark:text-slate-200 border-y border-gray-200 dark:border-slate-600 text-right">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                <?php $no=1; foreach($details as $d): ?>
                <tr>
                    <td class="py-3 px-4 text-sm text-gray-600 dark:text-slate-300"><?= $no++ ?></td>
                    <td class="py-3 px-4 text-sm font-medium text-gray-900 dark:text-white"><?= esc($d['judul_tagihan']) ?></td>
                    <td class="py-3 px-4 text-sm font-semibold text-gray-900 dark:text-white text-right"><?= rupiah($d['nominal_alokasi']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="py-4 px-4 text-right text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-t-2 border-gray-200 dark:border-slate-600">Total Pembayaran</td>
                    <td class="py-4 px-4 text-right text-xl font-black text-blue-600 dark:text-blue-400 border-t-2 border-gray-200 dark:border-slate-600"><?= rupiah($pembayaran['total_bayar']) ?></td>
                </tr>
            </tfoot>
        </table>
        
        <!-- Catatan & TTD -->
        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-slate-700/60 flex flex-col sm:flex-row items-start justify-between gap-6">
            <div class="max-w-md">
                <p class="text-[11px] text-gray-500 dark:text-slate-400 italic leading-relaxed">
                    * <?= esc(app_setting('receipt_footer', 'Kuitansi ini adalah bukti pembayaran yang sah dan diterbitkan secara resmi oleh sistem.')) ?>
                </p>
                <?php if (app_setting('bank_account_number')): ?>
                <div class="mt-2 text-xs text-gray-600 dark:text-slate-400 bg-gray-50 dark:bg-slate-700/40 p-2.5 rounded-lg border border-gray-100 dark:border-slate-700">
                    <span class="font-semibold text-gray-700 dark:text-slate-300">Rekening Resmi:</span>
                    <?= esc(app_setting('bank_name')) ?> <?= esc(app_setting('bank_account_number')) ?> a.n. <?= esc(app_setting('bank_account_holder')) ?>
                </div>
                <?php endif; ?>
            </div>

            <div class="text-center shrink-0 self-end sm:self-auto">
                <p class="text-xs text-gray-500 dark:text-slate-400 mb-1"><?= esc(app_setting('school_city', 'Bandung')) ?>, <?= date('d/m/Y', strtotime($pembayaran['tanggal_bayar'])) ?></p>
                <p class="text-sm text-gray-600 dark:text-slate-400 mb-14">Petugas / Kasir,</p>
                <div class="w-44 border-b border-gray-400 dark:border-slate-500 mb-1 mx-auto"></div>
                <p class="text-xs font-semibold text-gray-800 dark:text-slate-200"><?= esc(app_setting('cashier_name', 'Admin Keuangan')) ?></p>
            </div>
        </div>
        
    </div>

    <!-- Modal Kirim WhatsApp -->
    <div x-show="waModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" x-transition.opacity>
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="waModal = false"></div>
        <div class="min-h-full flex items-center justify-center p-4">
            <div class="relative bg-white dark:bg-slate-800 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100 dark:border-slate-700 space-y-5" @click.stop>
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-slate-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-600/20 dark:text-emerald-400 flex items-center justify-center text-xl">
                            <i class="ph ph-whatsapp-logo"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">Kirim Kuitansi WhatsApp</h3>
                            <p class="text-xs text-gray-500 dark:text-slate-400">Via Gateway Wapify</p>
                        </div>
                    </div>
                    <button @click="waModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200">
                        <i class="ph ph-x text-lg"></i>
                    </button>
                </div>

                <form action="<?= base_url('keuangan/kasir/kirim-wa/'.$pembayaran['id']) ?>" method="POST" @submit="isSendingWa = true" class="space-y-4">
                    <?= csrf_field() ?>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Nomor WhatsApp Wali Murid</label>
                        <div class="relative">
                            <i class="ph ph-phone absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input type="text" name="no_hp" x-model="waPhone" required
                                placeholder="08xxxxxxxxxx / 628xxxxxxxxxx"
                                class="w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-sm font-mono font-medium text-gray-900 dark:text-white focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                        </div>
                        <p class="text-[11px] text-gray-500 dark:text-slate-400 mt-1">Otomatis terisi dari data No. HP Orang Tua siswa.</p>
                    </div>

                    <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-slate-700/40 text-xs text-gray-600 dark:text-slate-300 space-y-1 border border-gray-100 dark:border-slate-700">
                        <p class="font-semibold text-gray-800 dark:text-white">Rincian Transaksi:</p>
                        <p>Siswa: <span class="font-medium"><?= esc($siswa['nama_lengkap']) ?></span> (Kelas <?= esc($kelas) ?>)</p>
                        <p>Kuitansi: <span class="font-mono font-bold text-gray-900 dark:text-white"><?= esc($pembayaran['no_kwitansi']) ?></span></p>
                        <p>Total Bayar: <span class="font-bold text-emerald-600 dark:text-emerald-400"><?= rupiah($pembayaran['total_bayar']) ?></span></p>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="waModal = false" class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-xl transition-colors">
                            Batal
                        </button>
                        <button type="submit" :disabled="isSendingWa" class="inline-flex items-center gap-2 px-5 py-2 text-xs font-semibold rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white transition-all shadow-md shadow-emerald-600/20 active:scale-95">
                            <i class="ph ph-spinner animate-spin" x-show="isSendingWa" style="display: none;"></i>
                            <i class="ph ph-paper-plane-tilt text-base" x-show="!isSendingWa"></i>
                            <span x-text="isSendingWa ? 'Mengirim...' : 'Kirim Sekarang'">Kirim Sekarang</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
