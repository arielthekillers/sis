<?= $this->extend('layout/main') ?>

<!-- 1. Breadcrumb -->
<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><span class="text-gray-500 dark:text-slate-400">Master Data</span></li>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Kamera CCTV</span></li>
<?= $this->endSection() ?>

<!-- 2. Judul Utama -->
<?= $this->section('page_title') ?>
Manajemen Kamera CCTV Sekolah
<?= $this->endSection() ?>

<!-- 3. Sub Judul -->
<?= $this->section('page_subtitle') ?>
Kelola konfigurasi perangkat kamera pemantau (CCTV), IP Public, port jaringan, dan ketersediaan tayang bagi orang tua siswa.
<?= $this->endSection() ?>

<!-- 4. Tombol Aksi Kanan Atas -->
<?= $this->section('page_actions') ?>
<button 
    @click="$dispatch('open-cctv-modal', { mode: 'create', camera: null })" 
    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all shadow-md shadow-blue-500/25 active:scale-95"
>
    <i class="ph ph-plus-circle text-base"></i>
    <span>Tambah Kamera CCTV</span>
</button>
<?= $this->endSection() ?>

<!-- 5. Konten Utama -->
<?= $this->section('content') ?>
<div x-data="cctvManagementData()" class="space-y-6">

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Kamera -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-gray-100 dark:border-slate-800 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-600/10 dark:text-blue-400 flex items-center justify-center text-2xl shrink-0">
                <i class="ph ph-video-camera"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-slate-400 font-medium">Total Kamera</p>
                <h4 class="text-xl font-bold text-gray-900 dark:text-white"><?= number_format($totalCamera) ?></h4>
            </div>
        </div>

        <!-- Kamera Aktif -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-gray-100 dark:border-slate-800 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-600/10 dark:text-emerald-400 flex items-center justify-center text-2xl shrink-0">
                <i class="ph ph-broadcast"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-slate-400 font-medium">Siaran Aktif</p>
                <h4 class="text-xl font-bold text-emerald-600 dark:text-emerald-400"><?= number_format($totalAktif) ?></h4>
            </div>
        </div>

        <!-- Khusus Fullday -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-gray-100 dark:border-slate-800 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 dark:bg-purple-600/10 dark:text-purple-400 flex items-center justify-center text-2xl shrink-0">
                <i class="ph ph-sparkle"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-slate-400 font-medium">Program Fullday</p>
                <h4 class="text-xl font-bold text-purple-600 dark:text-purple-400"><?= number_format($totalFullday) ?></h4>
            </div>
        </div>

        <!-- Nonaktif / Standby -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-gray-100 dark:border-slate-800 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 dark:bg-slate-700/50 dark:text-slate-300 flex items-center justify-center text-2xl shrink-0">
                <i class="ph ph-power"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-slate-400 font-medium">Nonaktif / Servis</p>
                <h4 class="text-xl font-bold text-gray-700 dark:text-slate-300"><?= number_format($totalNonaktif) ?></h4>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 border border-gray-100 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
        <form method="GET" action="<?= base_url('master-data/cctv') ?>" class="w-full sm:w-auto flex-1 flex flex-col sm:flex-row items-center gap-2">
            <!-- Search Input -->
            <div class="relative w-full sm:w-72">
                <i class="ph ph-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                <input 
                    type="text" 
                    name="q" 
                    value="<?= esc($keyword) ?>" 
                    placeholder="Cari nama, kode, IP Public..." 
                    class="w-full pl-10 pr-4 py-2 rounded-xl bg-gray-50 dark:bg-slate-700/50 border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white text-xs font-medium focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-blue-500 transition-all"
                >
            </div>

            <!-- Filter Status -->
            <select 
                name="status" 
                onchange="this.form.submit()" 
                class="w-full sm:w-auto px-3.5 py-2 rounded-xl bg-gray-50 dark:bg-slate-700/50 border-gray-200 dark:border-slate-700 text-gray-800 dark:text-slate-200 text-xs font-medium focus:ring-2 focus:ring-blue-500"
            >
                <option value="">Semua Status</option>
                <option value="aktif" <?= $statusFilter === 'aktif' ? 'selected' : '' ?>>Siaran Aktif</option>
                <option value="nonaktif" <?= $statusFilter === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                <option value="maintenance" <?= $statusFilter === 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
            </select>

            <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-slate-200 text-xs font-semibold transition-all">
                Filter
            </button>
            <?php if($keyword || $statusFilter): ?>
                <a href="<?= base_url('master-data/cctv') ?>" class="text-xs text-rose-500 hover:underline px-2">Reset</a>
            <?php endif; ?>
        </form>

        <span class="text-xs text-gray-400 self-end sm:self-auto">
            Menampilkan <?= count($cameras) ?> perangkat kamera
        </span>
    </div>

    <!-- Table List Kamera CCTV -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50/75 dark:bg-slate-900/50 border-b border-gray-100 dark:border-slate-800 text-gray-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="py-3.5 px-4 w-12 text-center">Urutan</th>
                        <th class="py-3.5 px-4">Kode & Nama Kamera</th>
                        <th class="py-3.5 px-4">Lokasi</th>
                        <th class="py-3.5 px-4">IP Public & Port</th>
                        <th class="py-3.5 px-4">Target Program</th>
                        <th class="py-3.5 px-4">Jam Operasional</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    <?php if (empty($cameras)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-12 text-gray-400 dark:text-slate-500">
                                <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-slate-700 flex items-center justify-center text-3xl mx-auto mb-3 text-gray-400">
                                    <i class="ph ph-video-camera-slash"></i>
                                </div>
                                <p class="text-sm font-semibold text-gray-700 dark:text-slate-300">Belum Ada Data Kamera CCTV</p>
                                <p class="text-xs text-gray-400 mt-1">Tambahkan perangkat kamera baru melalui tombol di pojok kanan atas.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($cameras as $cam): ?>
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/25 transition-colors">
                                <!-- Urutan -->
                                <td class="py-3.5 px-4 text-center font-mono font-bold text-gray-400">
                                    <?= esc($cam['urutan']) ?>
                                </td>

                                <!-- Kode & Nama -->
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center text-lg shrink-0">
                                            <i class="ph ph-video-camera"></i>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-1.5">
                                                <span class="font-mono font-bold text-[10px] px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                                    <?= esc($cam['kode_kamera']) ?>
                                                </span>
                                                <span class="font-bold text-gray-900 dark:text-white text-xs">
                                                    <?= esc($cam['nama_kamera']) ?>
                                                </span>
                                            </div>
                                            <p class="text-[11px] text-gray-400 dark:text-slate-500 truncate max-w-xs mt-0.5">
                                                <?= esc($cam['keterangan'] ?: 'Tidak ada keterangan tambahan') ?>
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Lokasi -->
                                <td class="py-3.5 px-4">
                                    <span class="text-gray-700 dark:text-slate-300 font-medium">
                                        <?= esc($cam['lokasi'] ?: '-') ?>
                                    </span>
                                </td>

                                <!-- IP Public & Port -->
                                <td class="py-3.5 px-4">
                                    <div class="space-y-0.5 font-mono text-[11px]">
                                        <div class="flex items-center gap-1.5 text-gray-800 dark:text-slate-200 font-semibold">
                                            <i class="ph ph-globe text-blue-500 text-xs"></i>
                                            <span><?= esc($cam['ip_public'] ?: 'Belum diatur') ?></span>
                                            <?php if($cam['port']): ?>
                                                <span class="text-gray-400">:<?= esc($cam['port']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <span class="text-[10px] text-gray-400 dark:text-slate-500 block uppercase">
                                            Stream: <?= esc($cam['tipe_stream']) ?> &bull; <?= esc($cam['resolusi']) ?>
                                        </span>
                                    </div>
                                </td>

                                <!-- Target Program -->
                                <td class="py-3.5 px-4">
                                    <?php if(($cam['target_program'] ?? 'Fullday') === 'Fullday'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Khusus Fullday
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                            Semua Siswa
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Jam Operasional -->
                                <td class="py-3.5 px-4">
                                    <span class="text-gray-600 dark:text-slate-400 font-medium flex items-center gap-1">
                                        <i class="ph ph-clock text-xs"></i>
                                        <?= esc($cam['jadwal_operasional']) ?>
                                    </span>
                                </td>

                                <!-- Status -->
                                <td class="py-3.5 px-4 text-center">
                                    <a 
                                        href="<?= base_url('master-data/cctv/toggle/' . $cam['id']) ?>" 
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold transition-all <?= $cam['status'] === 'aktif' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 hover:bg-emerald-100 border border-emerald-200 dark:border-emerald-800' : ($cam['status'] === 'maintenance' ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 border border-amber-200' : 'bg-gray-100 text-gray-500 dark:bg-slate-700 dark:text-slate-400 border border-gray-200') ?>"
                                        title="Klik untuk ubah status cepat"
                                    >
                                        <span class="w-1.5 h-1.5 rounded-full <?= $cam['status'] === 'aktif' ? 'bg-emerald-500 animate-pulse' : ($cam['status'] === 'maintenance' ? 'bg-amber-500' : 'bg-gray-400') ?>"></span>
                                        <span><?= ucfirst(esc($cam['status'])) ?></span>
                                    </a>
                                </td>

                                <!-- Aksi -->
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <!-- Tombol Preview Live -->
                                        <button 
                                            type="button" 
                                            @click="previewCamera(<?= htmlspecialchars(json_encode($cam), ENT_QUOTES, 'UTF-8') ?>)" 
                                            class="p-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:hover:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 transition-colors" 
                                            title="Uji Pantau / Live Monitor"
                                        >
                                            <i class="ph ph-broadcast text-base"></i>
                                        </button>

                                        <!-- Tombol Edit -->
                                        <button 
                                            type="button" 
                                            @click="$dispatch('open-cctv-modal', { mode: 'edit', camera: <?= htmlspecialchars(json_encode($cam), ENT_QUOTES, 'UTF-8') ?> })" 
                                            class="p-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 text-blue-600 dark:text-blue-400 transition-colors" 
                                            title="Ubah Konfigurasi Kamera"
                                        >
                                            <i class="ph ph-pencil-simple text-base"></i>
                                        </button>

                                        <!-- Tombol Hapus -->
                                        <button 
                                            type="button" 
                                            onclick="confirmAction('Hapus Kamera CCTV?', 'Data kamera <?= esc(addslashes($cam['kode_kamera'])) ?> (<?= esc(addslashes($cam['nama_kamera'])) ?>) akan dihapus secara permanen!', 'Ya, Hapus', () => { window.location.href = '<?= base_url('master-data/cctv/delete/' . $cam['id']) ?>'; })" 
                                            class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 dark:bg-rose-900/30 dark:hover:bg-rose-900/50 text-rose-600 dark:text-rose-400 transition-colors" 
                                            title="Hapus Kamera"
                                        >
                                            <i class="ph ph-trash text-base"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL FORM: TAMBAH & EDIT KAMERA CCTV (Alpine.js) -->
    <div 
        x-show="modalOpen" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs overflow-y-auto"
        @open-cctv-modal.window="openModal($event.detail)"
        @keydown.escape.window="modalOpen = false"
    >
        <div 
            class="relative w-full max-w-2xl bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-gray-100 dark:border-slate-800 my-8 overflow-hidden"
            @click.away="modalOpen = false"
        >
            <!-- Header Modal Seamless -->
            <div class="px-6 sm:px-8 pt-6 pb-4 bg-white dark:bg-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white" x-text="isEdit ? 'Ubah Konfigurasi CCTV' : 'Tambah Perangkat CCTV Baru'"></h3>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Lengkapi alamat IP Public, port transmisi video, dan pengaturan aksesibilitas kamera.</p>
                </div>
                <button type="button" @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-white p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                    <i class="ph ph-x text-xl"></i>
                </button>
            </div>

            <!-- Form Content -->
            <form :action="formAction" method="POST" @submit="isSubmitting = true" class="px-6 sm:px-8 pb-6 sm:pb-8 space-y-4 text-xs">
                <?= csrf_field() ?>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Kode Kamera -->
                    <div>
                        <label class="block font-semibold text-gray-700 dark:text-slate-300 mb-1.5">
                            Kode Kamera <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base">
                                <i class="ph ph-barcode"></i>
                            </span>
                            <input 
                                type="text" 
                                name="kode_kamera" 
                                x-model="formData.kode_kamera" 
                                required 
                                placeholder="Contoh: CAM-05" 
                                class="w-full pl-10 pr-3 py-2.5 rounded-xl bg-gray-50 dark:bg-slate-700/50 border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-blue-500 font-mono text-xs uppercase"
                            >
                        </div>
                    </div>

                    <!-- Nama Kamera -->
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-gray-700 dark:text-slate-300 mb-1.5">
                            Nama Kamera / Area <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base">
                                <i class="ph ph-video-camera"></i>
                            </span>
                            <input 
                                type="text" 
                                name="nama_kamera" 
                                x-model="formData.nama_kamera" 
                                required 
                                placeholder="Contoh: Ruang Bermain Toddler" 
                                class="w-full pl-10 pr-3 py-2.5 rounded-xl bg-gray-50 dark:bg-slate-700/50 border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-blue-500 font-medium text-xs"
                            >
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Lokasi Fisik -->
                    <div>
                        <label class="block font-semibold text-gray-700 dark:text-slate-300 mb-1.5">
                            Lokasi / Ruangan
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base">
                                <i class="ph ph-map-pin"></i>
                            </span>
                            <input 
                                type="text" 
                                name="lokasi" 
                                x-model="formData.lokasi" 
                                placeholder="Contoh: Indoor - Lantai 1" 
                                class="w-full pl-10 pr-3 py-2.5 rounded-xl bg-gray-50 dark:bg-slate-700/50 border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-blue-500 text-xs font-medium"
                            >
                        </div>
                    </div>

                    <!-- Target Program -->
                    <div>
                        <label class="block font-semibold text-gray-700 dark:text-slate-300 mb-1.5">
                            Akses Program Siswa <span class="text-rose-500">*</span>
                        </label>
                        <select 
                            name="target_program" 
                            x-model="formData.target_program" 
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 dark:bg-slate-700/50 border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-blue-500 text-xs font-semibold"
                        >
                            <option value="Fullday">Khusus Orang Tua Siswa Fullday</option>
                            <option value="Semua">Semua Orang Tua (Fullday & Reguler)</option>
                        </select>
                    </div>
                </div>

                <!-- Bagian Konfigurasi Jaringan & IP Public -->
                <div class="p-4 rounded-2xl bg-blue-50/50 dark:bg-slate-900/40 border border-blue-100 dark:border-slate-700 space-y-3">
                    <div class="flex items-center gap-2 text-blue-900 dark:text-blue-300 font-bold">
                        <i class="ph ph-globe text-base"></i>
                        <span>Konfigurasi Jaringan & IP Public</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <!-- IP Public -->
                        <div class="sm:col-span-2">
                            <label class="block font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                IP Public / Hostname CCTV
                            </label>
                            <input 
                                type="text" 
                                name="ip_public" 
                                x-model="formData.ip_public" 
                                placeholder="Contoh: 182.253.120.45 atau cctv.sekolah.id" 
                                class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-800 border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white font-mono text-xs focus:ring-2 focus:ring-blue-500"
                            >
                        </div>

                        <!-- Port -->
                        <div>
                            <label class="block font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Port Layanan
                            </label>
                            <input 
                                type="number" 
                                name="port" 
                                x-model="formData.port" 
                                placeholder="554 / 8080" 
                                class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-800 border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white font-mono text-xs focus:ring-2 focus:ring-blue-500"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <!-- Tipe Stream -->
                        <div>
                            <label class="block font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Tipe Player / Stream
                            </label>
                            <select 
                                name="tipe_stream" 
                                x-model="formData.tipe_stream" 
                                class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-800 border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white text-xs font-medium"
                            >
                                <option value="placeholder">Standby / Placeholder (Belum Pasang)</option>
                                <option value="embed">Embed Web Viewer (iFrame)</option>
                                <option value="hls">HLS Stream (.m3u8)</option>
                                <option value="mjpeg">MJPEG Stream (HTTP)</option>
                                <option value="rtsp">RTSP Stream Protocol</option>
                            </select>
                        </div>

                        <!-- URL Stream / Embed -->
                        <div class="sm:col-span-2">
                            <label class="block font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                URL Video Stream / Web Player (Opsional)
                            </label>
                            <input 
                                type="text" 
                                name="stream_url" 
                                x-model="formData.stream_url" 
                                placeholder="https://ip-public:port/live/ch1.m3u8" 
                                class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-800 border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white font-mono text-xs focus:ring-2 focus:ring-blue-500"
                            >
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <!-- Jam Operasional -->
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-gray-700 dark:text-slate-300 mb-1">
                            Jam Tayang Operasional
                        </label>
                        <input 
                            type="text" 
                            name="jadwal_operasional" 
                            x-model="formData.jadwal_operasional" 
                            placeholder="07.30 - 16.30 WIB" 
                            class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-700/50 border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white text-xs"
                        >
                    </div>

                    <!-- Resolusi -->
                    <div>
                        <label class="block font-semibold text-gray-700 dark:text-slate-300 mb-1">
                            Resolusi
                        </label>
                        <input 
                            type="text" 
                            name="resolusi" 
                            x-model="formData.resolusi" 
                            placeholder="1080p FHD" 
                            class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-700/50 border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white text-xs font-mono"
                        >
                    </div>

                    <!-- Urutan -->
                    <div>
                        <label class="block font-semibold text-gray-700 dark:text-slate-300 mb-1">
                            No. Urut
                        </label>
                        <input 
                            type="number" 
                            name="urutan" 
                            x-model="formData.urutan" 
                            class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-700/50 border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white text-xs font-mono"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <!-- Status -->
                    <div>
                        <label class="block font-semibold text-gray-700 dark:text-slate-300 mb-1">
                            Status Kamera <span class="text-rose-500">*</span>
                        </label>
                        <select 
                            name="status" 
                            x-model="formData.status" 
                            class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-700/50 border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white text-xs font-semibold"
                        >
                            <option value="aktif">Siaran Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                            <option value="maintenance">Maintenance / Perbaikan</option>
                        </select>
                    </div>

                    <!-- Keterangan -->
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-gray-700 dark:text-slate-300 mb-1">
                            Keterangan Tambahan
                        </label>
                        <input 
                            type="text" 
                            name="keterangan" 
                            x-model="formData.keterangan" 
                            placeholder="Catatan sudut pandang kamera atau deskripsi..." 
                            class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-700/50 border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white text-xs"
                        >
                    </div>
                </div>

                <!-- Footer Modal Seamless & Action Buttons -->
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100 dark:border-slate-700/60">
                    <button 
                        type="button" 
                        @click="modalOpen = false" 
                        class="px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-slate-200 font-semibold transition-all"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        :disabled="isSubmitting" 
                        class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-lg shadow-blue-500/30 transition-all flex items-center gap-2 disabled:opacity-50"
                        style="background-color: #2563eb; color: #ffffff;"
                    >
                        <i x-show="!isSubmitting" class="ph ph-floppy-disk text-base text-white"></i>
                        <i x-show="isSubmitting" class="ph ph-spinner animate-spin text-base text-white" style="display: none;"></i>
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Kamera'">Simpan Kamera</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL PREVIEW UJI PANTAU CCTV (Admin) -->
    <div 
        x-show="previewModal" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/85 backdrop-blur-md"
        @keydown.escape.window="previewModal = false"
    >
        <div 
            class="relative w-full max-w-3xl bg-slate-950 rounded-3xl overflow-hidden shadow-2xl border border-slate-800 flex flex-col"
            @click.away="previewModal = false"
        >
            <div class="p-4 bg-slate-900 border-b border-slate-800 flex items-center justify-between text-white">
                <div class="flex items-center gap-3">
                    <span class="font-mono font-bold px-2 py-0.5 rounded bg-blue-600 text-white text-xs" x-text="previewCam?.kode_kamera"></span>
                    <h4 class="text-sm font-bold text-white truncate" x-text="previewCam?.nama_kamera"></h4>
                    <span class="inline-flex items-center gap-1 text-[11px] text-emerald-400 font-semibold ml-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span x-text="previewCam?.status === 'aktif' ? 'Live Ready' : 'Standby / Offline'"></span>
                    </span>
                </div>
                <button @click="previewModal = false" class="text-slate-400 hover:text-white p-1 rounded-lg transition-colors">
                    <i class="ph ph-x text-2xl"></i>
                </button>
            </div>

            <!-- Layar Monitor CCTV -->
            <div class="relative bg-slate-950 aspect-video flex flex-col items-center justify-center text-white p-8 select-none">
                <div class="w-20 h-20 rounded-3xl bg-slate-800 border border-slate-700 flex items-center justify-center text-4xl text-blue-400 mb-4 shadow-xl">
                    <i class="ph ph-video-camera"></i>
                </div>
                <p class="text-lg font-bold text-white" x-text="previewCam?.nama_kamera"></p>
                <div class="flex items-center gap-2 mt-2 font-mono text-xs text-slate-400">
                    <span>IP Public:</span>
                    <span class="text-blue-300 font-bold" x-text="(previewCam?.ip_public || 'Belum diatur') + ':' + (previewCam?.port || '554')"></span>
                </div>
                <p class="text-xs text-slate-400 max-w-md text-center mt-3 leading-relaxed">
                    Uji pantau streaming kamera sekolah. Perangkat fisik mengirimkan sinyal video melalui IP Public yang telah dikonfigurasi.
                </p>
            </div>

            <div class="p-4 bg-slate-900 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
                <div class="space-x-2">
                    <span>Target: <strong class="text-white" x-text="previewCam?.target_program"></strong></span>
                    <span>&bull;</span>
                    <span>Jam: <strong class="text-white" x-text="previewCam?.jadwal_operasional"></strong></span>
                </div>
                <button @click="previewModal = false" class="px-5 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-semibold transition-all">
                    Tutup Preview
                </button>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function cctvManagementData() {
    return {
        modalOpen: false,
        isEdit: false,
        isSubmitting: false,
        formAction: '<?= base_url('master-data/cctv/store') ?>',
        previewModal: false,
        previewCam: null,
        formData: {
            kode_kamera: '',
            nama_kamera: '',
            lokasi: '',
            ip_public: '',
            port: 554,
            stream_url: '',
            tipe_stream: 'placeholder',
            jadwal_operasional: '07.30 - 16.30 WIB',
            resolusi: '1080p FHD',
            target_program: 'Fullday',
            keterangan: '',
            status: 'aktif',
            urutan: 1,
        },
        openModal(detail) {
            this.isSubmitting = false;
            if (detail && detail.mode === 'edit' && detail.camera) {
                this.isEdit = true;
                this.formAction = '<?= base_url('master-data/cctv/update') ?>/' + detail.camera.id;
                this.formData = {
                    kode_kamera: detail.camera.kode_kamera || '',
                    nama_kamera: detail.camera.nama_kamera || '',
                    lokasi: detail.camera.lokasi || '',
                    ip_public: detail.camera.ip_public || '',
                    port: detail.camera.port || 554,
                    stream_url: detail.camera.stream_url || '',
                    tipe_stream: detail.camera.tipe_stream || 'placeholder',
                    jadwal_operasional: detail.camera.jadwal_operasional || '07.30 - 16.30 WIB',
                    resolusi: detail.camera.resolusi || '1080p FHD',
                    target_program: detail.camera.target_program || 'Fullday',
                    keterangan: detail.camera.keterangan || '',
                    status: detail.camera.status || 'aktif',
                    urutan: detail.camera.urutan || 1,
                };
            } else {
                this.isEdit = false;
                this.formAction = '<?= base_url('master-data/cctv/store') ?>';
                this.formData = {
                    kode_kamera: '',
                    nama_kamera: '',
                    lokasi: '',
                    ip_public: '',
                    port: 554,
                    stream_url: '',
                    tipe_stream: 'placeholder',
                    jadwal_operasional: '07.30 - 16.30 WIB',
                    resolusi: '1080p FHD',
                    target_program: 'Fullday',
                    keterangan: '',
                    status: 'aktif',
                    urutan: 1,
                };
            }
            this.modalOpen = true;
        },
        previewCamera(cam) {
            this.previewCam = cam;
            this.previewModal = true;
        }
    };
}
</script>
<?= $this->endSection() ?>

