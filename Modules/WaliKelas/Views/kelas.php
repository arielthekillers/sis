<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><a href="<?= base_url('wali-kelas/dashboard') ?>" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Portal Wali Kelas</a></li>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium"><?= esc($rombel['nama_kelas']) ?></span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Rombel <?= esc($rombel['nama_kelas']) ?> (<?= esc($rombel['tingkat']) ?>)
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Tahun Ajaran <?= esc($rombel['tahun_ajaran']) ?> • Kelola data murid dan input jurnal perkembangan ananda.
<?= $this->endSection() ?>

<?= $this->section('page_actions') ?>
<a href="<?= base_url('wali-kelas/dashboard') ?>" class="bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 px-4 py-2 rounded-xl text-sm font-medium hover:bg-gray-200 dark:hover:bg-slate-700 transition-colors flex items-center gap-2">
    <i class="ph ph-arrow-left"></i> Kembali ke Daftar Kelas
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div x-data="{ searchQuery: '' }" class="space-y-6">

    <!-- Card Pencarian & Ringkasan -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-4 sm:p-6 flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div class="w-full sm:w-80 relative">
            <i class="ph ph-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
            <input 
                type="text" 
                x-model="searchQuery" 
                placeholder="Cari nama murid atau NIS..." 
                class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all"
            >
        </div>
        <div class="flex items-center gap-3 self-end sm:self-auto text-xs font-semibold text-gray-500 dark:text-slate-400">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                <i class="ph ph-users text-sm"></i> Total Murid: <?= count($siswaList) ?>
            </span>
        </div>
    </div>

    <!-- Grid Siswa -->
    <?php if(empty($siswaList)): ?>
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-12 text-center border border-gray-100 dark:border-slate-800">
            <div class="w-16 h-16 bg-gray-50 dark:bg-slate-700 rounded-full flex items-center justify-center text-gray-400 mx-auto mb-3 text-2xl">
                <i class="ph ph-user-circle"></i>
            </div>
            <h4 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Belum Ada Siswa di Kelas Ini</h4>
            <p class="text-sm text-gray-500 dark:text-slate-400 max-w-sm mx-auto">
                Siswa belum ditambahkan ke dalam rombel ini. Hubungi operator akademik untuk memasukkan data siswa.
            </p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php foreach($siswaList as $s): ?>
                <div 
                    x-show="!searchQuery || '<?= strtolower(esc($s['nama_lengkap'])) ?>'.includes(searchQuery.toLowerCase()) || '<?= strtolower(esc($s['nama_panggilan'] ?? '')) ?>'.includes(searchQuery.toLowerCase()) || '<?= esc($s['nis'] ?? '') ?>'.includes(searchQuery)"
                    x-transition
                    class="group bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-slate-800 hover:shadow-md hover:border-blue-300 dark:hover:border-blue-700/50 transition-all flex flex-col justify-between"
                >
                    <div class="flex items-start gap-4 mb-4">
                        <!-- Foto Siswa -->
                        <div class="relative flex-shrink-0">
                            <?php if(!empty($s['foto'])): ?>
                                <img src="<?= base_url('uploads/profiles/' . $s['foto']) ?>" alt="<?= esc($s['nama_lengkap']) ?>" class="w-14 h-14 object-cover rounded-xl border border-gray-100 dark:border-slate-700">
                            <?php else: ?>
                                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-slate-700 dark:to-slate-800 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold text-lg border border-gray-100 dark:border-slate-700">
                                    <?= strtoupper(substr($s['nama_lengkap'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                            <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-white dark:border-slate-800 flex items-center justify-center text-[9px] font-bold <?= $s['jenis_kelamin'] === 'L' ? 'bg-sky-500 text-white' : 'bg-pink-500 text-white' ?>">
                                <?= $s['jenis_kelamin'] ?>
                            </span>
                        </div>

                        <!-- Biodata Singkat -->
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-gray-900 dark:text-white text-base truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                <?= esc($s['nama_lengkap']) ?>
                            </h4>
                            <p class="text-xs text-gray-500 dark:text-slate-400 truncate mb-1.5">
                                <?= !empty($s['nama_panggilan']) ? 'Panggilan: ' . esc($s['nama_panggilan']) : 'NIS: ' . esc($s['nis'] ?: '-') ?>
                            </p>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold <?= $s['total_catatan'] > 0 ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400' : 'bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-slate-400' ?>">
                                <i class="ph ph-note-pencil"></i> <?= $s['total_catatan'] ?> Catatan Jurnal
                            </span>
                        </div>
                    </div>

                    <!-- Footer Tombol -->
                    <div class="pt-3 border-t border-gray-100 dark:border-slate-700/60 flex items-center gap-2">
                        <a href="<?= base_url('wali-kelas/siswa/' . $s['siswa_id'] . '/' . $rombel['id']) ?>" class="flex-1 inline-flex items-center justify-center gap-2 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-600 dark:hover:bg-blue-600 text-blue-700 dark:text-blue-400 hover:text-white dark:hover:text-white font-medium px-3 py-2 rounded-xl text-xs transition-all">
                            <i class="ph ph-notebook text-sm"></i>
                            <span>Catatan & Perkembangan</span>
                        </a>
                        <?php if(!empty($s['no_hp_ortu'])): ?>
                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $s['no_hp_ortu']) ?>" target="_blank" title="Hubungi Orang Tua via WhatsApp" class="p-2 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white dark:bg-emerald-900/30 dark:text-emerald-400 transition-colors">
                                <i class="ph ph-whatsapp-logo text-base"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

