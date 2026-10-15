<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Keuangan</span></li>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Pengeluaran Kas</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Pengeluaran Kas Operasional
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Catat dan kelola seluruh beban biaya operasional, perlengkapan, dan pengeluaran kas sekolah.
<?= $this->endSection() ?>

<?= $this->section('page_actions') ?>
<div class="flex items-center gap-2">
    <button @click="$dispatch('open-modal-kategori')" class="bg-gray-100 hover:bg-gray-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-gray-700 dark:text-slate-200 px-4 py-2.5 rounded-xl text-sm font-medium transition-colors border border-gray-200 dark:border-slate-700 flex items-center gap-2">
        <i class="ph ph-tag text-base"></i> Kelola Kategori
    </button>
    <button @click="$dispatch('open-modal-pengeluaran', { mode: 'create' })" class="bg-rose-600 hover:bg-rose-700 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition-all shadow-lg shadow-rose-600/30 flex items-center gap-2">
        <i class="ph ph-plus-circle text-lg"></i> Catat Pengeluaran
    </button>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div x-data="{ 
    modalOpen: false, 
    modalKategoriOpen: false,
    previewOpen: false,
    previewSrc: '',
    previewTitle: '',
    isEdit: false,
    form: {
        id: '',
        kode_transaksi: '',
        tanggal: '<?= date('Y-m-d') ?>',
        kategori_id: '',
        nominal: '',
        penerima: '',
        keperluan: '',
        metode_pembayaran: 'Tunai',
        bukti_lama: ''
    }
}"
@open-modal-pengeluaran.window="
    isEdit = ($event.detail.mode === 'edit');
    if (isEdit) {
        form = Object.assign({}, $event.detail.data);
    } else {
        form = {
            id: '',
            kode_transaksi: '',
            tanggal: '<?= date('Y-m-d') ?>',
            kategori_id: '<?= !empty($kategoriList) ? $kategoriList[0]['id'] : '' ?>',
            nominal: '',
            penerima: '',
            keperluan: '',
            metode_pembayaran: 'Tunai',
            bukti_lama: ''
        };
    }
    modalOpen = true;
"
@open-modal-kategori.window="modalKategoriOpen = true;"
@preview-bukti.window="
    previewSrc = $event.detail.url;
    previewTitle = $event.detail.title;
    previewOpen = true;
">

    <!-- Filter Form -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-5 mb-6">
        <form method="GET" action="<?= base_url('keuangan/pengeluaran') ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1.5 uppercase tracking-wider">Bulan</label>
                <select name="bulan" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white focus:bg-white focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 transition-all text-sm">
                    <?php foreach($bulan_list as $num => $name): ?>
                        <option value="<?= $num ?>" <?= $bulan_aktif == $num ? 'selected' : '' ?>><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1.5 uppercase tracking-wider">Tahun</label>
                <input type="number" name="tahun" value="<?= esc($tahun_aktif) ?>" min="2020" max="2100" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white focus:bg-white focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 transition-all text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1.5 uppercase tracking-wider">Filter Kategori</label>
                <select name="kategori_id" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white focus:bg-white focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 transition-all text-sm">
                    <option value="">-- Semua Kategori --</option>
                    <?php foreach($kategoriList as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= $kategori_aktif == $k['id'] ? 'selected' : '' ?>><?= esc($k['nama_kategori']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 bg-rose-600 text-white rounded-xl font-medium shadow-md shadow-rose-600/20 hover:bg-rose-700 focus:ring-4 focus:ring-rose-500/20 transition-all flex items-center justify-center gap-2 text-sm">
                    <i class="ph ph-funnel text-base"></i> Terapkan Filter
                </button>
                <a href="<?= base_url('keuangan/pengeluaran') ?>" class="p-2.5 text-gray-400 hover:text-gray-700 dark:hover:text-white bg-gray-100 dark:bg-slate-700 rounded-xl" title="Reset Filter">
                    <i class="ph ph-arrow-counter-clockwise text-lg"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Statistik Panel -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-slate-800 border-l-4 border-l-rose-500">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Total Kas Keluar</p>
                <div class="w-9 h-9 rounded-lg bg-rose-50 dark:bg-rose-500/10 flex items-center justify-center text-rose-600 dark:text-rose-400">
                    <i class="ph ph-arrow-circle-up-right text-xl"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-rose-600 dark:text-rose-400"><?= rupiah($totalNominal) ?></p>
            <p class="text-xs text-gray-400 dark:text-slate-500 mt-1"><?= $bulan_list[$bulan_aktif] ?> <?= esc($tahun_aktif) ?></p>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-slate-800 border-l-4 border-l-indigo-500">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Jumlah Transaksi</p>
                <div class="w-9 h-9 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                    <i class="ph ph-receipt text-xl"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= number_format($totalTransaksi, 0, ',', '.') ?> <span class="text-sm font-normal text-gray-500">transaksi</span></p>
            <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">Tercatat di sistem</p>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-slate-800 border-l-4 border-l-amber-500">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Pengeluaran Terbesar</p>
                <div class="w-9 h-9 rounded-lg bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400">
                    <i class="ph ph-trend-up text-xl"></i>
                </div>
            </div>
            <p class="text-xl font-bold text-gray-900 dark:text-white"><?= rupiah($maxNominal) ?></p>
            <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">Nominal puncak bulan ini</p>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-slate-800 border-l-4 border-l-emerald-500">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Rata-rata Pengeluaran</p>
                <div class="w-9 h-9 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                    <i class="ph ph-chart-pie text-xl"></i>
                </div>
            </div>
            <p class="text-xl font-bold text-gray-900 dark:text-white">
                <?= rupiah($totalTransaksi > 0 ? ($totalNominal / $totalTransaksi) : 0) ?>
            </p>
            <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">Per transaksi tercatat</p>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-gray-900 dark:text-white">Daftar Transaksi Pengeluaran</h3>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Menampilkan pengeluaran periode <?= $bulan_list[$bulan_aktif] ?> <?= esc($tahun_aktif) ?></p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?= base_url('keuangan/buku-kas?bulan='.$bulan_aktif.'&tahun='.$tahun_aktif) ?>" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                    <i class="ph ph-book-open"></i> Lihat di Buku Kas Umum
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/70 dark:bg-slate-800/70 border-b border-gray-100 dark:border-slate-800 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                        <th class="px-6 py-4 w-12 text-center">No</th>
                        <th class="px-6 py-4">Kode & Tanggal</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Keperluan / Keterangan</th>
                        <th class="px-6 py-4">Penerima / Toko</th>
                        <th class="px-6 py-4 text-right">Nominal</th>
                        <th class="px-6 py-4 text-center">Metode</th>
                        <th class="px-6 py-4 text-center">Bukti Nota</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800 text-sm">
                    <?php if(empty($pengeluaran)): ?>
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-gray-500 dark:text-slate-400">
                            <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-rose-50 dark:bg-rose-500/10 text-rose-500 mb-3">
                                <i class="ph ph-receipt-x text-3xl"></i>
                            </div>
                            <p class="font-medium text-gray-700 dark:text-slate-300">Belum ada catatan pengeluaran</p>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Tidak ada transaksi beban/kas keluar pada filter periode yang dipilih.</p>
                            <button @click="$dispatch('open-modal-pengeluaran', { mode: 'create' })" class="mt-4 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-medium transition-all shadow-sm">
                                + Catat Pengeluaran Sekarang
                            </button>
                        </td>
                    </tr>
                    <?php endif; ?>

                    <?php $no = 1; foreach($pengeluaran as $p): ?>
                    <tr class="hover:bg-gray-50/70 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="px-6 py-4 text-center text-gray-500 dark:text-slate-400 text-xs"><?= $no++ ?></td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-gray-900 dark:text-white font-mono text-xs block"><?= esc($p['kode_transaksi']) ?></span>
                            <span class="text-xs text-gray-500 dark:text-slate-400 mt-0.5 block"><?= tanggal_indo($p['tanggal']) ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400 border border-rose-200/50 dark:border-rose-500/20">
                                <i class="ph ph-tag mr-1 text-xs"></i>
                                <?= esc($p['nama_kategori'] ?: 'Umum') ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-900 dark:text-slate-200 font-medium max-w-xs break-words"><?= esc($p['keperluan']) ?></div>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-slate-300 text-xs">
                            <?= esc($p['penerima'] ?: '-') ?>
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-rose-600 dark:text-rose-400 font-mono text-sm">
                            <?= rupiah($p['nominal']) ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium <?= $p['metode_pembayaran'] == 'Tunai' ? 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400' : 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400' ?>">
                                <?= esc($p['metode_pembayaran']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <?php if(!empty($p['bukti_transaksi'])): 
                                $ext = strtolower(pathinfo($p['bukti_transaksi'], PATHINFO_EXTENSION));
                                $fileUrl = base_url('uploads/pengeluaran/' . $p['bukti_transaksi']);
                            ?>
                                <?php if($ext === 'pdf'): ?>
                                    <a href="<?= $fileUrl ?>" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-50 text-red-600 hover:bg-red-100 dark:bg-red-500/10 dark:hover:bg-red-500/20 rounded-lg text-xs font-medium transition-colors" title="Buka PDF">
                                        <i class="ph ph-file-pdf text-base"></i> Dokumen PDF
                                    </a>
                                <?php else: ?>
                                    <button @click="$dispatch('preview-bukti', { url: '<?= $fileUrl ?>', title: '<?= addslashes($p['kode_transaksi']) ?> - <?= addslashes($p['keperluan']) ?>' })" class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-slate-700 dark:text-slate-300 dark:hover:bg-slate-600 rounded-lg text-xs font-medium transition-colors" title="Lihat Foto Bukti">
                                        <i class="ph ph-image text-base"></i> Foto Bukti
                                    </button>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-xs text-gray-400 italic">Tanpa Bukti</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <button @click="$dispatch('open-modal-pengeluaran', {
                                    mode: 'edit',
                                    data: {
                                        id: '<?= $p['id'] ?>',
                                        kode_transaksi: '<?= esc($p['kode_transaksi']) ?>',
                                        tanggal: '<?= $p['tanggal'] ?>',
                                        kategori_id: '<?= $p['kategori_id'] ?>',
                                        nominal: '<?= (float)$p['nominal'] ?>',
                                        penerima: '<?= addslashes($p['penerima'] ?? '') ?>',
                                        keperluan: '<?= addslashes($p['keperluan']) ?>',
                                        metode_pembayaran: '<?= $p['metode_pembayaran'] ?>',
                                        bukti_lama: '<?= $p['bukti_transaksi'] ?? '' ?>'
                                    }
                                })" class="p-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-lg transition-colors" title="Edit Transaksi">
                                    <i class="ph ph-pencil-simple text-base"></i>
                                </button>
                                <button onclick="confirmAction('Hapus Transaksi?', 'Data pengeluaran <?= esc($p['kode_transaksi']) ?> senilai <?= rupiah($p['nominal']) ?> akan dihapus permanen!', 'Ya, Hapus', () => { window.location.href='<?= base_url('keuangan/pengeluaran/delete/'.$p['id']) ?>' })" class="p-1.5 text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors" title="Hapus Transaksi">
                                    <i class="ph ph-trash text-base"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-800 bg-gray-50/40 dark:bg-slate-800/40">
            <?= $pager->links('pengeluaran') ?>
        </div>
    </div>

    <!-- Breakdown per Kategori -->
    <?php if(!empty($breakdown)): ?>
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 mb-8">
        <h4 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
            <i class="ph ph-chart-donut text-rose-500 text-lg"></i>
            Rekap Pengeluaran Berdasarkan Kategori (<?= $bulan_list[$bulan_aktif] ?> <?= esc($tahun_aktif) ?>)
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach($breakdown as $b): 
                $pct = $totalNominal > 0 ? round(($b['total'] / $totalNominal) * 100, 1) : 0;
            ?>
            <div class="p-4 rounded-xl bg-gray-50 dark:bg-slate-700/40 border border-gray-100 dark:border-slate-700">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-sm font-semibold text-gray-800 dark:text-slate-200"><?= esc($b['nama_kategori'] ?: 'Umum') ?></span>
                    <span class="text-xs font-bold text-rose-600 dark:text-rose-400"><?= $pct ?>%</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-slate-600 h-2 rounded-full overflow-hidden mb-2">
                    <div class="bg-rose-500 h-2 rounded-full" style="width: <?= $pct ?>%"></div>
                </div>
                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-slate-400">
                    <span><?= number_format($b['count'], 0, ',', '.') ?> transaksi</span>
                    <span class="font-bold text-gray-800 dark:text-slate-200"><?= rupiah($b['total']) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- MODAL 1: Form Catat / Edit Pengeluaran (Design Standar Premium) -->
    <div x-show="modalOpen" class="relative z-50" aria-labelledby="modal-pengeluaran" role="dialog" aria-modal="true" style="display: none;" x-cloak>
        <div x-show="modalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="modalOpen" @click.away="modalOpen = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-gray-100 dark:border-slate-700">
                    
                    <form action="<?= base_url('keuangan/pengeluaran/save') ?>" method="POST" enctype="multipart/form-data" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" x-model="form.id">

                        <!-- Modal Header Seamless -->
                        <div class="px-6 pt-6 pb-2">
                            <div class="flex items-center justify-between">
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white" x-text="isEdit ? 'Ubah Pengeluaran Kas' : 'Catat Pengeluaran Kas'"></h3>
                                <button type="button" @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors">
                                    <i class="ph ph-x text-2xl"></i>
                                </button>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">
                                Pastikan rincian belanja dan nominal sesuai dengan nota atau kuitansi fisik.
                            </p>
                        </div>

                        <!-- Modal Body -->
                        <div class="px-6 py-4 space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Tanggal -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5 uppercase">Tanggal Pengeluaran <span class="text-rose-500">*</span></label>
                                    <div class="relative">
                                        <i class="ph ph-calendar text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 text-lg"></i>
                                        <input type="date" name="tanggal" x-model="form.tanggal" required class="block w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white focus:bg-white focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 transition-all text-sm">
                                    </div>
                                </div>

                                <!-- Kategori -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5 uppercase">Kategori Beban <span class="text-rose-500">*</span></label>
                                    <div class="relative">
                                        <i class="ph ph-tag text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 text-lg"></i>
                                        <select name="kategori_id" x-model="form.kategori_id" required class="block w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white focus:bg-white focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 transition-all text-sm">
                                            <option value="">-- Pilih Kategori --</option>
                                            <?php foreach($kategoriList as $k): ?>
                                                <option value="<?= $k['id'] ?>"><?= esc($k['nama_kategori']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Nominal -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5 uppercase">Nominal Pengeluaran (Rp) <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-gray-400 text-sm">Rp</span>
                                    <input type="number" name="nominal" x-model="form.nominal" required min="100" placeholder="0" class="block w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white focus:bg-white focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 transition-all font-mono text-lg font-bold">
                                </div>
                            </div>

                            <!-- Keperluan / Uraian -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5 uppercase">Keperluan / Uraian Belanja <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <textarea name="keperluan" x-model="form.keperluan" rows="2" required placeholder="Contoh: Pembelian 5 rim kertas A4 dan tinta printer admin..." class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white focus:bg-white focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 transition-all text-sm"></textarea>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Penerima / Toko -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5 uppercase">Penerima / Toko / Vendor</label>
                                    <div class="relative">
                                        <i class="ph ph-storefront text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 text-lg"></i>
                                        <input type="text" name="penerima" x-model="form.penerima" placeholder="Nama toko / penanggung jawab" class="block w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white focus:bg-white focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 transition-all text-sm">
                                    </div>
                                </div>

                                <!-- Metode Pembayaran -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5 uppercase">Metode Pembayaran</label>
                                    <div class="relative">
                                        <i class="ph ph-wallet text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 text-lg"></i>
                                        <select name="metode_pembayaran" x-model="form.metode_pembayaran" class="block w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white focus:bg-white focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 transition-all text-sm">
                                            <option value="Tunai">Kas Tunai</option>
                                            <option value="Transfer Bank">Transfer Bank</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Upload Bukti Nota/Struk -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5 uppercase">Bukti Nota / Struk Fisik (Opsional)</label>
                                <div class="flex items-center gap-3">
                                    <input type="file" name="bukti_transaksi" accept="image/*,.pdf" class="block w-full text-xs text-gray-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-700 dark:file:bg-rose-500/10 dark:file:text-rose-300 hover:file:bg-rose-100 transition-colors">
                                </div>
                                <p class="text-[11px] text-gray-400 dark:text-slate-500 mt-1">Format didukung: JPG, PNG, WebP, atau PDF (maks. 5MB).</p>
                                <template x-if="form.bukti_lama">
                                    <p class="text-[11px] text-emerald-600 dark:text-emerald-400 mt-1 flex items-center gap-1">
                                        <i class="ph ph-check-circle"></i> Sudah memiliki file bukti terlampir. Biarkan kosong jika tidak ingin mengubah.
                                    </p>
                                </template>
                            </div>
                        </div>

                        <!-- Modal Footer Seamless -->
                        <div class="px-6 py-4 flex flex-row-reverse gap-3 bg-gray-50/70 dark:bg-slate-800/70 rounded-b-2xl border-t border-gray-100 dark:border-slate-700">
                            <button type="submit" :disabled="isSubmitting" class="inline-flex w-full sm:w-auto justify-center rounded-xl bg-rose-600 px-6 py-2.5 text-sm font-medium text-white shadow-lg shadow-rose-600/30 hover:bg-rose-700 focus:outline-none transition-all items-center gap-2">
                                <i class="ph ph-spinner animate-spin text-lg" x-show="isSubmitting" style="display: none;"></i>
                                <span x-text="isSubmitting ? 'Menyimpan...' : (isEdit ? 'Simpan Perubahan' : 'Catat Pengeluaran')"></span>
                            </button>
                            <button type="button" @click="modalOpen = false" class="mt-3 sm:mt-0 inline-flex w-full sm:w-auto justify-center rounded-xl bg-white dark:bg-slate-700 px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-slate-200 shadow-sm border border-gray-200 dark:border-slate-600 hover:bg-gray-50 dark:hover:bg-slate-600 focus:outline-none transition-colors">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 2: Kelola Kategori Pengeluaran -->
    <div x-show="modalKategoriOpen" class="relative z-50" role="dialog" aria-modal="true" style="display: none;" x-cloak>
        <div x-show="modalKategoriOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="modalKategoriOpen" @click.away="modalKategoriOpen = false" x-transition class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100 dark:border-slate-700 p-6">
                    
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Kategori Pengeluaran</h3>
                            <p class="text-xs text-gray-500 dark:text-slate-400">Atur kategori akun beban untuk pengelompokan laporan.</p>
                        </div>
                        <button type="button" @click="modalKategoriOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-white">
                            <i class="ph ph-x text-2xl"></i>
                        </button>
                    </div>

                    <!-- Form Tambah Kategori Cepat -->
                    <form action="<?= base_url('keuangan/pengeluaran/kategori/save') ?>" method="POST" class="mb-5 p-4 rounded-xl bg-gray-50 dark:bg-slate-700/40 border border-gray-100 dark:border-slate-700">
                        <?= csrf_field() ?>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1 uppercase">Nama Kategori Baru</label>
                                <input type="text" name="nama_kategori" required placeholder="Contoh: Transportasi & Perjalanan Dinas" class="block w-full px-3 py-2 text-sm bg-white dark:bg-slate-800 border-gray-200 dark:border-slate-700 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-rose-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1 uppercase">Deskripsi (Opsional)</label>
                                <input type="text" name="deskripsi" placeholder="Keterangan singkat kategori..." class="block w-full px-3 py-2 text-sm bg-white dark:bg-slate-800 border-gray-200 dark:border-slate-700 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-rose-500">
                            </div>
                            <button type="submit" class="w-full py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-semibold transition-colors flex items-center justify-center gap-1.5 shadow-sm">
                                <i class="ph ph-plus-circle text-base"></i> Simpan Kategori Baru
                            </button>
                        </div>
                    </form>

                    <!-- Daftar Kategori Existing -->
                    <div class="space-y-2 max-h-60 overflow-y-auto custom-scrollbar pr-1">
                        <p class="text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-2">Kategori Terdaftar</p>
                        <?php foreach($kategoriList as $kat): ?>
                        <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50/50 dark:bg-slate-700/30 border border-gray-100 dark:border-slate-700/50">
                            <div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-slate-200"><?= esc($kat['nama_kategori']) ?></p>
                                <?php if($kat['deskripsi']): ?>
                                    <p class="text-xs text-gray-400 dark:text-slate-400"><?= esc($kat['deskripsi']) ?></p>
                                <?php endif; ?>
                            </div>
                            <button onclick="confirmAction('Hapus Kategori?', 'Kategori <?= esc($kat['nama_kategori']) ?> akan dihapus bila tidak ada pengeluaran terkait.', 'Hapus', () => { window.location.href='<?= base_url('keuangan/pengeluaran/kategori/delete/'.$kat['id']) ?>' })" class="text-gray-400 hover:text-red-500 p-1 rounded transition-colors" title="Hapus Kategori">
                                <i class="ph ph-trash text-base"></i>
                            </button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 3: Foto Bukti Transaksi Preview -->
    <div x-show="previewOpen" class="relative z-50" role="dialog" aria-modal="true" style="display: none;" x-cloak>
        <div x-show="previewOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity" @click="previewOpen = false"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="previewOpen" class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-100 dark:border-slate-700 p-6">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-slate-700 mb-4">
                        <h4 class="font-bold text-gray-900 dark:text-white truncate" x-text="previewTitle"></h4>
                        <button type="button" @click="previewOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-white">
                            <i class="ph ph-x text-2xl"></i>
                        </button>
                    </div>
                    <div class="flex justify-center items-center bg-gray-100 dark:bg-slate-900 rounded-xl overflow-hidden max-h-[70vh]">
                        <img :src="previewSrc" alt="Bukti Transaksi" class="object-contain max-h-[70vh] w-auto">
                    </div>
                    <div class="mt-4 flex justify-between items-center text-xs text-gray-500 dark:text-slate-400">
                        <span>Klik kanan gambar jika ingin menyimpan salinan berkas.</span>
                        <a :href="previewSrc" target="_blank" class="text-blue-600 dark:text-blue-400 font-semibold hover:underline flex items-center gap-1">
                            Buka Ukuran Penuh <i class="ph ph-arrow-square-out"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
