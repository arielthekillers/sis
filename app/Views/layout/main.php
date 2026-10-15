<!DOCTYPE html>
<html lang="id" x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }" x-init="$watch('darkMode', val => localStorage.setItem('theme', val ? 'dark' : 'light'))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'RSK APPS - Sistem Manajemen Sekolah' ?></title>
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('uploads/logo.png') ?>">
    <link rel="icon" type="image/svg+xml" href="<?= base_url('uploads/logo.svg') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('uploads/logo.png') ?>">
    <!-- Tailwind CSS output -->
    <link href="<?= base_url('css/app.css') ?>" rel="stylesheet">
    <!-- Alpine.js untuk interaksi menu & dropdown (sangat ringan) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Phosphor Icons (Menggunakan jsdelivr karena unpkg terkadang lambat) -->
    <script src="https://cdn.jsdelivr.net/npm/@phosphor-icons/web"></script>
    <!-- SweetAlert2 untuk Popup & Toasts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        // Mencegah flash putih sebelum Alpine berjalan
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 dark:bg-slate-900 dark:text-slate-200 antialiased overflow-hidden" style="font-family: 'Outfit', sans-serif;" x-data="{ sidebarOpen: false }">

    <div class="flex h-screen w-full">
        
        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-gray-900/80 backdrop-blur-sm lg:hidden" x-transition.opacity @click="sidebarOpen = false"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 flex flex-col bg-white dark:bg-slate-900 text-gray-600 dark:text-slate-300 transition-transform duration-300 lg:static lg:translate-x-0 shadow-2xl lg:shadow-none border-r border-gray-200 dark:border-slate-800">
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between h-14 px-4 bg-gray-50 dark:bg-slate-950 border-b border-gray-200 dark:border-slate-800 shrink-0">
                <div class="flex items-center gap-2">
                    <img src="<?= base_url('uploads/logo.png') ?>" alt="RSK APPS" class="h-8 w-auto object-contain">
                    <div class="leading-tight">
                        <span class="text-sm font-bold text-gray-900 dark:text-white tracking-wide">RSK <span class="text-rose-500 dark:text-rose-400">APPS</span></span>
                        <p class="text-[9px] text-gray-400 dark:text-slate-500 leading-none">Preschool & Kindergarten</p>
                    </div>
                </div>
                <!-- Close Button (Mobile Only) -->
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
                    <i class="ph ph-x text-2xl"></i>
                </button>
            </div>

            <!-- Sidebar Navigation -->
            <div class="flex-1 overflow-y-auto overflow-x-hidden p-2 space-y-0.5 custom-scrollbar">
                
                <!-- Main Dashboard -->
                <a href="<?= base_url() ?>" class="group flex items-center gap-2 px-3 py-1.5 rounded-md text-[13px] font-medium transition-colors hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white <?= uri_string() == '' ? 'bg-blue-50 text-blue-700 dark:bg-blue-600/10 dark:text-blue-400' : '' ?>">
                    <i class="ph ph-squares-four text-base text-blue-500 dark:text-blue-400 group-hover:scale-110 transition-transform"></i>
                    <span>Dashboard</span>
                </a>

                <div class="pt-3 pb-1">
                    <p class="px-3 text-[10px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Modul Sistem</p>
                </div>

                <!-- Modul Master Data (Dengan Submenu) -->
                <div x-data="{ open: <?= strpos(uri_string(), 'master-data') === 0 ? 'true' : 'false' ?> }">
                    <button @click="open = !open" class="group w-full flex items-center justify-between gap-2 px-3 py-1.5 rounded-md text-[13px] font-medium transition-colors hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white <?= strpos(uri_string(), 'master-data') === 0 ? 'text-gray-900 dark:text-white' : '' ?>">
                        <div class="flex items-center gap-2">
                            <i class="ph ph-database text-base text-indigo-500 dark:text-indigo-400 group-hover:scale-110 transition-transform"></i>
                            <span>Master Data</span>
                        </div>
                        <i class="ph ph-caret-down text-[10px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-collapse>
                        <div class="pl-8 pr-2 py-0.5 mt-0.5 space-y-0.5 border-l border-gray-200 dark:border-slate-700 ml-4">
                            <a href="<?= base_url('master-data/tahun-ajaran') ?>" class="block px-2 py-1.5 rounded text-xs transition-colors <?= uri_string() == 'master-data/tahun-ajaran' ? 'bg-indigo-50 text-indigo-700 font-semibold dark:bg-indigo-500/10 dark:text-indigo-400' : 'text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white' ?>">Tahun Ajaran</a>
                            <a href="<?= base_url('master-data/mata-pelajaran') ?>" class="block px-2 py-1.5 rounded text-xs transition-colors <?= uri_string() == 'master-data/mata-pelajaran' ? 'bg-indigo-50 text-indigo-700 font-semibold dark:bg-indigo-500/10 dark:text-indigo-400' : 'text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white' ?>">Mata Pelajaran</a>
                        </div>
                    </div>
                </div>

                <!-- Modul Civitas Akademika (Submenu) -->
                <div x-data="{ open: <?= strpos(uri_string(), 'civitas-akademika') === 0 ? 'true' : 'false' ?> }">
                    <button @click="open = !open" class="group w-full flex items-center justify-between gap-2 px-3 py-1.5 rounded-md text-[13px] font-medium transition-colors hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white <?= strpos(uri_string(), 'civitas-akademika') === 0 ? 'text-gray-900 dark:text-white' : '' ?>">
                        <div class="flex items-center gap-2">
                            <i class="ph ph-users-three text-base text-emerald-500 dark:text-emerald-400 group-hover:scale-110 transition-transform"></i>
                            <span>Civitas Akademika</span>
                        </div>
                        <i class="ph ph-caret-down text-[10px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-collapse>
                        <div class="pl-8 pr-2 py-0.5 mt-0.5 space-y-0.5 border-l border-gray-200 dark:border-slate-700 ml-4">
                            <a href="<?= base_url('civitas-akademika/guru-staf') ?>" class="block px-2 py-1.5 rounded text-xs transition-colors <?= uri_string() == 'civitas-akademika/guru-staf' ? 'bg-emerald-50 text-emerald-700 font-semibold dark:bg-emerald-500/10 dark:text-emerald-400' : 'text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white' ?>">Data Guru & Staf</a>
                            <a href="<?= base_url('civitas-akademika/siswa') ?>" class="block px-2 py-1.5 rounded text-xs transition-colors <?= uri_string() == 'civitas-akademika/siswa' ? 'bg-emerald-50 text-emerald-700 font-semibold dark:bg-emerald-500/10 dark:text-emerald-400' : 'text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white' ?>">Data Siswa</a>
                        </div>
                    </div>
                </div>

                <!-- Modul Akademik -->
                <div x-data="{ open: <?= strpos(uri_string(), 'akademik') === 0 ? 'true' : 'false' ?> }">
                    <button @click="open = !open" class="group w-full flex items-center justify-between gap-2 px-3 py-1.5 rounded-md text-[13px] font-medium transition-colors hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white <?= strpos(uri_string(), 'akademik') === 0 ? 'text-gray-900 dark:text-white' : '' ?>">
                        <div class="flex items-center gap-2">
                            <i class="ph ph-graduation-cap text-base text-amber-500 dark:text-amber-400 group-hover:scale-110 transition-transform"></i>
                            <span>Akademik & Nilai</span>
                        </div>
                        <i class="ph ph-caret-down text-[10px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-collapse>
                        <div class="pl-8 pr-2 py-0.5 mt-0.5 space-y-0.5 border-l border-gray-200 dark:border-slate-700 ml-4">
                            <a href="<?= base_url('akademik/rombel') ?>" class="block px-2 py-1.5 rounded text-xs transition-colors <?= uri_string() == 'akademik/rombel' ? 'bg-amber-50 text-amber-700 font-semibold dark:bg-amber-500/10 dark:text-amber-400' : 'text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white' ?>">Rombongan Belajar</a>
                            <a href="<?= base_url('akademik/anggota-rombel') ?>" class="block px-2 py-1.5 rounded text-xs transition-colors <?= strpos(uri_string(), 'akademik/anggota-rombel') === 0 ? 'bg-amber-50 text-amber-700 font-semibold dark:bg-amber-500/10 dark:text-amber-400' : 'text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white' ?>">Penempatan Siswa</a>
                        </div>
                    </div>
                </div>

                <!-- Keuangan -->
                <div class="space-y-1" x-data="{ open: <?= strpos(uri_string(), 'keuangan') === 0 ? 'true' : 'false' ?> }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg transition-colors <?= strpos(uri_string(), 'keuangan') === 0 ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white' ?>">
                        <div class="flex items-center gap-3">
                            <i class="ph ph-wallet text-lg <?= strpos(uri_string(), 'keuangan') === 0 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-slate-500' ?>"></i>
                            Keuangan
                        </div>
                        <i class="ph text-xs transition-transform duration-200" :class="open ? 'ph-caret-down' : 'ph-caret-right'"></i>
                    </button>
                    
                    <div x-show="open" x-collapse>
                        <div class="pl-8 pr-2 py-0.5 mt-0.5 space-y-0.5 border-l border-gray-200 dark:border-slate-700 ml-4">
                            <a href="<?= base_url('keuangan/jenis-pembayaran') ?>" class="block px-2 py-1.5 rounded text-xs transition-colors <?= uri_string() == 'keuangan/jenis-pembayaran' ? 'bg-amber-50 text-amber-700 font-semibold dark:bg-amber-500/10 dark:text-amber-400' : 'text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white' ?>">Master Jenis Tagihan</a>
                            <a href="<?= base_url('keuangan/setup-tarif') ?>" class="block px-2 py-1.5 rounded text-xs transition-colors <?= strpos(uri_string(), 'keuangan/setup-tarif') === 0 ? 'bg-amber-50 text-amber-700 font-semibold dark:bg-amber-500/10 dark:text-amber-400' : 'text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white' ?>">Setup Tarif Siswa</a>
                            <a href="<?= base_url('keuangan/generate-tagihan') ?>" class="block px-2 py-1.5 rounded text-xs transition-colors <?= strpos(uri_string(), 'keuangan/generate-tagihan') === 0 ? 'bg-amber-50 text-amber-700 font-semibold dark:bg-amber-500/10 dark:text-amber-400' : 'text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white' ?>">Terbitkan Tagihan</a>
                            <a href="<?= base_url('keuangan/daftar-tagihan') ?>" class="block px-2 py-1.5 rounded text-xs transition-colors <?= strpos(uri_string(), 'keuangan/daftar-tagihan') === 0 ? 'bg-amber-50 text-amber-700 font-semibold dark:bg-amber-500/10 dark:text-amber-400' : 'text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white' ?>">Daftar & Laporan Tagihan</a>
                            <a href="<?= base_url('keuangan/kasir') ?>" class="block px-2 py-1.5 rounded text-xs transition-colors <?= strpos(uri_string(), 'keuangan/kasir') === 0 ? 'bg-amber-50 text-amber-700 font-semibold dark:bg-amber-500/10 dark:text-amber-400' : 'text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white' ?>">Kasir Pembayaran</a>
                            <a href="<?= base_url('keuangan/riwayat-transaksi') ?>" class="block px-2 py-1.5 rounded text-xs transition-colors <?= strpos(uri_string(), 'keuangan/riwayat-transaksi') === 0 ? 'bg-amber-50 text-amber-700 font-semibold dark:bg-amber-500/10 dark:text-amber-400' : 'text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white' ?>">Riwayat Transaksi</a>
                            <a href="<?= base_url('keuangan/pengeluaran') ?>" class="block px-2 py-1.5 rounded text-xs transition-colors <?= strpos(uri_string(), 'keuangan/pengeluaran') === 0 ? 'bg-amber-50 text-amber-700 font-semibold dark:bg-amber-500/10 dark:text-amber-400' : 'text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white' ?>">Pengeluaran Kas</a>
                            <a href="<?= base_url('keuangan/buku-kas') ?>" class="block px-2 py-1.5 rounded text-xs transition-colors <?= strpos(uri_string(), 'keuangan/buku-kas') === 0 ? 'bg-amber-50 text-amber-700 font-semibold dark:bg-amber-500/10 dark:text-amber-400' : 'text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white' ?>">Buku Kas (Laporan)</a>
                        </div>
                    </div>
                </div>

                <div class="pt-3 pb-1">
                    <p class="px-3 text-[10px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Pengaturan</p>
                </div>

                <!-- Manajemen Pengguna -->
                <a href="<?= base_url('users') ?>" class="group flex items-center gap-2 px-3 py-1.5 rounded-md text-[13px] font-medium transition-colors hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white <?= strpos(uri_string(), 'users') === 0 ? 'bg-blue-50 text-blue-700 dark:bg-blue-600/10 dark:text-blue-400' : '' ?>">
                    <i class="ph ph-shield-check text-base text-sky-500 dark:text-sky-400 group-hover:scale-110 transition-transform"></i>
                    <span>Manajemen Pengguna</span>
                </a>
                
                <!-- Konfigurasi Sistem -->
                <a href="#" class="group flex items-center gap-2 px-3 py-1.5 rounded-md text-[13px] font-medium transition-colors hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white">
                    <i class="ph ph-gear text-base text-slate-500 dark:text-slate-400 group-hover:scale-110 transition-transform"></i>
                    <span>Konfigurasi Sistem</span>
                </a>
            </div>

            <!-- User Profile Area di Sidebar -->
            <div class="p-3 border-t border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 shrink-0">
                <div class="flex items-center gap-2">
                    <img src="https://ui-avatars.com/api/?name=Admin+SIS&background=random" alt="Avatar" class="w-8 h-8 rounded border border-gray-300 dark:border-slate-700">
                    <div class="flex-1 min-w-0">
                        <p class="text-[13px] font-medium text-gray-900 dark:text-white truncate">Administrator</p>
                        <p class="text-[11px] text-gray-500 dark:text-slate-400 truncate">Superadmin</p>
                    </div>
                    <a href="<?= url_to('logout') ?>" class="text-gray-400 dark:text-slate-400 hover:text-red-500 dark:hover:text-red-400 transition-colors p-1" title="Logout">
                        <i class="ph ph-sign-out text-lg"></i>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto bg-slate-50 dark:bg-slate-900 transition-colors duration-300 relative custom-scrollbar">
            
            <!-- Topbar (Header) -->
            <header class="h-14 bg-white/70 dark:bg-slate-950/70 backdrop-blur-lg border-b border-gray-200/50 dark:border-slate-800/50 flex items-center justify-between px-4 sm:px-6 z-10 sticky top-0 transition-colors duration-300 shrink-0">
                <!-- Left: Menu Button & Breadcrumb -->
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 rounded-md text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-700 dark:hover:text-slate-200 transition-colors">
                        <i class="ph ph-list text-xl"></i>
                    </button>
                    
                    <!-- Optional: Left Topbar Item -->
                    <div class="hidden sm:flex items-center gap-2 text-sm font-medium">
                        <?php
                            // Mengambil data Tahun Ajaran Aktif secara global untuk Header
                            $activeTa = model('Modules\MasterData\Models\TahunAjaranModel')->where('is_active', 1)->first();
                            $tahunLabel = $activeTa ? $activeTa['tahun'] . ' (' . $activeTa['semester'] . ')' : 'Belum Diatur';
                        ?>
                        <span class="text-gray-500 dark:text-slate-400">Tahun Ajaran Aktif:</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 border border-blue-200 dark:border-blue-800 shadow-sm">
                            <i class="ph ph-calendar-check mr-1 text-sm"></i>
                            <?= esc($tahunLabel) ?>
                        </span>
                    </div>
                </div>

                <!-- Right: Actions -->
                <div class="flex items-center gap-2 sm:gap-4">
                    <!-- Global Search -->
                    <div class="relative hidden md:block">
                        <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" placeholder="Pencarian cepat (Ctrl+K)" class="pl-10 pr-4 py-2 w-64 text-sm bg-gray-100 dark:bg-slate-800 border-transparent rounded-full focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder-gray-400 dark:placeholder-slate-500 dark:text-slate-200">
                    </div>

                    <!-- Dark Mode Toggle -->
                    <button @click="darkMode = !darkMode" class="w-9 h-9 flex items-center justify-center rounded-full text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors" title="Toggle Dark/Light Mode">
                        <i class="text-xl" :class="darkMode ? 'ph ph-sun' : 'ph ph-moon'"></i>
                    </button>

                    <!-- Notification -->
                    <button class="relative w-9 h-9 flex items-center justify-center rounded-full text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors">
                        <i class="ph ph-bell text-xl"></i>
                        <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-red-500 ring-2 ring-white dark:ring-slate-950"></span>
                    </button>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto space-y-6">
                    
                    <!-- PAGE HEADER START -->
                    <?php if ($this->renderSection('page_title')): ?>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <!-- Left: Title, Subtitle, Breadcrumb -->
                        <div>
                            <!-- Breadcrumbs -->
                            <nav class="flex mb-1.5" aria-label="Breadcrumb">
                                <ol class="flex items-center space-x-2 text-[13px] text-gray-500 dark:text-slate-400">
                                    <li><a href="<?= base_url() ?>" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors"><i class="ph ph-house"></i></a></li>
                                    <?= $this->renderSection('breadcrumb') ?>
                                </ol>
                            </nav>
                            <!-- Heading -->
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                                <?= $this->renderSection('page_title') ?>
                            </h1>
                            <!-- Subheading -->
                            <?php if ($this->renderSection('page_subtitle')): ?>
                            <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">
                                <?= $this->renderSection('page_subtitle') ?>
                            </p>
                            <?php endif; ?>
                        </div>

                        <!-- Right: Action Buttons -->
                        <div class="flex items-center gap-2 sm:gap-3">
                            <?= $this->renderSection('page_actions') ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <!-- PAGE HEADER END -->

                    <!-- Flash Messages (Diganti dengan SweetAlert Toast di bagian bawah) -->

                    <!-- Injected Content -->
                    <div class="pb-10">
                        <?= $this->renderSection('content') ?>
                    </div>
                </div>
            </main>
        </div>

    </div>

    <!-- Tambahan CSS Khusus untuk Scrollbar agar lebih cantik -->
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #334155;
            border-radius: 10px;
        }
        /* Alpine x-collapse transition bug fix */
        [x-cloak] { display: none !important; }
        
        /* SweetAlert2 Custom Dark Mode Tweaks */
        body.dark .swal2-popup {
            background: #1e293b !important; /* slate-800 */
            color: #f1f5f9 !important; /* slate-100 */
        }
        body.dark .swal2-title { color: #f8fafc !important; }
        body.dark .swal2-html-container { color: #cbd5e1 !important; }
    </style>

    <!-- Script Global untuk Toast Flash Messages -->
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#ffffff',
            color: document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#1e293b',
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        <?php if (session()->getFlashdata('success')) : ?>
            Toast.fire({ icon: 'success', title: '<?= addslashes(session()->getFlashdata('success')) ?>' });
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')) : ?>
            Toast.fire({ icon: 'error', title: '<?= addslashes(session()->getFlashdata('error')) ?>' });
        <?php endif; ?>

        <?php if (session()->getFlashdata('info')) : ?>
            Toast.fire({ icon: 'info', title: '<?= addslashes(session()->getFlashdata('info')) ?>' });
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('warning')) : ?>
            Toast.fire({ icon: 'warning', title: '<?= addslashes(session()->getFlashdata('warning')) ?>' });
        <?php endif; ?>

        // Fungsi Helper Global untuk Konfirmasi Hapus/Aksi Berbahaya
        function confirmAction(title, text, confirmButtonText, callback) {
            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444', // red-500
                cancelButtonColor: '#64748b', // slate-500
                confirmButtonText: confirmButtonText,
                cancelButtonText: 'Batal',
                reverseButtons: true,
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    const result = callback();
                    if (result && result.then) {
                        return result;
                    }
                    return new Promise((resolve) => {
                        // Membiarkan loading tetap berputar saat halaman berganti
                        setTimeout(() => resolve(), 3000); 
                    });
                }
            });
        }
    </script>
    
    <!-- Helpers -->
    <script src="<?= base_url('js/uploader.js') ?>"></script>
    
    <!-- Render custom scripts from views -->
    <?= $this->renderSection('scripts') ?>
</body>
</html>
