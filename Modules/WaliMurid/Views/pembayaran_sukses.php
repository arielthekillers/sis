<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><a href="<?= base_url('wali-murid/dashboard') ?>" class="text-gray-500 hover:text-gray-900 dark:text-slate-400">Portal Orang Tua</a></li>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><a href="<?= base_url('wali-murid/keuangan') ?>" class="text-gray-500 hover:text-gray-900 dark:text-slate-400">Tagihan & SPP</a></li>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Status Pembayaran</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Konfirmasi Pembayaran Online Xendit
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Status pemrosesan tagihan melalui gateway pembayaran online Xendit.
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="max-w-xl mx-auto py-4">
    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-800 p-8 text-center">
        <?php $isPaid = ($trx && $trx['status'] === 'PAID'); ?>

        <?php if($isPaid): ?>
            <!-- Ikon Sukses Lunas -->
            <div class="w-20 h-20 rounded-3xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-4xl mx-auto mb-4 border border-emerald-200 dark:border-emerald-800/40 shadow-lg shadow-emerald-500/10">
                <i class="ph ph-check-circle"></i>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 mb-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                Pembayaran Berhasil & Lunas
            </span>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">
                Alhamdulillah! Pembayaran Diterima
            </h3>
            <p class="text-xs text-gray-500 dark:text-slate-400 max-w-md mx-auto mb-6">
                Pembayaran online Anda telah berhasil diverifikasi oleh sistem sekolah. Kuitansi resmi pembayaran telah diterbitkan secara otomatis.
            </p>
        <?php else: ?>
            <!-- Ikon Sedang Diproses -->
            <div class="w-20 h-20 rounded-3xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-4xl mx-auto mb-4 border border-blue-200 dark:border-blue-800/40 shadow-lg shadow-blue-500/10">
                <i class="ph ph-clock"></i>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 mb-2">
                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                Menunggu Konfirmasi Sistem
            </span>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">
                Pembayaran Sedang Diverifikasi
            </h3>
            <p class="text-xs text-gray-500 dark:text-slate-400 max-w-md mx-auto mb-6">
                Jika Anda telah menyelesaikan transaksi di Xendit, sistem akan mengupdate status pembayaran secara otomatis dalam hitungan menit melalui webhook.
            </p>
        <?php endif; ?>

        <!-- Rincian Transaksi -->
        <?php if($trx): ?>
            <div class="p-5 bg-gray-50 dark:bg-slate-700/40 rounded-2xl border border-gray-100 dark:border-slate-700 text-left text-xs space-y-3 mb-6">
                <div class="flex items-center justify-between pb-2 border-b border-gray-200/60 dark:border-slate-600">
                    <span class="text-gray-400">Nomor Referensi:</span>
                    <span class="font-mono font-bold text-gray-800 dark:text-slate-200"><?= esc($trx['external_id']) ?></span>
                </div>
                <?php if($siswa): ?>
                    <div class="flex items-center justify-between pb-2 border-b border-gray-200/60 dark:border-slate-600">
                        <span class="text-gray-400">Nama Ananda:</span>
                        <span class="font-bold text-gray-800 dark:text-slate-200"><?= esc($siswa['nama_lengkap']) ?> (<?= esc($siswa['nis'] ?: '-') ?>)</span>
                    </div>
                <?php endif; ?>
                <?php if($tagihan): ?>
                    <div class="flex items-center justify-between pb-2 border-b border-gray-200/60 dark:border-slate-600">
                        <span class="text-gray-400">Jenis Tagihan:</span>
                        <span class="font-bold text-gray-800 dark:text-slate-200"><?= esc($tagihan['nama_pos'] ?: 'SPP / Tagihan') ?></span>
                    </div>
                <?php endif; ?>
                <div class="flex items-center justify-between pt-1 text-sm">
                    <span class="text-gray-700 dark:text-slate-300 font-bold">Total Pembayaran:</span>
                    <span class="font-black text-indigo-600 dark:text-indigo-400 text-base"><?= rupiah($trx['amount']) ?></span>
                </div>
            </div>
        <?php endif; ?>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="<?= base_url('wali-murid/keuangan') ?>" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-500/20 transition-all flex items-center justify-center gap-2">
                <i class="ph ph-receipt"></i>
                <span>Lihat Riwayat Tagihan</span>
            </a>
            <a href="<?= base_url('wali-murid/dashboard') ?>" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-slate-200 text-xs font-semibold transition-all flex items-center justify-center gap-2">
                <i class="ph ph-house"></i>
                <span>Kembali ke Beranda</span>
            </a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

