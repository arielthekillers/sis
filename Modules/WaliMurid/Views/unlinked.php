<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Portal Orang Tua</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Portal Orang Tua / Wali Murid
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-8 text-center max-w-xl mx-auto">
    <div class="w-16 h-16 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl">
        <i class="ph ph-baby"></i>
    </div>
    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Akun Belum Ditautkan ke Siswa</h3>
    <p class="text-sm text-gray-500 dark:text-slate-400 leading-relaxed mb-6">
        Akun orang tua Anda belum terhubung dengan data ananda yang terdaftar di sekolah. Silakan hubungi bagian Tata Usaha / Admin sekolah untuk menautkan akun ini ke data putra-putri Anda.
    </p>
    <a href="<?= base_url('sis') ?>" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2.5 rounded-xl text-sm transition-all shadow-md shadow-blue-500/20">
        <i class="ph ph-house"></i> Kembali ke Beranda
    </a>
</div>
<?= $this->endSection() ?>

