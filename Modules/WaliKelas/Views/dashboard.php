<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Beranda Guru</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Beranda Guru & Wali Kelas
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Selamat datang, <?= esc($guru ? ($guru['sapaan'] . ' ' . $guru['nama_lengkap']) : 'Bapak/Ibu Pendidik') ?>. Pantau rombel binaan dan catat perkembangan ananda tercinta.
<?= $this->endSection() ?>

<?= $this->section('page_actions') ?>
<div class="flex items-center gap-2">
    <a href="<?= base_url('wali-kelas/perkembangan') ?>" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-500/25 transition-all">
        <i class="ph ph-plus-circle text-base"></i>
        <span>Catat Jurnal Baru</span>
    </a>
    <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
        <i class="ph ph-calendar-check text-sm"></i>
        TA: <?= esc($tahun_aktif) ?>
    </span>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <!-- Banner Sambutan Interaktif -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-blue-600 via-indigo-600 to-sky-600 text-white p-6 md:p-8 shadow-xl shadow-blue-500/10">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold mb-3">
                <i class="ph ph-sparkle text-amber-300"></i> Ruang Pendidik & Wali Kelas
            </span>
            <h2 class="text-2xl md:text-3xl font-black tracking-tight mb-2">
                Halo, <?= esc($guru ? ($guru['sapaan'] . ' ' . $guru['nama_lengkap']) : 'Pendidik Hebat') ?>! 👋
            </h2>
            <p class="text-blue-100 text-xs md:text-sm leading-relaxed mb-4">
                Setiap perkembangan ananda adalah langkah berharga bagi masa depannya. Anda dapat mengelola data murid binaan, memantau catatan perkembangan, dan memperbarui profil Anda dengan mudah di sini.
            </p>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="<?= base_url('wali-kelas/murid') ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-white text-blue-700 hover:bg-blue-50 shadow-md transition-all">
                    <i class="ph ph-student text-base"></i>
                    <span>Daftar Murid (<?= $totalMurid ?> Siswa)</span>
                </a>
                <a href="<?= base_url('wali-kelas/perkembangan') ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-white/10 hover:bg-white/20 text-white border border-white/20 transition-all backdrop-blur-sm">
                    <i class="ph ph-notebook text-base"></i>
                    <span>Kelola Jurnal Perkembangan</span>
                </a>
            </div>
        </div>
        <div class="absolute -right-6 -bottom-8 opacity-15 pointer-events-none text-white text-9xl">
            <i class="ph ph-chalkboard-teacher"></i>
        </div>
    </div>

    <!-- Ringkasan Statistik Kartu (4 Metrik Utama) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Murid -->
        <a href="<?= base_url('wali-kelas/murid') ?>" class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 dark:text-slate-400">Total Murid Diampu</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                    <i class="ph ph-student"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <h3 class="text-2xl font-black text-gray-900 dark:text-white"><?= $totalMurid ?></h3>
                <span class="text-xs text-gray-400">Ananda</span>
            </div>
            <p class="text-[11px] text-blue-600 dark:text-blue-400 mt-2 font-semibold flex items-center gap-1">
                <span>Lihat seluruh data murid</span>
                <i class="ph ph-arrow-right text-xs group-hover:translate-x-0.5 transition-transform"></i>
            </p>
        </a>

        <!-- Card 2: Rombel Binaan -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-gray-100 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 dark:text-slate-400">Rombel Binaan</span>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl">
                    <i class="ph ph-chalkboard"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <h3 class="text-2xl font-black text-gray-900 dark:text-white"><?= count($kelasBinaan) ?></h3>
                <span class="text-xs text-gray-400">Kelas Aktif</span>
            </div>
            <p class="text-[11px] text-gray-400 mt-2">
                Tahun Ajaran <?= esc($tahun_aktif) ?>
            </p>
        </div>

        <!-- Card 3: Total Jurnal Perkembangan -->
        <a href="<?= base_url('wali-kelas/perkembangan') ?>" class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 dark:text-slate-400">Jurnal Tercatat</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                    <i class="ph ph-notebook"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <h3 class="text-2xl font-black text-gray-900 dark:text-white"><?= $totalCatatan ?></h3>
                <span class="text-xs text-gray-400">Catatan</span>
            </div>
            <p class="text-[11px] text-emerald-600 dark:text-emerald-400 mt-2 font-semibold flex items-center gap-1">
                <span>Kelola jurnal perkembangan</span>
                <i class="ph ph-arrow-right text-xs group-hover:translate-x-0.5 transition-transform"></i>
            </p>
        </a>

        <!-- Card 4: Jurnal Bulan Ini -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-gray-100 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 dark:text-slate-400">Jurnal Bulan Ini</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl">
                    <i class="ph ph-calendar-plus"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <h3 class="text-2xl font-black text-gray-900 dark:text-white"><?= $catatanBulanIni ?></h3>
                <span class="text-xs text-gray-400">Entri Baru</span>
            </div>
            <p class="text-[11px] text-gray-400 mt-2">
                Bulan <?= date('F Y') ?>
            </p>
        </div>
    </div>

    <!-- Layout Dua Kolom: Kelas Binaan & Jurnal Terkini -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kolom Kiri (2 Kolom): Rombongan Belajar yang Diampu -->
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="ph ph-chalkboard-teacher text-blue-600 dark:text-blue-400 text-lg"></i>
                        Rombongan Belajar Binaan
                    </h3>
                    <p class="text-xs text-gray-400">Daftar kelas yang berada di bawah tanggung jawab Anda.</p>
                </div>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                    <?= count($kelasBinaan) ?> Kelas
                </span>
            </div>

            <?php if(empty($kelasBinaan)): ?>
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 text-center border border-gray-100 dark:border-slate-800">
                    <i class="ph ph-chalkboard text-4xl text-gray-300 dark:text-slate-600 mx-auto mb-2 block"></i>
                    <h4 class="text-sm font-bold text-gray-700 dark:text-slate-300">Belum Ada Kelas yang Diampu</h4>
                    <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto">Anda belum ditugaskan sebagai wali kelas di rombongan belajar aktif. Silakan hubungi bagian Akademik.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <?php foreach($kelasBinaan as $k): ?>
                        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-gray-100 dark:border-slate-800 shadow-sm flex flex-col justify-between hover:border-blue-200 dark:hover:border-blue-800 transition-all">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="px-2.5 py-0.5 rounded-lg text-xs font-black bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                        <?= esc($k['tingkat']) ?>
                                    </span>
                                    <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                        <i class="ph ph-check-circle"></i> Aktif
                                    </span>
                                </div>

                                <h4 class="text-lg font-black text-gray-900 dark:text-white mb-1">
                                    <?= esc($k['nama_rombel']) ?>
                                </h4>
                                <p class="text-xs text-gray-400 mb-4">Tahun Ajaran <?= esc($k['tahun_ajaran']) ?></p>

                                <!-- Statistik Gender Murid -->
                                <div class="grid grid-cols-3 gap-2 bg-gray-50 dark:bg-slate-700/50 rounded-xl p-3 text-center mb-4 text-xs">
                                    <div>
                                        <span class="text-gray-400 block text-[10px]">Total</span>
                                        <strong class="text-gray-900 dark:text-white text-sm font-bold"><?= $k['total_siswa'] ?></strong>
                                    </div>
                                    <div>
                                        <span class="text-blue-500 block text-[10px]">Laki-laki</span>
                                        <strong class="text-blue-700 dark:text-blue-400 text-sm font-bold"><?= $k['total_l'] ?></strong>
                                    </div>
                                    <div>
                                        <span class="text-pink-500 block text-[10px]">Perempuan</span>
                                        <strong class="text-pink-700 dark:text-pink-400 text-sm font-bold"><?= $k['total_p'] ?></strong>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 pt-2 border-t border-gray-100 dark:border-slate-700/60">
                                <a 
                                    href="<?= base_url('wali-kelas/murid?rombel_id=' . $k['id']) ?>" 
                                    class="flex-1 py-2 px-3 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white text-center shadow-sm transition-all flex items-center justify-center gap-1.5"
                                >
                                    <i class="ph ph-users"></i>
                                    <span>Lihat Murid</span>
                                </a>
                                <a 
                                    href="<?= base_url('wali-kelas/perkembangan?rombel_id=' . $k['id']) ?>" 
                                    class="py-2 px-3 rounded-xl text-xs font-bold bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 text-gray-700 dark:text-slate-300 transition-all flex items-center gap-1"
                                    title="Lihat Jurnal Kelas Ini"
                                >
                                    <i class="ph ph-notebook text-base"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Kolom Kanan (1 Kolom): Jurnal Perkembangan Terbaru -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="ph ph-clock-counter-clockwise text-emerald-600 dark:text-emerald-400 text-lg"></i>
                        Jurnal Terkini
                    </h3>
                    <p class="text-xs text-gray-400">Catatan terbaru yang Anda simpan.</p>
                </div>
                <a href="<?= base_url('wali-kelas/perkembangan') ?>" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                    Lihat Semua
                </a>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-gray-100 dark:border-slate-800 shadow-sm space-y-4">
                <?php if(empty($recentCatatan)): ?>
                    <div class="py-8 text-center text-gray-400 text-xs">
                        <i class="ph ph-notebook text-3xl mb-1 block"></i>
                        Belum ada catatan jurnal perkembangan yang dicatat.
                        <div class="mt-3">
                            <a href="<?= base_url('wali-kelas/perkembangan') ?>" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                <i class="ph ph-plus"></i> Buat Catatan Pertama
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="divide-y divide-gray-100 dark:divide-slate-700/60">
                        <?php foreach($recentCatatan as $rc): ?>
                            <div class="py-3 first:pt-0 last:pb-0">
                                <div class="flex items-start justify-between gap-2 mb-1">
                                    <a href="<?= base_url('wali-kelas/murid/profil/' . $rc['siswa_id']) ?>" class="font-bold text-xs text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        <?= esc($rc['nama_lengkap']) ?>
                                    </a>
                                    <span class="text-[10px] text-gray-400 shrink-0"><?= tanggal_indo($rc['tanggal']) ?></span>
                                </div>
                                <div class="flex items-center gap-1.5 mb-1.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                                        <?= esc($rc['kategori']) ?>
                                    </span>
                                    <span class="text-[10px] text-gray-400">Kelas: <?= esc($rc['nama_rombel'] ?? '-') ?></span>
                                </div>
                                <h5 class="text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    <?= esc($rc['judul']) ?>
                                </h5>
                                <p class="text-[11px] text-gray-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
                                    <?= esc(strip_tags($rc['catatan'])) ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

</div>
<?= $this->endSection() ?>
