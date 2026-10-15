<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Keuangan & Pembayaran</span></li>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Setup Tarif Siswa</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Setup Tarif Tagihan per Siswa
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Pilih kelas di bawah ini untuk mengatur komponen biaya spesifik tiap siswa (Tahun Ajaran: <?= esc($tahun_aktif) ?>).
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (empty($rombel)): ?>
        <div class="col-span-full p-8 text-center bg-white dark:bg-slate-800 rounded-xl border border-gray-100 dark:border-slate-800">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-slate-700 mb-4">
                <i class="ph ph-chalkboard-teacher text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Belum ada Kelas/Rombel</h3>
            <p class="text-gray-500 dark:text-slate-400 text-sm mb-4">Silakan buat Rombongan Belajar terlebih dahulu.</p>
        </div>
    <?php else: ?>
        <?php foreach ($rombel as $r): ?>
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden hover:shadow-md transition-shadow group flex flex-col">
            <div class="p-5 flex-1 border-b border-gray-50 dark:border-slate-700/50">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-12 h-12 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20">
                        <span class="font-bold text-lg"><?= esc($r['tingkat']) ?></span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight"><?= esc($r['nama_kelas']) ?></h3>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Tingkat: <?= esc($r['tingkat']) ?></p>
                    </div>
                </div>
            </div>
            
            <a href="<?= base_url('keuangan/setup-tarif/kelas/' . $r['id']) ?>" class="block w-full py-3 px-5 text-center text-sm font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-50/50 dark:bg-emerald-900/10 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-colors">
                Setup Tarif Siswa <i class="ph ph-arrow-right ml-1"></i>
            </a>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
