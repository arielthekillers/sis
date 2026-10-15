<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Keuangan</span></li>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Riwayat Transaksi</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Riwayat Kuitansi Pembayaran
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Daftar seluruh transaksi yang masuk ke kasir. Bisa dicetak ulang atau dibatalkan (void).
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 mb-6">
    <form method="GET" action="<?= base_url('keuangan/riwayat-transaksi') ?>" class="flex flex-col sm:flex-row gap-4 items-end">
        <div class="w-full sm:w-48">
            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Bulan Transaksi</label>
            <select name="bulan" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm">
                <?php foreach($bulan_list as $num => $name): ?>
                    <option value="<?= $num ?>" <?= $bulan_aktif == $num ? 'selected' : '' ?>><?= $name ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="w-full sm:w-32">
            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Tahun</label>
            <input type="number" name="tahun" value="<?= esc($tahun_aktif) ?>" min="2020" max="2100" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm">
        </div>
        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 text-white rounded-xl font-medium shadow-lg shadow-blue-500/30 hover:bg-blue-700 focus:ring-4 focus:ring-blue-500/20 transition-all flex items-center justify-center gap-2">
            <i class="ph ph-funnel text-lg"></i> Tampilkan
        </button>
    </form>
</div>

<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-800">
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider w-16">No</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Tanggal & Kuitansi</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Siswa</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-right">Total Transaksi</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Metode</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                <?php if(empty($riwayat)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-slate-400">
                        <i class="ph ph-files text-4xl mb-3"></i>
                        <p>Tidak ada transaksi pada bulan dan tahun ini.</p>
                    </td>
                </tr>
                <?php endif; ?>

                <?php $no=1; foreach($riwayat as $r): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-slate-400"><?= $no++ ?></td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-gray-900 dark:text-white"><?= esc($r['no_kwitansi']) ?></div>
                        <div class="text-xs text-gray-500 dark:text-slate-400 font-mono mt-0.5"><?= date('d M Y H:i', strtotime($r['tanggal_bayar'])) ?></div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-semibold text-gray-900 dark:text-white"><?= esc($r['nama_lengkap']) ?></div>
                        <div class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">NIS: <?= esc($r['nis'] ?: '-') ?></div>
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-black text-blue-600 dark:text-blue-400">
                        <?= rupiah($r['total_bayar']) ?>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700 dark:bg-slate-700 dark:text-slate-300">
                            <?= esc($r['metode_pembayaran']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="<?= base_url('keuangan/kasir/cetak/'.$r['id']) ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 dark:bg-blue-500/10 dark:hover:bg-blue-500/20 rounded-lg text-sm transition-colors font-medium">
                            <i class="ph ph-printer text-lg"></i> Detail / Cetak
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-800 bg-gray-50/30 dark:bg-slate-800/30">
        <?= $pager->links('transaksi') ?>
    </div>
</div>
<?= $this->endSection() ?>
