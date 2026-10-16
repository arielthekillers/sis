<?= $this->extend('layout/main') ?>

<!-- 1. Breadcrumb -->
<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><a href="<?= base_url('keuangan/daftar-tagihan') ?>" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Keuangan</a></li>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Status SPP Siswa</span></li>
<?= $this->endSection() ?>

<!-- 2. Judul Utama -->
<?= $this->section('page_title') ?>
Pengecekan Status SPP Siswa
<?= $this->endSection() ?>

<!-- 3. Sub Judul -->
<?= $this->section('page_subtitle') ?>
Cek cepat status pembayaran siswa saat orang tua/wali murid menanyakan keamanan SPP ananda.
<?= $this->endSection() ?>

<!-- 4. Tombol Aksi Kanan Atas -->
<?= $this->section('page_actions') ?>
<?php if (!empty($siswa)): ?>
<div class="flex items-center gap-2">
    <?php if (!$isAman): ?>
    <a href="<?= base_url('keuangan/kasir/bayar/' . $siswa['id']) ?>" 
       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-md shadow-blue-600/20 transition-all">
        <i class="ph ph-credit-card text-base"></i>
        <span>Bayar di Kasir</span>
    </a>
    <?php endif; ?>

    <?php if (!empty($waOrtuLink)): ?>
    <a href="<?= $waOrtuLink ?>" target="_blank" rel="noopener noreferrer"
       class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-2 rounded-xl text-xs font-bold shadow-md shadow-emerald-600/20 transition-all">
        <i class="ph ph-whatsapp-logo text-base"></i>
        <span>Kirim WA ke Ortu</span>
    </a>
    <?php endif; ?>

    <a href="<?= base_url('spp') ?>?nis=<?= urlencode($siswa['nis'] ?? '') ?>" target="_blank"
       class="inline-flex items-center gap-2 bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 text-gray-700 dark:text-slate-300 px-3.5 py-2 rounded-xl text-xs font-semibold transition-all">
        <i class="ph ph-arrow-square-out text-base"></i>
        <span>Tampilan Ortu</span>
    </a>
</div>
<?php endif; ?>
<?= $this->endSection() ?>

<!-- 5. Konten Utama -->
<?= $this->section('content') ?>
<div class="space-y-6">

    <!-- Search & Quick Student Selector Bar -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <label for="siswa_select" class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400 mb-1.5">
                    <i class="ph ph-magnifying-glass text-sm mr-1"></i> Pilih atau Cari Siswa:
                </label>
                <p class="text-xs text-gray-400 dark:text-slate-500">Pilih dari daftar siswa untuk melihat status pembayaran dan riwayat 12 bulan SPP.</p>
            </div>

            <!-- Student Dropdown / Quick Switch -->
            <div class="w-full md:w-96">
                <select id="siswa_select" 
                        onchange="if(this.value) window.location.href='<?= base_url('keuangan/status-spp') ?>/' + this.value"
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all cursor-pointer">
                    <?php foreach ($allSiswa as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= $s['id'] == $selectedSiswaId ? 'selected' : '' ?>>
                        <?= esc($s['nama_lengkap']) ?> (NIS: <?= esc($s['nis'] ?: '-') ?>) - <?= esc($s['nama_kelas'] ?: 'Belum Masuk Kelas') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <?php if (empty($siswa)): ?>
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-12 text-center text-gray-400 border border-gray-100 dark:border-slate-800">
        <i class="ph ph-user-circle text-5xl mb-2"></i>
        <p class="text-sm font-medium">Pilih siswa terlebih dahulu untuk memeriksa status SPP.</p>
    </div>
    <?php else: ?>

    <!-- STATUS AMAN / BELUM AMAN HERO BANNER -->
    <?php if ($isAman): ?>
    <div class="bg-gradient-to-r from-emerald-500/15 via-emerald-500/10 to-transparent dark:from-emerald-950/40 dark:via-emerald-950/20 border-2 border-emerald-500/30 rounded-3xl p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm animate-fade-in">
        <div class="flex items-start sm:items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-emerald-500 text-white flex items-center justify-center text-3xl shadow-lg shadow-emerald-500/30 shrink-0">
                <i class="ph ph-shield-check"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300 uppercase tracking-wider">
                        <i class="ph ph-check-circle"></i> AMAN 100%
                    </span>
                    <span class="text-xs text-emerald-700 dark:text-emerald-400 font-semibold">Bebas Tunggakan</span>
                </div>
                <h3 class="text-xl sm:text-2xl font-black text-emerald-900 dark:text-emerald-300 mt-1">
                    Pembayaran SPP Sudah Aman!
                </h3>
                <p class="text-xs sm:text-sm text-emerald-800/90 dark:text-emerald-400/90 mt-0.5">
                    Seluruh kewajiban pembayaran ananda <strong class="font-bold underline"><?= esc($siswa['nama_lengkap']) ?></strong> telah lunas. Tidak ada tagihan yang tertunda.
                </p>
            </div>
        </div>

        <div class="sm:text-right shrink-0 pt-2 sm:pt-0">
            <span class="text-xs text-emerald-700 dark:text-emerald-400 block font-semibold uppercase tracking-wider">Sisa Kewajiban:</span>
            <span class="text-2xl sm:text-3xl font-black text-emerald-700 dark:text-emerald-400 font-mono">Rp 0</span>
        </div>
    </div>
    <?php else: ?>
    <div class="bg-gradient-to-r from-rose-500/15 via-rose-500/10 to-transparent dark:from-rose-950/40 dark:via-rose-950/20 border-2 border-rose-500/30 rounded-3xl p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm animate-fade-in">
        <div class="flex items-start sm:items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-rose-600 text-white flex items-center justify-center text-3xl shadow-lg shadow-rose-600/30 shrink-0">
                <i class="ph ph-warning-octagon"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-black bg-rose-100 text-rose-800 dark:bg-rose-900/60 dark:text-rose-300 uppercase tracking-wider">
                        <i class="ph ph-warning"></i> BELUM AMAN
                    </span>
                    <span class="text-xs text-rose-700 dark:text-rose-400 font-semibold"><?= $stats['countBelumBayar'] + $stats['countMencicil'] ?> Tagihan Tertunda</span>
                </div>
                <h3 class="text-xl sm:text-2xl font-black text-rose-900 dark:text-rose-300 mt-1">
                    Terdapat Tunggakan Pembayaran SPP
                </h3>
                <p class="text-xs sm:text-sm text-rose-800/90 dark:text-rose-400/90 mt-0.5">
                    Ananda <strong class="font-bold underline"><?= esc($siswa['nama_lengkap']) ?></strong> masih memiliki kewajiban pembayaran yang belum diselesaikan.
                </p>
            </div>
        </div>

        <div class="sm:text-right shrink-0 pt-2 sm:pt-0">
            <span class="text-xs text-rose-700 dark:text-rose-400 block font-semibold uppercase tracking-wider">Total Sisa Tunggakan:</span>
            <span class="text-2xl sm:text-3xl font-black text-rose-600 dark:text-rose-400 font-mono"><?= rupiah($stats['totalSisa']) ?></span>
            <div class="mt-2">
                <a href="<?= base_url('keuangan/kasir/bayar/' . $siswa['id']) ?>" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-md transition-all">
                    <i class="ph ph-credit-card"></i> Proses Bayar Sekarang
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Student Detail & Metrics Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Student Info Card -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 flex flex-col justify-between">
            <div class="flex items-center gap-4 pb-4 border-b border-gray-100 dark:border-slate-700/60">
                <?php if (!empty($siswa['foto'])): ?>
                    <img src="<?= base_url('uploads/profiles/' . $siswa['foto']) ?>" alt="Foto" class="w-16 h-16 rounded-2xl object-cover border-2 border-gray-100 dark:border-slate-700 shadow-sm shrink-0">
                <?php else: ?>
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center text-2xl font-black shadow-md shrink-0">
                        <?= strtoupper(substr($siswa['nama_lengkap'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <div>
                    <h4 class="text-base font-extrabold text-gray-900 dark:text-white leading-snug"><?= esc($siswa['nama_lengkap']) ?></h4>
                    <p class="text-xs text-blue-600 dark:text-blue-400 font-bold mt-0.5">Kelas: <?= esc($rombel) ?></p>
                    <p class="text-xs text-gray-400 font-mono mt-0.5">NIS: <?= esc($siswa['nis'] ?: '-') ?> · NISN: <?= esc($siswa['nisn'] ?: '-') ?></p>
                </div>
            </div>

            <div class="pt-4 space-y-2.5 text-xs">
                <div class="flex items-center justify-between">
                    <span class="text-gray-500 dark:text-slate-400">Nama Orang Tua:</span>
                    <span class="font-semibold text-gray-800 dark:text-slate-200"><?= esc($siswa['nama_ayah'] ?: ($siswa['nama_ibu'] ?: '-')) ?></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500 dark:text-slate-400">No. WhatsApp Ortu:</span>
                    <span class="font-semibold font-mono text-gray-800 dark:text-slate-200"><?= esc($siswa['no_hp_ortu'] ?: '-') ?></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500 dark:text-slate-400">Status Siswa:</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-50 text-green-700 dark:bg-green-950/40 dark:text-green-400 border border-green-200 dark:border-green-800">
                        <?= esc($siswa['status_siswa'] ?? 'Aktif') ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Right: 3 Financial Metrics -->
        <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Total SPP -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-5 flex flex-col justify-between">
                <div>
                    <span class="text-[11px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Total Tagihan SPP</span>
                    <h5 class="text-2xl font-black text-gray-900 dark:text-white mt-1"><?= rupiah($stats['totalNominal']) ?></h5>
                </div>
                <p class="text-xs text-gray-400 mt-3"><?= count($tagihanList) ?> pos tagihan diterbitkan</p>
            </div>

            <!-- Total Terbayar -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-5 flex flex-col justify-between">
                <div>
                    <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">SPP Terbayar</span>
                    <h5 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1"><?= rupiah($stats['totalTerbayar']) ?></h5>
                </div>
                <p class="text-xs text-gray-400 mt-3"><?= $stats['countLunas'] ?> pos tagihan lunas</p>
            </div>

            <!-- Sisa Tunggakan -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border <?= $stats['totalSisa'] > 0 ? 'border-rose-200 dark:border-rose-900/40 bg-rose-50/10' : 'border-emerald-200 dark:border-emerald-900/40 bg-emerald-50/10' ?> p-5 flex flex-col justify-between">
                <div>
                    <span class="text-[11px] font-bold <?= $stats['totalSisa'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' ?> uppercase tracking-wider">Sisa Tunggakan</span>
                    <h5 class="text-2xl font-black <?= $stats['totalSisa'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' ?> mt-1"><?= rupiah($stats['totalSisa']) ?></h5>
                </div>
                <p class="text-xs <?= $stats['totalSisa'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' ?> font-semibold mt-3">
                    <?= $stats['totalSisa'] == 0 ? '✓ Seluruh SPP Lunas' : ($stats['countBelumBayar'] + $stats['countMencicil']) . ' tagihan belum tuntas' ?>
                </p>
            </div>
        </div>

    </div>

    <!-- TABEL 12 BULAN (JANUARI - DESEMBER) -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
        
        <!-- Header Tabel & Selector 3 Tahun -->
        <div class="p-5 sm:p-6 border-b border-gray-100 dark:border-slate-700/60 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400 flex items-center justify-center text-lg">
                        <i class="ph ph-calendar"></i>
                    </div>
                    <h4 class="text-base font-extrabold text-gray-900 dark:text-white">
                        Tabel SPP Tahun <?= esc($selectedYear) ?> (Januari – Desember)
                    </h4>
                </div>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Daftar kewajiban SPP per bulan beserta tanggal bayar dan keterangan lunas.</p>
            </div>

            <!-- 3 Year Selector Tabs (-1, Tahun Ini, +1) -->
            <div class="flex items-center gap-1.5 p-1 bg-gray-100 dark:bg-slate-700/60 rounded-xl border border-gray-200/60 dark:border-slate-600/60 shrink-0">
                <?php foreach ($availableYears as $yr): ?>
                <a href="<?= base_url('keuangan/status-spp/' . $siswa['id']) ?>?tahun=<?= $yr ?>"
                   class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 <?= $yr == $selectedYear ? 'bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white' ?>">
                    <span><?= $yr ?></span>
                    <?php if ($yr == date('Y')): ?>
                    <span class="text-[10px] px-1.5 py-0.5 rounded-md font-semibold <?= $yr == $selectedYear ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/50 dark:text-blue-300' : 'bg-gray-200/80 text-gray-600 dark:bg-slate-600 dark:text-slate-300' ?>">Tahun Ini</span>
                    <?php endif; ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Ringkasan Cepat Tahun Terpilih -->
        <div class="bg-gray-50/70 dark:bg-slate-900/40 px-5 sm:px-6 py-2.5 border-b border-gray-100 dark:border-slate-700/60 flex flex-wrap items-center gap-4 text-xs font-medium">
            <span class="text-gray-500 dark:text-slate-400">Rekap Tahun <?= esc($selectedYear) ?>:</span>
            <span class="inline-flex items-center gap-1.5 text-emerald-700 dark:text-emerald-400 font-bold">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <?= $matriksSummary['countLunas'] ?> Bulan Lunas
            </span>
            <?php if ($matriksSummary['countBelumLunas'] > 0): ?>
            <span class="inline-flex items-center gap-1.5 text-rose-600 dark:text-rose-400 font-bold">
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                <?= $matriksSummary['countBelumLunas'] ?> Belum Lunas
            </span>
            <?php endif; ?>
            <?php if ($matriksSummary['countMencicil'] > 0): ?>
            <span class="inline-flex items-center gap-1.5 text-amber-600 dark:text-amber-400 font-bold">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                <?= $matriksSummary['countMencicil'] ?> Mencicil
            </span>
            <?php endif; ?>
            <span class="inline-flex items-center gap-1.5 text-gray-400 dark:text-slate-500">
                <span class="w-2 h-2 rounded-full bg-gray-300 dark:bg-slate-600"></span>
                <?= $matriksSummary['countBelumTerbit'] ?> Belum Terbit
            </span>
        </div>

        <!-- The Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse min-w-[620px]">
                <thead>
                    <tr class="bg-gray-50 dark:bg-slate-900/60 border-b border-gray-100 dark:border-slate-800 text-[11px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-4 w-12 text-center">#</th>
                        <th class="py-3 px-4 w-32">Bulan</th>
                        <th class="py-3 px-4">Rincian SPP</th>
                        <th class="py-3 px-4 text-right">Nominal</th>
                        <th class="py-3 px-4 text-right">Terbayar</th>
                        <th class="py-3 px-4 text-right">Sisa SPP</th>
                        <th class="py-3 px-4 text-center w-36">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    <?php foreach ($matriksBulan as $m): ?>
                    <tr class="hover:bg-gray-50/70 dark:hover:bg-slate-700/40 transition-colors <?= $m['status'] === 'Belum Lunas' ? 'bg-rose-50/20 dark:bg-rose-950/10' : ($m['status'] === 'Lunas' ? 'bg-emerald-50/10 dark:bg-emerald-950/5' : '') ?>">
                        <!-- Nomor -->
                        <td class="py-3 px-4 text-center font-mono text-xs text-gray-400 font-semibold">
                            <?= sprintf('%02d', $m['nomor']) ?>
                        </td>

                        <!-- Bulan -->
                        <td class="py-3 px-4 font-bold text-gray-900 dark:text-white whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <i class="ph ph-calendar-blank text-gray-400 text-sm"></i>
                                <span><?= esc($m['nama_bulan']) ?></span>
                            </div>
                        </td>

                        <!-- Rincian -->
                        <td class="py-3 px-4 text-xs text-gray-600 dark:text-slate-300">
                            <?php if ($m['ada_tagihan']): ?>
                                <div class="space-y-1">
                                    <?php foreach ($m['items'] as $item): ?>
                                    <div class="font-semibold text-gray-800 dark:text-slate-200">
                                        <?= esc($item['judul_tagihan']) ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <span class="text-gray-400 italic text-[11px]">- Belum ada tagihan -</span>
                            <?php endif; ?>
                        </td>

                        <!-- Nominal -->
                        <td class="py-3 px-4 text-right font-mono font-medium text-gray-700 dark:text-slate-300 whitespace-nowrap">
                            <?= $m['ada_tagihan'] ? rupiah($m['total_nominal']) : '<span class="text-gray-300 dark:text-slate-600">-</span>' ?>
                        </td>

                        <!-- Terbayar -->
                        <td class="py-3 px-4 text-right font-mono font-medium text-emerald-600 dark:text-emerald-400 whitespace-nowrap">
                            <?= $m['ada_tagihan'] && $m['total_dibayar'] > 0 ? rupiah($m['total_dibayar']) : '<span class="text-gray-300 dark:text-slate-600">-</span>' ?>
                        </td>

                        <!-- Sisa -->
                        <td class="py-3 px-4 text-right font-mono font-bold whitespace-nowrap <?= $m['total_sisa'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-gray-400' ?>">
                            <?= $m['ada_tagihan'] ? ($m['total_sisa'] > 0 ? rupiah($m['total_sisa']) : '<span class="text-emerald-600 font-semibold">Rp 0</span>') : '<span class="text-gray-300 dark:text-slate-600">-</span>' ?>
                        </td>

                        <!-- Keterangan -->
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            <?php if ($m['status'] === 'Lunas'): ?>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800">
                                    <i class="ph ph-check-circle-fill text-emerald-600 dark:text-emerald-400 text-sm"></i>
                                    Lunas
                                </span>
                                <?php if (!empty($m['tanggal_bayar'])): ?>
                                <div class="text-[10px] text-gray-400 dark:text-slate-500 mt-1">
                                    <?= date('d/m/Y', strtotime($m['tanggal_bayar'])) ?>
                                    <?php if (!empty($m['metode_pembayaran'])): ?>· <?= esc($m['metode_pembayaran']) ?><?php endif; ?>
                                </div>
                                <?php endif; ?>

                            <?php elseif ($m['status'] === 'Mencicil'): ?>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-300 dark:border-amber-800">
                                    <i class="ph ph-clock-countdown-fill text-amber-600 dark:text-amber-400 text-sm"></i>
                                    Mencicil
                                </span>

                            <?php elseif ($m['status'] === 'Belum Lunas'): ?>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-300 dark:border-rose-800">
                                    <i class="ph ph-x-circle-fill text-rose-600 dark:text-rose-400 text-sm"></i>
                                    Belum Lunas
                                </span>

                            <?php else: ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-gray-100 text-gray-400 dark:bg-slate-800 dark:text-slate-500 border border-gray-200 dark:border-slate-700">
                                    <i class="ph ph-minus text-xs"></i>
                                    Belum Terbit
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>

                <tfoot>
                    <tr class="bg-gray-100/80 dark:bg-slate-900/80 font-bold border-t border-gray-200 dark:border-slate-700 text-xs">
                        <td colspan="3" class="py-3 px-4 text-gray-800 dark:text-white uppercase tracking-wider">
                            Total SPP Tahun <?= esc($selectedYear) ?>
                        </td>
                        <td class="py-3 px-4 text-right font-mono text-gray-900 dark:text-white">
                            <?= rupiah($matriksSummary['totalNominal']) ?>
                        </td>
                        <td class="py-3 px-4 text-right font-mono text-emerald-600 dark:text-emerald-400">
                            <?= rupiah($matriksSummary['totalTerbayar']) ?>
                        </td>
                        <td class="py-3 px-4 text-right font-mono <?= $matriksSummary['totalSisa'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600' ?>">
                            <?= rupiah($matriksSummary['totalSisa']) ?>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <?php if ($matriksSummary['totalSisa'] == 0 && $matriksSummary['totalNominal'] > 0): ?>
                            <span class="text-emerald-600 dark:text-emerald-400 text-xs font-bold">✓ Tuntas</span>
                            <?php elseif ($matriksSummary['totalSisa'] > 0): ?>
                            <span class="text-rose-600 dark:text-rose-400 text-xs font-bold"><?= rupiah($matriksSummary['totalSisa']) ?></span>
                            <?php else: ?>
                            <span class="text-gray-400 text-xs">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Riwayat Kwitansi Kasir -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
        <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-slate-700/60 mb-4">
            <div class="flex items-center gap-2">
                <i class="ph ph-receipt text-xl text-blue-600 dark:text-blue-400"></i>
                <h4 class="text-base font-bold text-gray-900 dark:text-white">Riwayat Kwitansi Pembayaran di Kasir</h4>
            </div>
            <span class="text-xs text-gray-400"><?= count($pembayaranList) ?> kwitansi tercatat</span>
        </div>

        <?php if (empty($pembayaranList)): ?>
        <div class="py-8 text-center text-gray-400">
            <i class="ph ph-receipt-x text-3xl mb-1"></i>
            <p class="text-xs">Belum ada riwayat pembayaran kasir untuk siswa ini.</p>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-slate-900/60 border-b border-gray-100 dark:border-slate-700 text-gray-500 font-bold uppercase tracking-wider">
                        <th class="py-2.5 px-3">No. Kwitansi</th>
                        <th class="py-2.5 px-3">Tanggal Bayar</th>
                        <th class="py-2.5 px-3">Metode</th>
                        <th class="py-2.5 px-3 text-right">Total Dibayar</th>
                        <th class="py-2.5 px-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    <?php foreach ($pembayaranList as $p): ?>
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="py-3 px-3 font-mono font-bold text-blue-600 dark:text-blue-400"><?= esc($p['no_kwitansi']) ?></td>
                        <td class="py-3 px-3"><?= tanggal_indo($p['tanggal_bayar']) ?></td>
                        <td class="py-3 px-3">
                            <span class="px-2 py-0.5 rounded-md font-semibold text-[11px] bg-gray-100 text-gray-700 dark:bg-slate-700 dark:text-slate-300">
                                <?= esc($p['metode_pembayaran']) ?>
                            </span>
                        </td>
                        <td class="py-3 px-3 text-right font-mono font-bold text-gray-900 dark:text-white"><?= rupiah($p['total_bayar']) ?></td>
                        <td class="py-3 px-3 text-center">
                            <a href="<?= base_url('keuangan/kasir/cetak/' . $p['id']) ?>" target="_blank"
                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 text-gray-700 dark:text-slate-300 font-medium transition-colors">
                                <i class="ph ph-printer"></i>
                                <span>Cetak</span>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <?php endif; ?>

</div>
<?= $this->endSection() ?>
