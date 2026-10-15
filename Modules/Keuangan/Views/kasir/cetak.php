<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><a href="<?= base_url('keuangan/kasir') ?>" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Kasir Pembayaran</a></li>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Cetak Kuitansi</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Kuitansi Pembayaran
<?= $this->endSection() ?>

<?= $this->section('page_actions') ?>
<button onclick="window.print()" class="bg-blue-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors shadow-sm flex items-center gap-2">
    <i class="ph ph-printer"></i> Cetak PDF
</button>
<button onclick="confirmAction('Batalkan Kuitansi?', 'Semua tagihan dan deposit terkait transaksi ini akan di-rollback ke keadaan semula!', 'Ya, Batalkan', () => { window.location.href='<?= base_url('keuangan/kasir/batal/'.$pembayaran['id']) ?>' })" class="bg-red-50 dark:bg-red-500/10 text-red-600 border border-red-200 dark:border-red-500/20 px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors">
    <i class="ph ph-trash"></i> Batalkan Kuitansi
</button>
<a href="<?= base_url('keuangan/kasir') ?>" class="bg-white dark:bg-slate-700 text-gray-700 dark:text-slate-200 border border-gray-200 dark:border-slate-600 px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-slate-600 transition-colors">
    Kembali ke Kasir
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
@media print {
    body * {
        visibility: hidden;
    }
    #print-area, #print-area * {
        visibility: visible;
    }
    #print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .print-hide {
        display: none !important;
    }
}
</style>

<div class="max-w-3xl mx-auto">
    <div id="print-area" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-8">
        
        <!-- Kop Surat -->
        <div class="flex items-center justify-between border-b-2 border-gray-200 dark:border-slate-700 pb-6 mb-6">
            <div class="flex items-center gap-4">
                <img src="<?= base_url('uploads/logo.png') ?>" alt="RSK APPS" class="h-16 w-auto object-contain">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">RSK APPS</h2>
                    <p class="text-sm font-medium text-rose-500">Rumah Semut Kecil · Preschool & Kindergarten</p>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">rsk.sch.id</p>
                </div>
            </div>
            <div class="text-right">
                <h1 class="text-3xl font-black text-gray-200 dark:text-slate-700 uppercase tracking-wider">Kuitansi</h1>
                <p class="font-semibold text-gray-800 dark:text-slate-200 mt-1"><?= esc($pembayaran['no_kwitansi']) ?></p>
            </div>
        </div>
        
        <!-- Info Pembayaran -->
        <div class="grid grid-cols-2 gap-8 mb-8">
            <div>
                <p class="text-sm text-gray-500 dark:text-slate-400 mb-1">Telah terima dari:</p>
                <p class="font-bold text-gray-900 dark:text-white text-lg"><?= esc($siswa['nama_lengkap']) ?></p>
                <p class="text-sm text-gray-700 dark:text-slate-300">NIS: <?= esc($siswa['nis'] ?: '-') ?> | Kelas: <?= esc($kelas) ?></p>
            </div>
            <div class="text-right">
                <table class="ml-auto text-sm text-gray-600 dark:text-slate-300">
                    <tr>
                        <td class="py-1 pr-4 text-gray-500 dark:text-slate-400">Tanggal:</td>
                        <td class="py-1 font-semibold text-gray-900 dark:text-white"><?= date('d M Y H:i', strtotime($pembayaran['tanggal_bayar'])) ?></td>
                    </tr>
                    <tr>
                        <td class="py-1 pr-4 text-gray-500 dark:text-slate-400">Metode:</td>
                        <td class="py-1 font-semibold text-gray-900 dark:text-white"><?= esc($pembayaran['metode_pembayaran']) ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Tabel Rincian -->
        <table class="w-full text-left border-collapse mb-8">
            <thead>
                <tr class="bg-gray-50 dark:bg-slate-700/50">
                    <th class="py-3 px-4 text-sm font-semibold text-gray-700 dark:text-slate-200 border-y border-gray-200 dark:border-slate-600 w-16">No</th>
                    <th class="py-3 px-4 text-sm font-semibold text-gray-700 dark:text-slate-200 border-y border-gray-200 dark:border-slate-600">Untuk Pembayaran</th>
                    <th class="py-3 px-4 text-sm font-semibold text-gray-700 dark:text-slate-200 border-y border-gray-200 dark:border-slate-600 text-right">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                <?php $no=1; foreach($details as $d): ?>
                <tr>
                    <td class="py-3 px-4 text-sm text-gray-600 dark:text-slate-300"><?= $no++ ?></td>
                    <td class="py-3 px-4 text-sm font-medium text-gray-900 dark:text-white"><?= esc($d['judul_tagihan']) ?></td>
                    <td class="py-3 px-4 text-sm font-semibold text-gray-900 dark:text-white text-right"><?= rupiah($d['nominal_alokasi']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="py-4 px-4 text-right text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-t-2 border-gray-200 dark:border-slate-600">Total Pembayaran</td>
                    <td class="py-4 px-4 text-right text-xl font-black text-blue-600 dark:text-blue-400 border-t-2 border-gray-200 dark:border-slate-600"><?= rupiah($pembayaran['total_bayar']) ?></td>
                </tr>
            </tfoot>
        </table>
        
        <!-- TTD -->
        <div class="flex justify-end mt-12">
            <div class="text-center">
                <p class="text-sm text-gray-500 dark:text-slate-400 mb-16">Petugas / Kasir,</p>
                <div class="w-40 border-b border-gray-400 dark:border-slate-500 mb-1"></div>
                <p class="text-xs text-gray-500 dark:text-slate-400">Admin Keuangan</p>
            </div>
        </div>
        
    </div>
</div>
<?= $this->endSection() ?>
