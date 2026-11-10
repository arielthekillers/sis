<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Portal Orang Tua</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Beranda Ananda
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Pantau tumbuh kembang, aktivitas belajar, dan administrasi sekolah ananda secara praktis dan transparan.
<?= $this->endSection() ?>

<?= $this->section('page_actions') ?>
<div class="flex items-center gap-2">
    <?php if(($activeAnak['program'] ?? 'Reguler') === 'Fullday' || $hasFulldayChild): ?>
        <a href="<?= base_url('wali-murid/cctv') ?>" class="bg-rose-600 hover:bg-rose-700 text-white px-3.5 py-2 rounded-xl text-xs md:text-sm font-semibold transition-all shadow-md shadow-rose-500/20 flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
            <i class="ph ph-video-camera text-base"></i>
            <span>Live CCTV</span>
        </a>
    <?php endif; ?>
    <a href="<?= base_url('wali-murid/keuangan') ?>" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-2 rounded-xl text-xs md:text-sm font-semibold transition-all shadow-md shadow-emerald-500/20 flex items-center gap-1.5">
        <i class="ph ph-wallet text-base"></i>
        <span>Tagihan & SPP</span>
    </a>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">

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
                            href="<?= base_url('wali-murid/dashboard?anak_id=' . $a['siswa_id']) ?>" 
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs md:text-sm font-semibold transition-all whitespace-nowrap <?= $isActive ? 'bg-blue-600 text-white shadow-md shadow-blue-500/25 ring-2 ring-blue-500/30' : 'bg-gray-50 dark:bg-slate-700/60 text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700' ?>"
                        >
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold <?= $isActive ? 'bg-white text-blue-600' : 'bg-blue-100 text-blue-700 dark:bg-slate-600 dark:text-slate-200' ?>">
                                <?= strtoupper(substr($a['nama_lengkap'], 0, 1)) ?>
                            </span>
                            <span><?= esc($a['nama_panggilan'] ?: $a['nama_lengkap']) ?></span>
                            <?php if(!empty($a['kelas'])): ?>
                                <span class="text-[10px] opacity-80 font-normal">(<?= esc($a['kelas']['nama_kelas']) ?>)</span>
                            <?php endif; ?>
                            <?php if(($a['program'] ?? 'Reguler') === 'Fullday'): ?>
                                <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-amber-400 text-amber-950">Fullday</span>
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

    <!-- Banner Profil Ananda yang Sedang Aktif Dilihat -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 relative overflow-hidden">
        <!-- Subtle background accent -->
        <div class="absolute -right-8 -top-8 w-48 h-48 bg-gradient-to-br from-blue-500/10 to-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="flex flex-col md:flex-row items-center md:items-start gap-6 relative z-10">
            <!-- Foto Murid -->
            <div class="relative flex-shrink-0">
                <?php if(!empty($activeAnak['foto'])): ?>
                    <img src="<?= base_url('uploads/profiles/' . $activeAnak['foto']) ?>" alt="<?= esc($activeAnak['nama_lengkap']) ?>" class="w-24 h-24 object-cover rounded-2xl border-4 border-gray-50 dark:border-slate-700 shadow-sm">
                <?php else: ?>
                    <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-extrabold text-3xl border-4 border-gray-50 dark:border-slate-700 shadow-sm">
                        <?= strtoupper(substr($activeAnak['nama_lengkap'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <span class="absolute -bottom-1 -right-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold shadow-sm <?= $activeAnak['jenis_kelamin'] === 'L' ? 'bg-sky-500 text-white' : 'bg-pink-500 text-white' ?>">
                    <?= $activeAnak['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?>
                </span>
            </div>

            <!-- Detail Ananda -->
            <div class="flex-1 text-center md:text-left">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-2">
                    <div>
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 dark:text-blue-400">
                            <i class="ph ph-heart-straight text-sm"></i>
                            <?= esc($activeAnak['hubungan'] ? ucfirst($activeAnak['hubungan']) : 'Wali Murid') ?> dari
                        </span>
                        <h2 class="text-2xl font-black text-gray-900 dark:text-white mt-0.5">
                            <?= esc($activeAnak['nama_lengkap']) ?>
                            <?php if(!empty($activeAnak['nama_panggilan'])): ?>
                                <span class="text-base font-normal text-gray-500 dark:text-slate-400">(<?= esc($activeAnak['nama_panggilan']) ?>)</span>
                            <?php endif; ?>
                        </h2>
                    </div>

                    <div class="flex items-center gap-2 justify-center md:justify-end flex-wrap">
                        <?php if(!empty($activeAnak['kelas'])): ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                                <i class="ph ph-chalkboard"></i> Kelas: <?= esc($activeAnak['kelas']['nama_kelas']) ?> (<?= esc($activeAnak['kelas']['tingkat']) ?>)
                            </span>
                        <?php endif; ?>

                        <!-- Program Belajar Badge -->
                        <?php if(($activeAnak['program'] ?? 'Reguler') === 'Fullday'): ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                <i class="ph ph-sun text-sm text-amber-500"></i> Program Fullday
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                                <i class="ph ph-clock text-sm"></i> Program Reguler
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Info Kontak Wali Kelas Ananda -->
                <div class="pt-4 mt-3 border-t border-gray-100 dark:border-slate-700/60 flex flex-wrap items-center justify-between gap-4 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="text-gray-400">Pendidik / Wali Kelas:</span>
                        <?php if(!empty($waliKelasList)): ?>
                            <div class="flex items-center gap-2 flex-wrap">
                                <?php foreach($waliKelasList as $wk): ?>
                                    <span class="font-semibold text-gray-800 dark:text-slate-200 bg-gray-50 dark:bg-slate-700/60 px-2.5 py-1 rounded-lg flex items-center gap-1.5 border border-gray-100 dark:border-slate-700">
                                        <i class="ph ph-user text-blue-500"></i>
                                        <?= esc($wk['sapaan']) ?> <?= esc($wk['nama_lengkap']) ?>
                                        <?php if(!empty($wk['no_hp'])): ?>
                                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $wk['no_hp']) ?>?text=Halo%20<?= urlencode($wk['sapaan'] . ' ' . $wk['nama_lengkap']) ?>,%20saya%20orang%20tua%20dari%20<?= urlencode($activeAnak['nama_lengkap']) ?>" target="_blank" title="Kirim Pesan WhatsApp" class="text-emerald-600 hover:text-emerald-700 ml-1">
                                                <i class="ph ph-whatsapp-logo text-sm"></i>
                                            </a>
                                        <?php endif; ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <span class="text-gray-500 italic">Belum ditentukan</span>
                        <?php endif; ?>
                    </div>

                    <!-- NIS / Nomor Induk -->
                    <div class="flex items-center gap-2">
                        <span class="text-gray-400">NIS:</span>
                        <span class="font-mono font-semibold text-gray-700 dark:text-slate-300"><?= esc($activeAnak['nis'] ?: '-') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4 Quick Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Stat 1: Rombel -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-slate-800 flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl shrink-0">
                <i class="ph ph-chalkboard-simple"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Rombel Aktif</p>
                <p class="text-sm md:text-base font-bold text-gray-900 dark:text-white truncate">
                    <?= esc($activeAnak['kelas']['nama_kelas'] ?? 'Belum Ada') ?>
                </p>
            </div>
        </div>

        <!-- Stat 2: Program Belajar -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-slate-800 flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl shrink-0">
                <i class="ph ph-sun"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Program</p>
                <p class="text-sm md:text-base font-bold text-gray-900 dark:text-white truncate">
                    <?= esc($activeAnak['program'] ?? 'Reguler') ?>
                </p>
            </div>
        </div>

        <!-- Stat 3: Catatan Jurnal -->
        <a href="<?= base_url('wali-murid/perkembangan?anak_id=' . $activeAnakId) ?>" class="bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-slate-800 flex items-center gap-3 hover:border-blue-300 transition-colors group">
            <div class="w-11 h-11 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl shrink-0 group-hover:scale-105 transition-transform">
                <i class="ph ph-notebook"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Jurnal Terbit</p>
                <p class="text-sm md:text-base font-bold text-gray-900 dark:text-white truncate">
                    <?= (int)$totalCatatan ?> Catatan
                </p>
            </div>
        </a>

        <!-- Stat 4: Tagihan SPP -->
        <a href="<?= base_url('wali-murid/keuangan?anak_id=' . $activeAnakId) ?>" class="bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-slate-800 flex items-center gap-3 hover:border-emerald-300 transition-colors group">
            <div class="w-11 h-11 rounded-xl <?= ($activeAnak['sisa_tagihan'] ?? 0) <= 0 ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : 'bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400' ?> flex items-center justify-center text-xl shrink-0 group-hover:scale-105 transition-transform">
                <i class="ph <?= ($activeAnak['sisa_tagihan'] ?? 0) <= 0 ? 'ph-check-circle' : 'ph-receipt' ?>"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Kewajiban Tagihan</p>
                <p class="text-sm md:text-base font-bold <?= ($activeAnak['sisa_tagihan'] ?? 0) <= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' ?> truncate">
                    <?= ($activeAnak['sisa_tagihan'] ?? 0) <= 0 ? 'Semua Lunas' : rupiah($activeAnak['sisa_tagihan']) ?>
                </p>
            </div>
        </a>
    </div>

    <!-- Quick Banner: CCTV Online (Hanya jika Fullday) -->
    <?php if(($activeAnak['program'] ?? 'Reguler') === 'Fullday'): ?>
        <div class="bg-gradient-to-r from-rose-600 via-pink-600 to-rose-700 text-white rounded-2xl p-5 shadow-lg shadow-rose-500/20 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4 text-center sm:text-left">
                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center text-2xl shrink-0">
                    <i class="ph ph-video-camera"></i>
                </div>
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/25 text-white mb-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                        LIVE STREAMING TERSEDIA
                    </div>
                    <h3 class="text-base font-extrabold text-white">Monitoring CCTV Online Kelas Fullday</h3>
                    <p class="text-xs text-rose-100 max-w-xl">
                        Pantau langsung aktivitas belajar, bermain di playground, hingga waktu makan siang ananda di sekolah secara real-time.
                    </p>
                </div>
            </div>
            <a href="<?= base_url('wali-murid/cctv') ?>" class="bg-white text-rose-600 hover:bg-rose-50 px-4 py-2.5 rounded-xl text-xs font-bold transition-all shadow-md shrink-0 flex items-center gap-2 whitespace-nowrap">
                <span>Buka Ruang CCTV</span>
                <i class="ph ph-arrow-right font-bold"></i>
            </a>
        </div>
    <?php endif; ?>

    <!-- Layout 2 Kolom: Jurnal Perkembangan & Tagihan -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kolom Kiri (2 Span): Timeline Perkembangan Ananda -->
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="ph ph-notebook text-blue-600 dark:text-blue-400 text-xl"></i>
                    <span>Catatan Perkembangan Terbaru</span>
                </h3>
                <a href="<?= base_url('wali-murid/perkembangan?anak_id=' . $activeAnakId) ?>" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                    <span>Lihat Semua Jurnal</span>
                    <i class="ph ph-arrow-right"></i>
                </a>
            </div>

            <?php if(empty($perkembangan)): ?>
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-10 text-center border border-gray-100 dark:border-slate-800">
                    <div class="w-16 h-16 bg-blue-50 dark:bg-slate-700 rounded-2xl flex items-center justify-center text-blue-500 dark:text-blue-400 mx-auto mb-3 text-3xl">
                        <i class="ph ph-hourglass-medium"></i>
                    </div>
                    <h4 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Belum Ada Catatan Jurnal</h4>
                    <p class="text-sm text-gray-500 dark:text-slate-400 max-w-sm mx-auto">
                        Pendidik/wali kelas belum mempublikasikan catatan pengamatan perkembangan untuk ananda saat ini.
                    </p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach($perkembangan as $p): ?>
                        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-slate-800 hover:shadow-md transition-all">
                            <div class="flex items-center justify-between gap-3 mb-3">
                                <?php
                                    $kategoriColors = [
                                        'Nilai Agama & Moral'   => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                                        'Fisik & Motorik'       => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border-amber-200 dark:border-amber-800',
                                        'Kognitif'              => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 border-blue-200 dark:border-blue-800',
                                        'Bahasa'                => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 border-indigo-200 dark:border-indigo-800',
                                        'Sosial & Emosional'    => 'bg-pink-50 text-pink-700 dark:bg-pink-900/30 dark:text-pink-400 border-pink-200 dark:border-pink-800',
                                        'Seni & Kreativitas'    => 'bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 border-purple-200 dark:border-purple-800',
                                    ];
                                    $badgeClass = $kategoriColors[$p['kategori']] ?? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 border-blue-200';
                                ?>
                                <span class="px-2.5 py-1 rounded-lg text-xs font-semibold border <?= $badgeClass ?>">
                                    <?= esc($p['kategori']) ?>
                                </span>
                                <span class="text-xs text-gray-400 dark:text-slate-500 flex items-center gap-1 font-medium">
                                    <i class="ph ph-calendar text-sm"></i> <?= tanggal_indo($p['tanggal'], true) ?>
                                </span>
                            </div>

                            <h4 class="text-base font-bold text-gray-900 dark:text-white mb-2">
                                <?= esc($p['judul']) ?>
                            </h4>

                            <div class="text-sm text-gray-600 dark:text-slate-300 leading-relaxed whitespace-pre-line mb-4 font-normal">
                                <?= esc($p['catatan']) ?>
                            </div>

                            <!-- Foto Kegiatan Jika Ada -->
                            <?php if(!empty($p['foto'])): ?>
                                <div class="mb-4">
                                    <a href="<?= base_url('uploads/perkembangan/' . $p['foto']) ?>" target="_blank" class="inline-block group relative rounded-xl overflow-hidden border border-gray-100 dark:border-slate-700 max-w-sm">
                                        <img src="<?= base_url('uploads/perkembangan/' . $p['foto']) ?>" alt="Foto kegiatan ananda" class="max-h-56 w-auto object-cover rounded-xl group-hover:scale-105 transition-transform duration-300">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-semibold gap-1.5">
                                            <i class="ph ph-magnifying-glass-plus text-base"></i> Buka Foto Penuh
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="pt-3 border-t border-gray-100 dark:border-slate-700/60 flex items-center justify-between text-xs text-gray-400 dark:text-slate-500">
                                <span class="flex items-center gap-1.5">
                                    <i class="ph ph-chalkboard-teacher text-blue-500"></i>
                                    Pendidik: <strong class="text-gray-700 dark:text-slate-300"><?= esc($p['sapaan'] ?? '') ?> <?= esc($p['guru_panggilan'] ?: ($p['guru_nama'] ?? 'Bunda Guru')) ?></strong>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Kolom Kanan (1 Span): Ringkasan Administrasi & Tagihan -->
        <div class="space-y-6">
            <!-- Card Tagihan Berjalan Ananda -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="ph ph-receipt text-emerald-600 dark:text-emerald-400 text-lg"></i>
                        Tagihan Berjalan
                    </h3>
                    <a href="<?= base_url('wali-murid/keuangan?anak_id=' . $activeAnakId) ?>" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                        Lihat Semua
                    </a>
                </div>

                <?php if(empty($tagihanAktif)): ?>
                    <div class="text-center py-6 bg-emerald-50/50 dark:bg-emerald-950/20 rounded-xl border border-emerald-100 dark:border-emerald-900/30 p-4">
                        <i class="ph ph-check-circle text-3xl text-emerald-600 dark:text-emerald-400 mx-auto mb-1"></i>
                        <p class="text-xs font-bold text-emerald-800 dark:text-emerald-300">Semua Tagihan Sudah Lunas</p>
                        <p class="text-[11px] text-emerald-600 dark:text-emerald-400 mt-0.5">Terima kasih atas partisipasi dan kerjasamanya!</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-3 mb-4">
                        <?php foreach($tagihanAktif as $t): ?>
                            <?php $sisa = $t['nominal_tagihan'] - $t['nominal_dibayar']; ?>
                            <div class="p-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl flex items-center justify-between text-xs">
                                <div>
                                    <p class="font-bold text-gray-900 dark:text-white"><?= esc($t['nama_pos']) ?></p>
                                    <p class="text-[10px] text-gray-400">Jatuh Tempo: <?= tanggal_indo($t['jatuh_tempo']) ?></p>
                                </div>
                                <div class="text-right">
                                    <span class="font-extrabold text-gray-900 dark:text-white"><?= rupiah($sisa) ?></span>
                                    <span class="block text-[10px] font-semibold text-amber-600 dark:text-amber-400"><?= esc($t['status']) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if($isXenditEnabled): ?>
                        <a href="<?= base_url('wali-murid/keuangan?anak_id=' . $activeAnakId) ?>" class="w-full inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition-all shadow-md shadow-indigo-500/20 mb-2">
                            <i class="ph ph-lightning text-sm text-amber-300"></i>
                            <span>Bayar Online via Xendit</span>
                        </a>
                    <?php endif; ?>
                    <a href="<?= base_url('wali-murid/keuangan?anak_id=' . $activeAnakId) ?>" class="w-full inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2.5 rounded-xl text-xs transition-all shadow-md shadow-emerald-500/20">
                        <i class="ph ph-credit-card"></i>
                        <span>Cara Bayar & Rekening Sekolah</span>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Card Total Kewajiban Keluarga (Jika lebih dari 1 anak) -->
            <?php if(count($anakList) > 1): ?>
                <div class="bg-gradient-to-br from-indigo-900 to-slate-900 text-white rounded-2xl p-6 shadow-md">
                    <span class="text-[11px] uppercase tracking-wider font-bold text-indigo-300 block mb-1">
                        Ringkasan Keluarga
                    </span>
                    <h4 class="text-sm font-medium text-slate-300 mb-3">Total Kewajiban Seluruh Anak</h4>
                    <p class="text-2xl font-black text-white mb-4">
                        <?= rupiah($totalTagihanKeluarga) ?>
                    </p>
                    <div class="space-y-2 pt-3 border-t border-white/10 text-xs">
                        <?php foreach($anakList as $a): ?>
                            <div class="flex items-center justify-between text-slate-300">
                                <span><?= esc($a['nama_panggilan'] ?: $a['nama_lengkap']) ?> (<?= esc($a['kelas']['nama_kelas'] ?? '-') ?>):</span>
                                <span class="font-bold text-white"><?= rupiah($a['sisa_tagihan']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
