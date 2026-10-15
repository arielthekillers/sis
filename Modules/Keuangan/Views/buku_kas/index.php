<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Keuangan</span></li>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Buku Kas Umum</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Buku Kas Umum (Laporan Arus Kas)
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Rekapitulasi komprehensif arus kas masuk (pembayaran siswa) dan arus kas keluar (beban operasional sekolah).
<?= $this->endSection() ?>

<?= $this->section('page_actions') ?>
<div class="flex items-center gap-2 print-hide">
    <a href="<?= base_url('keuangan/pengeluaran?bulan='.$bulan_aktif.'&tahun='.$tahun_aktif) ?>" class="bg-gray-100 hover:bg-gray-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-gray-700 dark:text-slate-200 px-4 py-2.5 rounded-xl text-sm font-medium transition-colors border border-gray-200 dark:border-slate-700 flex items-center gap-1.5">
        <i class="ph ph-arrow-circle-up-right text-rose-500 text-base"></i> Kelola Pengeluaran
    </a>
    <button onclick="window.print()" class="bg-blue-600 text-white px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/25 flex items-center gap-2">
        <i class="ph ph-printer text-lg"></i> Cetak Laporan Resmi
    </button>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
@media print {
    @page {
        size: A4 portrait;
        margin: 12mm 15mm;
    }
    body * { visibility: hidden; }
    #print-area, #print-area * { visibility: visible; }
    #print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        color: #000000 !important;
        background: #ffffff !important;
    }
    .print-hide { display: none !important; }
    .print-block { display: block !important; }
    
    /* Tabel Cetak Minimalis & Rapi */
    table {
        width: 100% !important;
        border-collapse: collapse !important;
        font-size: 10.5px !important;
        border: 1px solid #64748b !important;
    }
    th, td {
        border: 1px solid #cbd5e1 !important;
        padding: 5px 8px !important;
        color: #000000 !important;
        background-color: transparent !important;
    }
    thead th {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
        font-weight: 700 !important;
        border-bottom: 2px solid #64748b !important;
        font-size: 10px !important;
    }
    tfoot td {
        background-color: #f8fafc !important;
        border-top: 2px solid #64748b !important;
        font-weight: 700 !important;
    }
    
    /* Hilangkan background kartu, shadow, dan border dekoratif saat print */
    .bg-white, .dark\:bg-slate-800, .rounded-xl, .shadow-sm {
        background: transparent !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        border: none !important;
    }
    .overflow-hidden {
        overflow: visible !important;
    }
}
</style>

<div x-data="{ currentTab: '<?= $active_tab ?>' }">

    <!-- Filter Form -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-5 mb-6 print-hide">
        <form method="GET" action="<?= base_url('keuangan/buku-kas') ?>" class="flex flex-col sm:flex-row gap-4 items-end">
            <div class="w-full sm:w-48">
                <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1.5 uppercase tracking-wider">Bulan Laporan</label>
                <select name="bulan" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm">
                    <?php foreach($bulan_list as $num => $name): ?>
                        <option value="<?= $num ?>" <?= $bulan_aktif == $num ? 'selected' : '' ?>><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="w-full sm:w-32">
                <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1.5 uppercase tracking-wider">Tahun</label>
                <input type="number" name="tahun" value="<?= esc($tahun_aktif) ?>" min="2020" max="2100" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm">
            </div>
            <input type="hidden" name="tab" :value="currentTab">
            <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 text-white rounded-xl font-medium shadow-lg shadow-blue-500/25 hover:bg-blue-700 focus:ring-4 focus:ring-blue-500/20 transition-all flex items-center justify-center gap-2 text-sm">
                <i class="ph ph-funnel text-base"></i> Tampilkan Laporan
            </button>
        </form>
    </div>

    <!-- Area yang Dicetak Saat Window Print -->
    <div id="print-area">
        
        <!-- Header Cetak Resmi Minimalis (Hanya Muncul Saat Print) -->
        <div class="hidden print-block mb-4 text-center border-b-2 border-black pb-3">
            <h1 class="text-lg font-bold uppercase tracking-wider text-black">BUKU KAS UMUM</h1>
            <p class="text-xs text-gray-700 mt-0.5">Laporan Arus Kas Masuk & Keluar Periode: <?= esc($nama_bulan) ?> <?= esc($tahun_aktif) ?></p>
        </div>

        <!-- Ringkasan Finansial Minimalis (Hanya Muncul Saat Print) -->
        <div class="hidden print-block mb-4">
            <table class="w-full text-xs border border-gray-400">
                <thead>
                    <tr class="bg-gray-100 font-semibold text-gray-800 text-center">
                        <th class="w-1/4 p-1.5 border border-gray-300">Saldo Awal Bulan</th>
                        <th class="w-1/4 p-1.5 border border-gray-300">Pemasukan (Kas Masuk)</th>
                        <th class="w-1/4 p-1.5 border border-gray-300">Pengeluaran (Kas Keluar)</th>
                        <th class="w-1/4 p-1.5 border border-gray-300">Saldo Kas Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="font-mono font-bold text-black text-center">
                        <td class="p-1.5 border border-gray-300"><?= rupiah($saldoAwal) ?></td>
                        <td class="p-1.5 border border-gray-300">+ <?= rupiah($totalMasuk) ?></td>
                        <td class="p-1.5 border border-gray-300">- <?= rupiah($totalKeluar) ?></td>
                        <td class="p-1.5 border border-gray-300"><?= rupiah($saldoAkhir) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- 4 Kartu Indikator Finansial (Hanya Tampil di Layar Web, Sembunyikan Saat Print) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6 print-hide">
            <!-- Saldo Awal -->
            <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-slate-800 border-l-4 border-l-slate-400">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Saldo Awal Bulan</p>
                    <i class="ph ph-clock-counter-clockwise text-xl text-gray-400"></i>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= rupiah($saldoAwal) ?></p>
                <p class="text-[11px] text-gray-400 dark:text-slate-500 mt-1">Sisa kas sebelum tanggal 1 <?= esc($nama_bulan) ?></p>
            </div>

            <!-- Total Pemasukan -->
            <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-slate-800 border-l-4 border-l-emerald-500">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Pemasukan (Kas Masuk)</p>
                    <i class="ph ph-arrow-circle-down-right text-xl text-emerald-500"></i>
                </div>
                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">+ <?= rupiah($totalMasuk) ?></p>
                <p class="text-[11px] text-gray-400 dark:text-slate-500 mt-1">Tunai: <?= rupiah($totalTunai) ?> | Bank: <?= rupiah($totalTransfer + $totalQris) ?></p>
            </div>

            <!-- Total Pengeluaran -->
            <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-slate-800 border-l-4 border-l-rose-500">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Pengeluaran (Kas Keluar)</p>
                    <i class="ph ph-arrow-circle-up-right text-xl text-rose-500"></i>
                </div>
                <p class="text-2xl font-bold text-rose-600 dark:text-rose-400">- <?= rupiah($totalKeluar) ?></p>
                <p class="text-[11px] text-gray-400 dark:text-slate-500 mt-1">Operasional, ATK & pemeliharaan</p>
            </div>

            <!-- Saldo Akhir Kas -->
            <?php 
                $surplus = $totalMasuk - $totalKeluar;
                $isSurplus = $surplus >= 0;
            ?>
            <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-slate-800 border-l-4 border-l-blue-600 bg-gradient-to-br from-blue-50/30 to-indigo-50/10 dark:from-slate-800 dark:to-slate-800">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Saldo Kas Akhir</p>
                    <i class="ph ph-vault text-xl text-blue-600 dark:text-blue-400"></i>
                </div>
                <p class="text-2xl font-black text-blue-600 dark:text-blue-400"><?= rupiah($saldoAkhir) ?></p>
                <p class="text-[11px] font-medium <?= $isSurplus ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' ?> mt-1">
                    Arus Kas Bersih: <?= ($isSurplus ? '+' : '') . rupiah($surplus) ?>
                </p>
            </div>
        </div>

        <!-- Navigasi Tab (Rekapitulasi Harian vs Jurnal Detail) -->
        <div class="flex items-center gap-2 mb-4 border-b border-gray-200 dark:border-slate-700 print-hide">
            <button @click="currentTab = 'rekap'" :class="currentTab === 'rekap' ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-500 hover:text-gray-800 dark:text-slate-400 dark:hover:text-slate-200'" class="pb-3 px-3 text-sm flex items-center gap-2 transition-colors">
                <i class="ph ph-calendar-blank text-base"></i>
                <span>Rekapitulasi Arus Kas Harian</span>
            </button>
            <button @click="currentTab = 'detail'" :class="currentTab === 'detail' ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-500 hover:text-gray-800 dark:text-slate-400 dark:hover:text-slate-200'" class="pb-3 px-3 text-sm flex items-center gap-2 transition-colors">
                <i class="ph ph-list-numbers text-base"></i>
                <span>Buku Kas Umum (Jurnal Detail)</span>
            </button>
        </div>

        <!-- TAB 1: REKAPITULASI HARIAN -->
        <div x-show="currentTab === 'rekap'" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-gray-50/50 dark:bg-slate-900/20 print-hide">
                <h3 class="font-bold text-gray-900 dark:text-white">Rekapitulasi Kas Masuk & Keluar per Tanggal</h3>
                <span class="text-xs text-gray-500 dark:text-slate-400">Saldo Awal Bulan: <strong><?= rupiah($saldoAwal) ?></strong></span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/80 dark:bg-slate-800/80 border-b border-gray-100 dark:border-slate-800 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                            <th class="px-6 py-4">Tanggal Transaksi</th>
                            <th class="px-6 py-4 text-right text-emerald-600 dark:text-emerald-400">Kas Masuk (Rp)</th>
                            <th class="px-6 py-4 text-right text-rose-600 dark:text-rose-400">Kas Keluar (Rp)</th>
                            <th class="px-6 py-4 text-right">Net Hari Ini</th>
                            <th class="px-6 py-4 text-right">Saldo Kas Berjalan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-800 text-sm">
                        <!-- Baris Saldo Awal -->
                        <tr class="bg-slate-50/60 dark:bg-slate-900/40 font-semibold italic text-gray-600 dark:text-slate-400">
                            <td class="px-6 py-3.5">
                                <i class="ph ph-flag mr-1"></i> Saldo Awal Bulan (s/d 01 <?= esc($nama_bulan) ?> <?= esc($tahun_aktif) ?>)
                            </td>
                            <td class="px-6 py-3.5 text-right">-</td>
                            <td class="px-6 py-3.5 text-right">-</td>
                            <td class="px-6 py-3.5 text-right">-</td>
                            <td class="px-6 py-3.5 text-right font-bold text-gray-900 dark:text-white font-mono">
                                <?= rupiah($saldoAwal) ?>
                            </td>
                        </tr>

                        <?php if(empty($laporanHarian)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-slate-400">
                                <i class="ph ph-calendar-blank text-4xl mb-3"></i>
                                <p>Tidak ada transaksi kas masuk maupun kas keluar pada bulan ini.</p>
                            </td>
                        </tr>
                        <?php endif; ?>

                        <?php foreach($laporanHarian as $row): 
                            $netHari = $row['pemasukan'] - $row['pengeluaran'];
                        ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                <span class="block"><?= tanggal_indo($row['tanggal']) ?></span>
                                <span class="text-xs text-gray-400 dark:text-slate-500 font-normal">
                                    <?= $row['transaksi_masuk'] ?> penerimaan | <?= $row['transaksi_keluar'] ?> pengeluaran
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-emerald-600 dark:text-emerald-400 font-mono">
                                <?= $row['pemasukan'] > 0 ? rupiah($row['pemasukan']) : '-' ?>
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-rose-600 dark:text-rose-400 font-mono">
                                <?= $row['pengeluaran'] > 0 ? rupiah($row['pengeluaran']) : '-' ?>
                            </td>
                            <td class="px-6 py-4 text-right font-semibold font-mono <?= $netHari >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' ?>">
                                <?= ($netHari >= 0 ? '+' : '') . rupiah($netHari) ?>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-white font-mono bg-gray-50/30 dark:bg-slate-700/10">
                                <?= rupiah($row['saldo_berjalan']) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-100 dark:bg-slate-900 font-bold border-t-2 border-gray-200 dark:border-slate-700 text-sm">
                            <td class="px-6 py-4 uppercase text-gray-900 dark:text-white">Total Mutasi & Saldo Akhir</td>
                            <td class="px-6 py-4 text-right text-emerald-600 dark:text-emerald-400 font-mono text-base">+ <?= rupiah($totalMasuk) ?></td>
                            <td class="px-6 py-4 text-right text-rose-600 dark:text-rose-400 font-mono text-base">- <?= rupiah($totalKeluar) ?></td>
                            <td class="px-6 py-4 text-right font-mono <?= $surplus >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' ?>">
                                <?= ($surplus >= 0 ? '+' : '') . rupiah($surplus) ?>
                            </td>
                            <td class="px-6 py-4 text-right text-blue-600 dark:text-blue-400 font-mono text-lg font-black"><?= rupiah($saldoAkhir) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- TAB 2: JURNAL TRANSAKSI DETAIL (STANDAR BUKU KAS UMUM AKUNTANSI) -->
        <div x-show="currentTab === 'detail'" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-gray-50/50 dark:bg-slate-900/20 print-hide">
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-white">Buku Kas Umum (Jurnal Kronologis Transaksi)</h3>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Daftar seluruh kuitansi masuk dan beban keluar secara terperinci.</p>
                </div>
                <span class="text-xs text-gray-500 dark:text-slate-400">Total Transaksi: <strong><?= count($jurnalDetail) ?></strong></span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50/80 dark:bg-slate-800/80 border-b border-gray-100 dark:border-slate-800 font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                            <th class="px-4 py-3.5 w-10 text-center">No</th>
                            <th class="px-4 py-3.5">Tanggal & Waktu</th>
                            <th class="px-4 py-3.5">No. Bukti / Kuitansi</th>
                            <th class="px-4 py-3.5">Uraian Transaksi</th>
                            <th class="px-4 py-3.5 text-center">Metode</th>
                            <th class="px-4 py-3.5 text-right text-emerald-600 dark:text-emerald-400">Masuk (Debit)</th>
                            <th class="px-4 py-3.5 text-right text-rose-600 dark:text-rose-400">Keluar (Kredit)</th>
                            <th class="px-4 py-3.5 text-right">Saldo Kas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                        <!-- Baris Saldo Awal -->
                        <tr class="bg-slate-50/60 dark:bg-slate-900/40 italic font-semibold text-gray-700 dark:text-slate-300">
                            <td class="px-4 py-3 text-center">-</td>
                            <td class="px-4 py-3">01-<?= $bulan_aktif ?>-<?= $tahun_aktif ?></td>
                            <td class="px-4 py-3 font-mono">-</td>
                            <td class="px-4 py-3 font-bold" colspan="2">Saldo Kas Awal Periode</td>
                            <td class="px-4 py-3 text-right">-</td>
                            <td class="px-4 py-3 text-right">-</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white font-mono"><?= rupiah($saldoAwal) ?></td>
                        </tr>

                        <?php if(empty($jurnalDetail)): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-gray-500 dark:text-slate-400">
                                Tidak ada transaksi yang tercatat di bulan ini.
                            </td>
                        </tr>
                        <?php endif; ?>

                        <?php $no = 1; foreach($jurnalDetail as $j): ?>
                        <tr class="hover:bg-gray-50/70 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="px-4 py-3 text-center text-gray-500 dark:text-slate-400"><?= $no++ ?></td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-600 dark:text-slate-300 font-mono">
                                <?= date('d/m/Y H:i', strtotime($j['waktu'])) ?>
                            </td>
                            <td class="px-4 py-3 font-mono font-semibold <?= $j['tipe'] == 'MASUK' ? 'text-blue-600 dark:text-blue-400' : 'text-rose-600 dark:text-rose-400' ?>">
                                <?= esc($j['kode']) ?>
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-white font-medium max-w-sm">
                                <?= esc($j['keterangan']) ?>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold <?= $j['metode'] == 'Tunai' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' ?>">
                                    <?= esc($j['metode']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-medium text-emerald-600 dark:text-emerald-400">
                                <?= $j['pemasukan'] > 0 ? rupiah($j['pemasukan']) : '-' ?>
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-medium text-rose-600 dark:text-rose-400">
                                <?= $j['pengeluaran'] > 0 ? rupiah($j['pengeluaran']) : '-' ?>
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-gray-900 dark:text-white bg-gray-50/40 dark:bg-slate-800/40">
                                <?= rupiah($j['saldo_berjalan']) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-100 dark:bg-slate-900 font-bold border-t-2 border-gray-200 dark:border-slate-700">
                            <td colspan="5" class="px-4 py-3 text-right uppercase text-gray-900 dark:text-white">Total Arus Kas & Saldo Akhir</td>
                            <td class="px-4 py-3 text-right text-emerald-600 dark:text-emerald-400 font-mono text-sm">+ <?= rupiah($totalMasuk) ?></td>
                            <td class="px-4 py-3 text-right text-rose-600 dark:text-rose-400 font-mono text-sm">- <?= rupiah($totalKeluar) ?></td>
                            <td class="px-4 py-3 text-right text-blue-600 dark:text-blue-400 font-mono text-base font-black"><?= rupiah($saldoAkhir) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Kolom Tanda Tangan Cetak Resmi (Hanya Muncul Saat Print) -->
        <div class="hidden print-block mt-8" style="page-break-inside: avoid;">
            <div class="grid grid-cols-2 gap-8 text-center text-xs">
                <div>
                    <p class="text-gray-800 mb-14">Mengetahui,<br><strong>Kepala Sekolah</strong></p>
                    <p class="font-bold underline text-black">( .................................................... )</p>
                    <p class="text-gray-600">NIP. .............................................</p>
                </div>
                <div>
                    <p class="text-gray-800 mb-14">Tanggal: <?= tanggal_indo(date('Y-m-d')) ?><br><strong>Bendahara Sekolah</strong></p>
                    <p class="font-bold underline text-black">( .................................................... )</p>
                    <p class="text-gray-600">NIP. .............................................</p>
                </div>
            </div>
        </div>

    </div>

</div>
<?= $this->endSection() ?>
