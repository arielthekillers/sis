<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><a href="<?= base_url('wali-murid/dashboard') ?>" class="text-gray-500 hover:text-gray-900 dark:text-slate-400">Portal Orang Tua</a></li>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Perkembangan Ananda</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Jurnal Perkembangan Ananda
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Dokumentasi pengamatan dan catatan capaian tumbuh kembang ananda oleh bunda & ustadzah guru.
<?= $this->endSection() ?>

<?= $this->section('page_actions') ?>
<a href="<?= base_url('wali-murid/dashboard') ?>" class="bg-gray-100 hover:bg-gray-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-gray-700 dark:text-slate-200 px-4 py-2 rounded-xl text-xs md:text-sm font-semibold transition-all flex items-center gap-2">
    <i class="ph ph-arrow-left"></i>
    <span>Kembali ke Beranda</span>
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6" x-data="{ previewModal: false, previewSrc: '', previewTitle: '' }">

    <!-- CHILD SWITCHER (Jika Orang Tua memiliki lebih dari 1 anak) -->
    <?php if(count($anakList) > 1): ?>
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                    <i class="ph ph-users-three text-blue-600 dark:text-blue-400 text-base"></i>
                    <span>Pilih Data Ananda:</span>
                </div>
                <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
                    <?php foreach($anakList as $a): ?>
                        <?php $isActive = ((int)$a['siswa_id'] === (int)$activeAnakId); ?>
                        <a 
                            href="<?= base_url('wali-murid/perkembangan?anak_id=' . $a['siswa_id']) ?>" 
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs md:text-sm font-semibold transition-all whitespace-nowrap <?= $isActive ? 'bg-blue-600 text-white shadow-md shadow-blue-500/25 ring-2 ring-blue-500/30' : 'bg-gray-50 dark:bg-slate-700/60 text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700' ?>"
                        >
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold <?= $isActive ? 'bg-white text-blue-600' : 'bg-blue-100 text-blue-700 dark:bg-slate-600 dark:text-slate-200' ?>">
                                <?= strtoupper(substr($a['nama_lengkap'], 0, 1)) ?>
                            </span>
                            <span><?= esc($a['nama_panggilan'] ?: $a['nama_lengkap']) ?></span>
                            <?php if(!empty($a['kelas'])): ?>
                                <span class="text-[10px] opacity-80 font-normal">(<?= esc($a['kelas']['nama_kelas']) ?>)</span>
                            <?php endif; ?>
                            <?php if($isActive): ?>
                                <i class="ph ph-check-circle text-xs"></i>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Identitas Ringkas Ananda -->
    <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 text-white rounded-2xl p-6 shadow-md relative overflow-hidden">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 relative z-10">
            <div class="flex items-center gap-4 text-center sm:text-left">
                <?php if(!empty($activeAnak['foto'])): ?>
                    <img src="<?= base_url('uploads/profiles/' . $activeAnak['foto']) ?>" alt="<?= esc($activeAnak['nama_lengkap']) ?>" class="w-16 h-16 object-cover rounded-2xl border-2 border-white/30 shadow-md">
                <?php else: ?>
                    <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center text-white font-black text-2xl border-2 border-white/30 shadow-md">
                        <?= strtoupper(substr($activeAnak['nama_lengkap'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <div>
                    <span class="text-xs uppercase tracking-wider font-semibold text-blue-200">Buku Jurnal Pengamatan</span>
                    <h2 class="text-xl md:text-2xl font-black text-white"><?= esc($activeAnak['nama_lengkap']) ?></h2>
                    <p class="text-xs text-blue-100 mt-0.5">
                        Kelas: <?= esc($activeAnak['kelas']['nama_kelas'] ?? 'Belum Ditentukan') ?> &bull; 
                        Program: <?= esc($activeAnak['program'] ?? 'Reguler') ?> &bull; 
                        Tahun Ajaran: <?= esc($tahunAktif) ?>
                    </p>
                </div>
            </div>
            <div class="bg-white/10 backdrop-blur border border-white/20 rounded-xl px-4 py-2.5 text-center shrink-0">
                <span class="text-[11px] text-blue-200 block uppercase font-bold">Total Capaian</span>
                <span class="text-2xl font-extrabold text-white"><?= count($jurnalList) ?></span>
                <span class="text-[10px] text-blue-100 block">Jurnal Terbit</span>
            </div>
        </div>
    </div>

    <!-- Filter Aspek Perkembangan -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-slate-800">
        <div class="flex items-center gap-2 overflow-x-auto pb-1 custom-scrollbar">
            <!-- Filter All -->
            <a 
                href="<?= base_url('wali-murid/perkembangan?anak_id=' . $activeAnakId) ?>" 
                class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all <?= empty($kategoriFilter) ? 'bg-blue-600 text-white shadow-md shadow-blue-500/25 ring-2 ring-blue-500/30' : 'bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 hover:bg-gray-200' ?>"
            >
                <span>Semua Aspek</span>
                <span class="ml-1.5 px-1.5 py-0.5 text-[10px] rounded-full <?= empty($kategoriFilter) ? 'bg-white/25 text-white' : 'bg-gray-200 dark:bg-slate-600 text-gray-700 dark:text-slate-200' ?>">
                    <?= array_sum($aspekCounts) ?>
                </span>
            </a>

            <!-- Each Aspect -->
            <?php foreach($aspekList as $aspek): ?>
                <?php 
                    $isSelected = ($kategoriFilter === $aspek);
                    $count = $aspekCounts[$aspek] ?? 0;
                ?>
                <a 
                    href="<?= base_url('wali-murid/perkembangan?anak_id=' . $activeAnakId . '&kategori=' . urlencode($aspek)) ?>" 
                    class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all <?= $isSelected ? 'bg-blue-600 text-white shadow-md shadow-blue-500/25 ring-2 ring-blue-500/30' : 'bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 hover:bg-gray-200' ?>"
                >
                    <span><?= esc($aspek) ?></span>
                    <span class="ml-1.5 px-1.5 py-0.5 text-[10px] rounded-full <?= $isSelected ? 'bg-white/25 text-white' : 'bg-gray-200 dark:bg-slate-600 text-gray-700 dark:text-slate-200' ?>">
                        <?= $count ?>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Timeline List Catatan Perkembangan -->
    <?php if(empty($jurnalList)): ?>
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-12 text-center border border-gray-100 dark:border-slate-800">
            <div class="w-20 h-20 bg-blue-50 dark:bg-slate-700 rounded-3xl flex items-center justify-center text-blue-500 dark:text-blue-400 mx-auto mb-4 text-4xl">
                <i class="ph ph-notebook"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Belum Ada Catatan Perkembangan</h3>
            <p class="text-sm text-gray-500 dark:text-slate-400 max-w-md mx-auto">
                <?php if(!empty($kategoriFilter)): ?>
                    Belum ada catatan jurnal pada kategori "<strong><?= esc($kategoriFilter) ?></strong>". Silakan pilih kategori lain atau tampilkan seluruh catatan.
                <?php else: ?>
                    Bunda guru belum mengunggah catatan pengamatan untuk ananda pada tahun ajaran ini.
                <?php endif; ?>
            </p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach($jurnalList as $j): ?>
                <?php
                    $kategoriColors = [
                        'Nilai Agama & Moral'   => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                        'Fisik & Motorik'       => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border-amber-200 dark:border-amber-800',
                        'Kognitif'              => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 border-blue-200 dark:border-blue-800',
                        'Bahasa'                => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 border-indigo-200 dark:border-indigo-800',
                        'Sosial & Emosional'    => 'bg-pink-50 text-pink-700 dark:bg-pink-900/30 dark:text-pink-400 border-pink-200 dark:border-pink-800',
                        'Seni & Kreativitas'    => 'bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 border-purple-200 dark:border-purple-800',
                    ];
                    $badgeClass = $kategoriColors[$j['kategori']] ?? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 border-blue-200';
                ?>
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-slate-800 hover:shadow-md transition-all">
                    <!-- Header Kartu -->
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 rounded-lg text-xs font-bold border <?= $badgeClass ?>">
                                <?= esc($j['kategori']) ?>
                            </span>
                            <?php if(!empty($j['nama_kelas'])): ?>
                                <span class="text-xs text-gray-400 font-medium">
                                    Kelas <?= esc($j['nama_kelas']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <span class="text-xs text-gray-400 dark:text-slate-500 flex items-center gap-1.5 font-medium">
                            <i class="ph ph-calendar text-sm"></i>
                            <?= tanggal_indo($j['tanggal'], true) ?>
                        </span>
                    </div>

                    <!-- Judul Catatan -->
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
                        <?= esc($j['judul']) ?>
                    </h3>

                    <!-- Deskripsi Pengamatan -->
                    <div class="text-sm text-gray-600 dark:text-slate-300 leading-relaxed whitespace-pre-line mb-4 font-normal">
                        <?= esc($j['catatan']) ?>
                    </div>

                    <!-- Lampiran Foto Dokumentasi -->
                    <?php if(!empty($j['foto'])): ?>
                        <div class="mb-4">
                            <div 
                                @click="previewSrc = '<?= base_url('uploads/perkembangan/' . $j['foto']) ?>'; previewTitle = '<?= esc(addslashes($j['judul'])) ?>'; previewModal = true;"
                                class="inline-block group relative rounded-2xl overflow-hidden border border-gray-100 dark:border-slate-700 max-w-md cursor-pointer shadow-sm hover:shadow-md transition-all"
                            >
                                <img src="<?= base_url('uploads/perkembangan/' . $j['foto']) ?>" alt="Dokumentasi aktivitas ananda" class="max-h-72 w-auto object-cover rounded-2xl group-hover:scale-105 transition-transform duration-300">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-semibold gap-2">
                                    <i class="ph ph-magnifying-glass-plus text-lg"></i>
                                    <span>Klik untuk Memperbesar Foto</span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Footer Info Guru -->
                    <div class="pt-3 border-t border-gray-100 dark:border-slate-700/60 flex items-center justify-between text-xs text-gray-400 dark:text-slate-500">
                        <span class="flex items-center gap-1.5">
                            <i class="ph ph-chalkboard-teacher text-blue-500 text-sm"></i>
                            Pendidik: <strong class="text-gray-700 dark:text-slate-300"><?= esc($j['sapaan'] ?? '') ?> <?= esc($j['guru_panggilan'] ?: ($j['guru_nama'] ?? 'Bunda Guru')) ?></strong>
                        </span>
                        <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-medium">
                            <i class="ph ph-check-circle"></i> Terverifikasi Sekolah
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Modal Pratinjau Foto Penuh -->
    <div 
        x-show="previewModal" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
        @keydown.escape.window="previewModal = false"
    >
        <div 
            class="relative max-w-4xl max-h-[90vh] bg-white dark:bg-slate-900 rounded-3xl overflow-hidden shadow-2xl p-4 flex flex-col"
            @click.away="previewModal = false"
        >
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-slate-800">
                <h4 class="text-sm font-bold text-gray-900 dark:text-white truncate" x-text="previewTitle"></h4>
                <button @click="previewModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-white p-1 rounded-lg">
                    <i class="ph ph-x text-xl"></i>
                </button>
            </div>
            <div class="p-2 overflow-auto flex-1 flex items-center justify-center">
                <img :src="previewSrc" alt="Preview Foto" class="max-h-[75vh] w-auto object-contain rounded-2xl shadow-md">
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>

