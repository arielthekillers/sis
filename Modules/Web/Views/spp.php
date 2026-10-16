<!DOCTYPE html>
<html lang="id" x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }" x-init="$watch('darkMode', val => localStorage.setItem('theme', val ? 'dark' : 'light'))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <meta name="description" content="<?= esc($description) ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url(app_setting('school_favicon', 'uploads/logo.png')) ?>">
    
    <!-- Tailwind CSS Output -->
    <link href="<?= base_url('css/app.css') ?>" rel="stylesheet">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Phosphor Icons -->
    <script src="https://cdn.jsdelivr.net/npm/@phosphor-icons/web"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-screen antialiased selection:bg-blue-600 selection:text-white" style="font-family: 'Outfit', sans-serif;">

    <!-- Background Decorative Glow -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-tr from-blue-500/10 via-emerald-500/10 to-transparent blur-3xl rounded-full"></div>
    </div>

    <!-- Main Wrapper -->
    <div class="relative z-10 max-w-4xl mx-auto px-4 py-8 sm:py-12 space-y-6">

        <!-- Top Navigation & Brand Header -->
        <header class="flex items-center justify-between pb-2">
            <a href="<?= base_url() ?>" class="flex items-center gap-3 group">
                <img src="<?= base_url(app_setting('school_logo', 'uploads/logo.png')) ?>" alt="Logo" class="w-10 h-10 object-contain rounded-xl drop-shadow-xs group-hover:scale-105 transition-transform">
                <div>
                    <h1 class="text-base font-bold text-slate-900 dark:text-white tracking-tight leading-tight"><?= esc(app_setting('school_name', 'Sekolah')) ?></h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Portal SPP</p>
                </div>
            </a>

            <!-- Dark Mode Toggle Button -->
            <button @click="darkMode = !darkMode" type="button" class="p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white shadow-xs transition-colors" title="Ubah Tema">
                <i class="ph text-lg" :class="darkMode ? 'ph-sun text-amber-400' : 'ph-moon text-slate-600'"></i>
            </button>
        </header>

        <!-- Compact Search Box Card -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-7 shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-800 space-y-4">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 mb-2">
                    <i class="ph ph-shield-check text-xs"></i>
                    Akses Orang Tua / Wali Murid
                </span>
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">SPP Siswa</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">Masukkan Nomor Induk Siswa (NIS) ananda untuk melihat status dan rincian SPP per bulan.</p>
            </div>

            <!-- Form Cari NIS -->
            <form action="<?= base_url('spp') ?>" method="GET" class="space-y-3">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 text-xl">
                            <i class="ph ph-identification-card"></i>
                        </div>
                        <input type="text" name="nis" value="<?= esc($nis) ?>" required autofocus
                            placeholder="Masukkan NIS Siswa (contoh: S0001)..." 
                            class="block w-full pl-12 pr-4 py-3.5 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-semibold tracking-wide text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:bg-white dark:focus:bg-slate-800 focus:ring-4 focus:ring-blue-500/10 transition-all uppercase">
                    </div>

                    <?php if (!empty($selectedYear)): ?>
                    <input type="hidden" name="tahun" value="<?= esc($selectedYear) ?>">
                    <?php endif; ?>

                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold shadow-lg shadow-blue-600/25 active:scale-[0.99] transition-all shrink-0">
                        <i class="ph ph-magnifying-glass text-base"></i>
                        <span>Lihat SPP</span>
                    </button>
                </div>
            </form>

            <p class="text-[11px] text-slate-400 dark:text-slate-500">
                <i class="ph ph-info mr-1"></i> NIS dapat dilihat pada kartu pelajar, buku rapor, atau pesan WhatsApp resmi sekolah.
            </p>
        </div>

        <?php if ($searched && $notFound): ?>
        <!-- Not Found Card -->
        <div class="bg-rose-50/80 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/40 rounded-3xl p-6 text-center space-y-3 animate-fade-in">
            <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 dark:bg-rose-900/50 dark:text-rose-400 flex items-center justify-center text-2xl mx-auto shadow-sm">
                <i class="ph ph-user-x"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-rose-900 dark:text-rose-300">Data Siswa Tidak Ditemukan</h3>
                <p class="text-xs text-rose-700 dark:text-rose-400/90 mt-1 max-w-md mx-auto">
                    Nomor Induk Siswa <strong class="font-mono underline">"<?= esc($nis) ?>"</strong> tidak terdaftar di sistem. Mohon pastikan kembali NIS yang dimasukkan sudah benar atau hubungi tata usaha sekolah.
                </p>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($searched && !$notFound && $siswa): ?>
        <!-- Data Siswa & Tabel SPP 12 Bulan -->
        <div class="space-y-6 animate-fade-in">

            <!-- 1. Student Identity Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center text-2xl font-black shadow-md shadow-blue-600/20 shrink-0">
                        <?= strtoupper(substr($siswa['nama_lengkap'], 0, 1)) ?>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                <i class="ph ph-check-circle"></i> <?= esc($siswa['status_siswa'] ?? 'Aktif') ?>
                            </span>
                            <span class="text-xs text-slate-400 font-mono">NIS: <?= esc($siswa['nis'] ?: '-') ?></span>
                        </div>
                        <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white mt-1 leading-snug"><?= esc($siswa['nama_lengkap']) ?></h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Rombel / Kelas: <strong class="text-blue-600 dark:text-blue-400 font-bold"><?= esc($rombel) ?></strong>
                            <?php if (!empty($siswa['nisn'])): ?> · NISN: <span class="font-mono"><?= esc($siswa['nisn']) ?></span><?php endif; ?>
                        </p>
                    </div>
                </div>

                <!-- Overall Status Pill -->
                <div class="sm:text-right">
                    <?php if ($stats['totalSisa'] == 0): ?>
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-2xl text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                        <i class="ph ph-check-circle-fill text-emerald-600 dark:text-emerald-400 text-base"></i>
                        Semua SPP Lunas
                    </span>
                    <?php else: ?>
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-2xl text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                        <i class="ph ph-warning-circle-fill text-rose-600 dark:text-rose-400 text-base"></i>
                        Sisa SPP: <?= rupiah($stats['totalSisa']) ?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 2. TABEL SPP 12 BULAN (JANUARI - DESEMBER) -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-800 overflow-hidden">
                
                <!-- Table Header & Year Filter -->
                <div class="p-5 sm:p-6 border-b border-slate-100 dark:border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400 flex items-center justify-center text-lg">
                                <i class="ph ph-calendar-blank"></i>
                            </div>
                            <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">Tabel SPP Tahun <?= esc($selectedYear) ?></h3>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Daftar pembayaran bulanan (Januari – Desember) beserta keterangan status lunas.</p>
                    </div>

                    <!-- Year Selector Tabs (Tahun Ini, -1, dan +1) -->
                    <div class="flex items-center gap-1 p-1 bg-slate-100 dark:bg-slate-800 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 shrink-0">
                        <?php foreach ($availableYears as $yr): ?>
                        <a href="<?= base_url('spp') ?>?nis=<?= urlencode($nis) ?>&tahun=<?= $yr ?>"
                           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 <?= $yr == $selectedYear ? 'bg-white dark:bg-slate-700 text-blue-600 dark:text-blue-400 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' ?>">
                            <span><?= $yr ?></span>
                            <?php if ($yr == date('Y')): ?>
                            <span class="text-[10px] px-1.5 py-0.5 rounded-md font-semibold <?= $yr == $selectedYear ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/50 dark:text-blue-300' : 'bg-slate-200/70 text-slate-500 dark:bg-slate-600 dark:text-slate-300' ?>">Tahun Ini</span>
                            <?php endif; ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Year Quick Progress Indicators -->
                <div class="bg-slate-50/70 dark:bg-slate-800/40 px-5 sm:px-6 py-3 border-b border-slate-100 dark:border-slate-800 flex flex-wrap items-center gap-4 text-xs font-medium">
                    <span class="text-slate-500 dark:text-slate-400">Ringkasan Tahun <?= esc($selectedYear) ?>:</span>
                    <span class="inline-flex items-center gap-1.5 text-emerald-700 dark:text-emerald-400 font-bold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <?= $matriksSummary['countLunas'] ?> Bulan Lunas
                    </span>
                    <?php if ($matriksSummary['countBelumLunas'] > 0): ?>
                    <span class="inline-flex items-center gap-1.5 text-rose-600 dark:text-rose-400 font-bold">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        <?= $matriksSummary['countBelumLunas'] ?> Bulan Belum Lunas
                    </span>
                    <?php endif; ?>
                    <?php if ($matriksSummary['countMencicil'] > 0): ?>
                    <span class="inline-flex items-center gap-1.5 text-amber-600 dark:text-amber-400 font-bold">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <?= $matriksSummary['countMencicil'] ?> Mencicil
                    </span>
                    <?php endif; ?>
                    <span class="inline-flex items-center gap-1.5 text-slate-400 dark:text-slate-500">
                        <span class="w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                        <?= $matriksSummary['countBelumTerbit'] ?> Belum Terbit
                    </span>
                </div>

                <!-- 12-Month Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse min-w-[620px]">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-100 dark:border-slate-800 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                <th class="py-3 px-4 w-12 text-center">#</th>
                                <th class="py-3 px-4 w-32">Bulan</th>
                                <th class="py-3 px-4">Rincian SPP</th>
                                <th class="py-3 px-4 text-right">Nominal</th>
                                <th class="py-3 px-4 text-right">Terbayar</th>
                                <th class="py-3 px-4 text-right">Sisa SPP</th>
                                <th class="py-3 px-4 text-center w-36">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <?php foreach ($matriksBulan as $m): ?>
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/50 transition-colors <?= $m['status'] === 'Belum Lunas' ? 'bg-rose-50/20 dark:bg-rose-950/10' : ($m['status'] === 'Lunas' ? 'bg-emerald-50/10 dark:bg-emerald-950/5' : '') ?>">
                                <!-- 1. Nomor Bulan -->
                                <td class="py-3.5 px-4 text-center font-mono text-xs text-slate-400 font-semibold">
                                    <?= sprintf('%02d', $m['nomor']) ?>
                                </td>

                                <!-- 2. Nama Bulan -->
                                <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <i class="ph ph-calendar text-slate-400 text-sm"></i>
                                        <span><?= esc($m['nama_bulan']) ?></span>
                                    </div>
                                </td>

                                <!-- 3. Rincian SPP -->
                                <td class="py-3.5 px-4 text-xs text-slate-600 dark:text-slate-300">
                                    <?php if ($m['ada_tagihan']): ?>
                                        <div class="space-y-1">
                                            <?php foreach ($m['items'] as $item): ?>
                                            <div class="font-semibold text-slate-800 dark:text-slate-200">
                                                <?= esc($item['judul_tagihan']) ?>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-slate-400 italic text-[11px]">- Belum ada tagihan -</span>
                                    <?php endif; ?>
                                </td>

                                <!-- 4. Nominal -->
                                <td class="py-3.5 px-4 text-right font-mono font-medium text-slate-700 dark:text-slate-300 whitespace-nowrap">
                                    <?= $m['ada_tagihan'] ? rupiah($m['total_nominal']) : '<span class="text-slate-300 dark:text-slate-600">-</span>' ?>
                                </td>

                                <!-- 5. Terbayar -->
                                <td class="py-3.5 px-4 text-right font-mono font-medium text-emerald-600 dark:text-emerald-400 whitespace-nowrap">
                                    <?= $m['ada_tagihan'] && $m['total_dibayar'] > 0 ? rupiah($m['total_dibayar']) : '<span class="text-slate-300 dark:text-slate-600">-</span>' ?>
                                </td>

                                <!-- 6. Sisa SPP -->
                                <td class="py-3.5 px-4 text-right font-mono font-bold whitespace-nowrap <?= $m['total_sisa'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400' ?>">
                                    <?= $m['ada_tagihan'] ? ($m['total_sisa'] > 0 ? rupiah($m['total_sisa']) : '<span class="text-emerald-600 font-semibold">Rp 0</span>') : '<span class="text-slate-300 dark:text-slate-600">-</span>' ?>
                                </td>

                                <!-- 7. Keterangan / Status Lunas -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <?php if ($m['status'] === 'Lunas'): ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800 shadow-xs">
                                            <i class="ph ph-check-circle-fill text-emerald-600 dark:text-emerald-400 text-sm"></i>
                                            Lunas
                                        </span>
                                        <?php if (!empty($m['tanggal_bayar'])): ?>
                                        <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">
                                            <?= date('d/m/Y', strtotime($m['tanggal_bayar'])) ?>
                                            <?php if (!empty($m['metode_pembayaran'])): ?>· <?= esc($m['metode_pembayaran']) ?><?php endif; ?>
                                        </div>
                                        <?php endif; ?>

                                    <?php elseif ($m['status'] === 'Mencicil'): ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-300 dark:border-amber-800 shadow-xs">
                                            <i class="ph ph-clock-countdown-fill text-amber-600 dark:text-amber-400 text-sm"></i>
                                            Mencicil
                                        </span>

                                    <?php elseif ($m['status'] === 'Belum Lunas'): ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-300 dark:border-rose-800 shadow-xs">
                                            <i class="ph ph-x-circle-fill text-rose-600 dark:text-rose-400 text-sm"></i>
                                            Belum Lunas
                                        </span>

                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-500 border border-slate-200 dark:border-slate-700">
                                            <i class="ph ph-minus text-xs"></i>
                                            Belum Terbit
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>

                        <!-- Table Footer Total -->
                        <tfoot>
                            <tr class="bg-slate-100/80 dark:bg-slate-800/80 font-bold border-t border-slate-200 dark:border-slate-700 text-xs">
                                <td colspan="3" class="py-3 px-4 text-slate-800 dark:text-white uppercase tracking-wider">
                                    Total SPP Tahun <?= esc($selectedYear) ?>
                                </td>
                                <td class="py-3 px-4 text-right font-mono text-slate-900 dark:text-white">
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
                                    <span class="text-slate-400 text-xs">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>

            <!-- 3. Payment Instructions & Confirmation Card -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-950 text-white rounded-3xl p-6 sm:p-7 shadow-xl space-y-6">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-white/10 text-slate-200 mb-2">
                        <i class="ph ph-bank"></i> Rekening Resmi Pembayaran SPP
                    </span>
                    <h4 class="text-lg sm:text-xl font-bold">Pembayaran SPP</h4>
                    <p class="text-xs text-slate-300 mt-1">Pembayaran SPP dapat dilakukan melalui transfer antar-bank atau secara tunai di loket kasir sekolah.</p>
                </div>

                <!-- Bank Card Box -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <p class="text-xs text-slate-400 font-medium"><?= esc($bankInfo['name']) ?></p>
                        <p class="text-2xl font-mono font-bold tracking-wider text-white" id="bank-acc-num"><?= esc($bankInfo['number']) ?></p>
                        <p class="text-xs text-slate-300">Atas Nama: <strong class="text-white"><?= esc($bankInfo['holder']) ?></strong></p>
                    </div>

                    <button type="button" 
                        onclick="navigator.clipboard.writeText('<?= esc($bankInfo['number']) ?>'); Swal.fire({ icon: 'success', title: 'Nomor Rekening Disalin!', text: '<?= esc($bankInfo['number']) ?> berhasil disalin ke clipboard.', timer: 2000, showConfirmButton: false, toast: true, position: 'top-end' });"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold border border-white/15 transition-all shrink-0">
                        <i class="ph ph-copy text-base"></i>
                        <span>Salin No. Rekening</span>
                    </button>
                </div>

                <!-- Alternative Cash Info & WhatsApp Confirmation Button -->
                <div class="space-y-3 pt-1">
                    <p class="text-xs text-slate-400 flex items-start gap-2">
                        <i class="ph ph-map-pin text-base shrink-0 mt-0.5 text-blue-400"></i>
                        <span>Pembayaran tunai dapat dilayani langsung di loket kasir / Tata Usaha <?= esc(app_setting('school_name', 'Sekolah')) ?> pada jam kerja (Senin – Jumat, 08.00 – 14.30 WIB).</span>
                    </p>

                    <?php if (!empty($waLink)): ?>
                    <a href="<?= $waLink ?>" target="_blank" rel="noopener noreferrer"
                       class="w-full inline-flex items-center justify-center gap-2.5 px-5 py-3.5 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-sm shadow-lg shadow-emerald-500/25 transition-all active:scale-[0.99]">
                        <i class="ph ph-whatsapp-logo text-xl"></i>
                        <span>Konfirmasi Pembayaran SPP via WhatsApp</span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>

        </div>
        <?php endif; ?>

        <!-- Footer -->
        <footer class="text-center pt-4 pb-8 space-y-2 text-xs text-slate-400 dark:text-slate-600">
            <p>© <?= date('Y') ?> <?= esc(app_setting('school_name', 'Sekolah')) ?> · Seluruh Hak Cipta Dilindungi</p>
            <div class="flex items-center justify-center gap-3 text-[11px]">
                <a href="<?= base_url() ?>" class="hover:text-slate-700 dark:hover:text-slate-400 hover:underline">Website Sekolah</a>
                <span>·</span>
                <a href="<?= base_url('sis') ?>" target="_blank" rel="noopener noreferrer" class="hover:text-slate-700 dark:hover:text-slate-400 hover:underline">Portal Petugas SIS 🔐</a>
            </div>
        </footer>

    </div>

</body>
</html>
