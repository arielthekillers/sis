<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Akademik & Nilai</span></li>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Penempatan Siswa</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Penempatan Anggota Rombel
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Pilih kelas di bawah ini untuk mengatur daftar siswa di dalamnya (Tahun Ajaran: <?= esc($tahun_aktif) ?>).
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (empty($rombel)): ?>
        <div class="col-span-full p-8 text-center bg-white dark:bg-slate-800 rounded-xl border border-gray-100 dark:border-slate-800">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-slate-700 mb-4">
                <i class="ph ph-chalkboard-teacher text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Belum ada Kelas/Rombel</h3>
            <p class="text-gray-500 dark:text-slate-400 text-sm mb-4">Silakan buat Rombongan Belajar terlebih dahulu di menu Rombel.</p>
            <a href="<?= base_url('akademik/rombel') ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                Kelola Rombel
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($rombel as $r): ?>
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden hover:shadow-md transition-shadow group flex flex-col">
            <div class="p-5 flex-1 border-b border-gray-50 dark:border-slate-700/50">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-lg bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-500/20">
                            <span class="font-bold text-lg"><?= esc($r['tingkat']) ?></span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight"><?= esc($r['nama_kelas']) ?></h3>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Tingkat: <?= esc($r['tingkat']) ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500 dark:text-slate-400">Total Siswa:</span>
                    <span class="font-bold text-gray-900 dark:text-white bg-gray-100 dark:bg-slate-700 px-2 py-0.5 rounded-md">
                        <?= $r['jumlah_siswa'] ?> Orang
                    </span>
                </div>
            </div>
            
            <a href="<?= base_url('akademik/anggota-rombel/detail/' . $r['id']) ?>" class="block w-full py-3 px-5 text-center text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                Kelola Anggota Kelas <i class="ph ph-arrow-right ml-1"></i>
            </a>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
