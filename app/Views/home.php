<?= $this->extend('layout/main') ?>

<?= $this->section('page_title') ?>
Dashboard SIS
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Ringkasan performa sekolah dan aktivitas keuangan hari ini.
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Dashboard</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_actions') ?>
<a href="<?= base_url('keuangan/kasir') ?>" class="flex items-center gap-2 bg-blue-600 border border-transparent text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors shadow-sm">
    <i class="ph ph-money text-lg"></i>
    <span>Buka Kasir</span>
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-6">
    <!-- Card 1: Siswa -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-100 dark:border-slate-800 p-5 shadow-sm transition-colors hover:border-blue-500/30">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-gray-500 dark:text-slate-400">Total Siswa Aktif</h3>
            <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400">
                <i class="ph ph-users-three text-xl"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= number_format($totalSiswa, 0, ',', '.') ?> <span class="text-sm font-normal text-gray-500">Siswa</span></p>
        <div class="flex items-center gap-1 mt-2 text-sm">
            <i class="ph ph-check-circle text-blue-500"></i>
            <span class="text-blue-500 font-medium">Tahun Ajaran Ini</span>
        </div>
    </div>
    
    <!-- Card 2: Pemasukan Kasir -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-100 dark:border-slate-800 p-5 shadow-sm transition-colors hover:border-emerald-500/30">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-gray-500 dark:text-slate-400">Kas Masuk (Bulan Ini)</h3>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                <i class="ph ph-arrow-circle-down-right text-xl"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400"><?= rupiah($totalPendapatan) ?></p>
        <div class="flex items-center gap-1 mt-2 text-sm">
            <a href="<?= base_url('keuangan/buku-kas') ?>" class="text-emerald-600 dark:text-emerald-400 font-medium hover:underline flex items-center gap-1">
                Buku Kas <i class="ph ph-arrow-right"></i>
            </a>
        </div>
    </div>

    <!-- Card 3: Pengeluaran Kas -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-100 dark:border-slate-800 p-5 shadow-sm transition-colors hover:border-rose-500/30">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-gray-500 dark:text-slate-400">Kas Keluar (Bulan Ini)</h3>
            <div class="w-10 h-10 rounded-lg bg-rose-50 dark:bg-rose-500/10 flex items-center justify-center text-rose-600 dark:text-rose-400">
                <i class="ph ph-arrow-circle-up-right text-xl"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-rose-600 dark:text-rose-400"><?= rupiah($totalPengeluaran) ?></p>
        <div class="flex items-center gap-1 mt-2 text-sm">
            <a href="<?= base_url('keuangan/pengeluaran') ?>" class="text-rose-600 dark:text-rose-400 font-medium hover:underline flex items-center gap-1">
                Pengeluaran <i class="ph ph-arrow-right"></i>
            </a>
        </div>
    </div>

    <!-- Card 4: Tunggakan -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-100 dark:border-slate-800 p-5 shadow-sm transition-colors hover:border-orange-500/30">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-gray-500 dark:text-slate-400">Tunggakan Tagihan</h3>
            <div class="w-10 h-10 rounded-lg bg-orange-50 dark:bg-orange-500/10 flex items-center justify-center text-orange-600 dark:text-orange-400">
                <i class="ph ph-warning-circle text-xl"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= rupiah($totalTunggakan) ?></p>
        <div class="flex items-center gap-1 mt-2 text-sm">
            <a href="<?= base_url('keuangan/daftar-tagihan') ?>" class="text-orange-600 dark:text-orange-400 font-medium hover:underline flex items-center gap-1">
                Daftar Tagihan <i class="ph ph-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Tabel Transaksi Terbaru -->
    <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 transition-colors overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-700/50 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="ph ph-receipt text-blue-600 dark:text-blue-400"></i> Transaksi Kasir Terbaru
            </h3>
            <a href="<?= base_url('keuangan/riwayat-transaksi') ?>" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">Lihat Semua</a>
        </div>
        <div class="p-0">
            <?php if(empty($transaksiTerbaru)): ?>
                <div class="text-center py-10">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 dark:bg-slate-800/50 mb-4">
                        <i class="ph ph-receipt text-3xl text-gray-400 dark:text-slate-500"></i>
                    </div>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">Belum ada transaksi</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Transaksi kasir akan muncul di sini.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50/50 dark:bg-slate-800/50">
                            <tr>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">Waktu</th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">Siswa</th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider text-right">Nominal</th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider text-center">Metode</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700/50">
                            <?php foreach($transaksiTerbaru as $t): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                                <td class="px-6 py-3 text-sm text-gray-500 dark:text-slate-400">
                                    <?= date('d M, H:i', strtotime($t['tanggal_bayar'])) ?>
                                </td>
                                <td class="px-6 py-3 font-medium text-gray-900 dark:text-white">
                                    <?= esc($t['nama_lengkap']) ?>
                                    <div class="text-xs text-gray-400 font-normal"><?= esc($t['no_kwitansi']) ?></div>
                                </td>
                                <td class="px-6 py-3 text-right font-bold text-blue-600 dark:text-blue-400">
                                    <?= rupiah($t['total_bayar']) ?>
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700 dark:bg-slate-700 dark:text-slate-300">
                                        <?= esc($t['metode_pembayaran']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Info Singkat Sidebar -->
    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl shadow-lg border border-transparent transition-colors overflow-hidden text-white p-6 relative">
        <div class="absolute -right-6 -top-6 text-white/10">
            <i class="ph ph-graduation-cap" style="font-size: 150px;"></i>
        </div>
        <div class="relative z-10">
            <h3 class="text-xl font-bold mb-2">Sistem Informasi Sekolah</h3>
            <p class="text-blue-100 text-sm mb-6 leading-relaxed">Kelola semua aktivitas sekolah dari satu pintu. Data master, akademik, dan keuangan yang terintegrasi penuh.</p>
            
            <div class="space-y-3">
                <a href="<?= base_url('civitas-akademika/siswa') ?>" class="flex items-center gap-3 p-3 bg-white/10 hover:bg-white/20 rounded-lg transition-colors backdrop-blur-sm">
                    <i class="ph ph-student text-2xl"></i>
                    <div>
                        <div class="text-sm font-semibold">Data Siswa</div>
                        <div class="text-xs text-blue-200">Kelola master siswa</div>
                    </div>
                </a>
                <a href="<?= base_url('keuangan/kasir') ?>" class="flex items-center gap-3 p-3 bg-white/10 hover:bg-white/20 rounded-lg transition-colors backdrop-blur-sm">
                    <i class="ph ph-money text-2xl"></i>
                    <div>
                        <div class="text-sm font-semibold">Kasir Keuangan</div>
                        <div class="text-xs text-blue-200">Terima pembayaran SPP</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
