<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Keuangan</span></li>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Daftar Tagihan</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Laporan Daftar Tagihan
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Pantau jumlah tagihan yang telah diterbitkan dan pergerakan pembayarannya.
<?= $this->endSection() ?>

<?= $this->section('page_actions') ?>
<a href="<?= base_url('keuangan/broadcast-tagihan') ?>" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-xs font-semibold transition-all shadow-md shadow-emerald-600/20 active:scale-95">
    <i class="ph ph-whatsapp-logo text-base"></i>
    <span>Rekap & Broadcast Tagihan WA</span>
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div x-data="{ 
    editModalOpen: false, 
    form: { id: '', judul: '', nominal: '' },
    bulanAktif: '<?= $bulan_aktif ?>',
    tahunAktif: '<?= $tahun_aktif ?>',
    async openWaWeb(sId, phone, name) {
        if (!phone) {
            const { value: newPhone } = await Swal.fire({
                title: 'No. WhatsApp Wali Murid',
                text: `Masukkan nomor WhatsApp orang tua/wali dari ${name}:`,
                input: 'text',
                inputValue: '',
                inputPlaceholder: 'Contoh: 081234567890',
                showCancelButton: true,
                confirmButtonText: 'Simpan & Buka WA Web',
                cancelButtonText: 'Batal',
                inputValidator: (val) => (!val || !val.trim()) ? 'Nomor WhatsApp wajib diisi!' : null
            });

            if (newPhone) {
                const formData = new FormData();
                formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                formData.append('siswa_id', sId);
                formData.append('phone', newPhone.trim());
                await fetch('<?= base_url('keuangan/broadcast-tagihan/update-phone') ?>', { method: 'POST', body: formData });
                phone = newPhone.trim();
            } else {
                return;
            }
        }

        Swal.fire({
            title: 'Memformat Tagihan...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        try {
            const res = await fetch(`<?= base_url('keuangan/broadcast-tagihan/get-message') ?>?siswa_id=${sId}&bulan=${this.bulanAktif}&tahun=${this.tahunAktif}`);
            const data = await res.json();
            Swal.close();

            if (data.status === 'success' && data.wa_link) {
                window.open(data.wa_link, '_blank');
            } else {
                Swal.fire('Perhatian', data.message || 'Gagal memformat pesan WhatsApp.', 'warning');
            }
        } catch(e) {
            Swal.fire('Error', 'Kesalahan koneksi: ' + e.message, 'error');
        }
    }
}" @edit-tagihan.window="editModalOpen = true; form = $event.detail;">
<!-- Filter Form -->
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 mb-6">
    <form method="GET" action="<?= base_url('keuangan/daftar-tagihan') ?>" class="flex flex-col sm:flex-row gap-4 items-end">
        <div class="w-full sm:w-48">
            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Bulan Tagihan</label>
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
            <i class="ph ph-funnel text-lg"></i> Filter Data
        </button>
    </form>
</div>

<!-- Statistik Panel -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-slate-800 flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400">
            <i class="ph ph-files text-2xl"></i>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Total Tagihan Terbit</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= number_format($totalTagihan, 0, ',', '.') ?></p>
        </div>
    </div>
    
    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-slate-800 flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
            <i class="ph ph-money text-2xl"></i>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Potensi Pendapatan</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white"><?= rupiah($totalNominal) ?></p>
        </div>
    </div>
    
    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-slate-800 flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400">
            <i class="ph ph-coins text-2xl"></i>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Total Terbayar</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white"><?= rupiah($totalTerbayar) ?></p>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-slate-800 flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-red-50 dark:bg-red-500/10 flex items-center justify-center text-red-600 dark:text-red-400">
            <i class="ph ph-warning-circle text-2xl"></i>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Siswa Belum Bayar</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= number_format($belumBayarCount, 0, ',', '.') ?></p>
        </div>
    </div>
</div>

<!-- Tabel Daftar Tagihan -->
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-900/20">
        <h3 class="font-semibold text-gray-800 dark:text-slate-200">Rincian Tagihan - <?= esc($nama_bulan) ?> <?= esc($tahun_aktif) ?></h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-800">
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Siswa</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Judul Tagihan</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-right">Nominal</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-right">Terbayar</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                <?php if(empty($tagihanList)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-slate-400">
                        <i class="ph ph-receipt text-4xl mb-3"></i>
                        <p>Belum ada tagihan yang diterbitkan pada bulan dan tahun ini.</p>
                        <a href="<?= base_url('keuangan/generate-tagihan') ?>" class="inline-block mt-3 text-blue-600 dark:text-blue-400 font-medium hover:underline">Pergi ke Terbitkan Tagihan</a>
                    </td>
                </tr>
                <?php endif; ?>

                <?php foreach($tagihanList as $t): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="text-sm font-semibold text-gray-900 dark:text-white"><?= esc($t['nama_lengkap']) ?></div>
                        <div class="text-xs text-gray-500 dark:text-slate-400 font-mono mt-0.5">NIS: <?= esc($t['nis'] ?: '-') ?></div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-800 dark:text-slate-200"><?= esc($t['judul_tagihan']) ?></div>
                        <div class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Jatuh Tempo: <?= tanggal_indo($t['jatuh_tempo']) ?></div>
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900 dark:text-white">
                        <?= rupiah($t['nominal_tagihan']) ?>
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-medium <?= $t['nominal_dibayar'] > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-slate-400' ?>">
                        <?= rupiah($t['nominal_dibayar']) ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php 
                            $statusColor = 'bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-400'; // Belum Bayar
                            if($t['status'] == 'Lunas') $statusColor = 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400';
                            if($t['status'] == 'Mencicil') $statusColor = 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-400';
                        ?>
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium <?= $statusColor ?>">
                            <?= esc($t['status']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <div class="flex items-center justify-end gap-1.5">
                            <?php if ($t['status'] != 'Lunas'): ?>
                            <button type="button" @click="openWaWeb(<?= $t['siswa_id'] ?>, '<?= esc($t['no_hp_ortu'] ?? '') ?>', '<?= esc(addslashes($t['nama_lengkap'])) ?>')" class="px-2 py-1 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded-lg transition-colors flex items-center gap-1 text-xs font-semibold shadow-2xs active:scale-95" title="Kirim Pengingat Tagihan ke WhatsApp Web (Akumulasi Tunggakan)">
                                <i class="ph ph-whatsapp-logo text-sm text-emerald-600 dark:text-emerald-400"></i>
                                <span>WA Web</span>
                            </button>
                            <?php endif; ?>
                            <button @click="$dispatch('edit-tagihan', { id: <?= $t['id'] ?>, judul: '<?= addslashes($t['judul_tagihan']) ?>', nominal: <?= $t['nominal_tagihan'] ?> })" class="p-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-lg transition-colors" title="Edit Nominal">
                                <i class="ph ph-pencil-simple text-base"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-800 bg-gray-50/30 dark:bg-slate-800/30">
        <?= $pager->links('tagihan') ?>
    </div>
</div>

<!-- Modal Edit Nominal -->
<div x-show="editModalOpen" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;" x-cloak>
    <div x-show="editModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="editModalOpen" @click.away="editModalOpen = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100 dark:border-slate-700">
                <form action="<?= base_url('keuangan/daftar-tagihan/update-nominal') ?>" method="POST" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                    <?= csrf_field() ?>
                    <input type="hidden" name="tagihan_id" x-model="form.id">
                    
                    <div class="px-6 py-6">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Ubah Nominal Tagihan</h3>
                        <p class="text-sm text-gray-500 dark:text-slate-400 mb-6" x-text="form.judul"></p>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Nominal Baru (Rp)</label>
                            <input type="number" name="nominal_baru" x-model="form.nominal" required min="0" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all font-mono text-lg">
                            <p class="mt-2 text-xs text-gray-500 dark:text-slate-400">
                                <i class="ph ph-info mr-1"></i> Status lunas/mencicil akan otomatis disesuaikan oleh sistem jika nominal berubah.
                            </p>
                        </div>
                    </div>

                    <div class="px-6 py-4 flex flex-row-reverse gap-3 bg-gray-50 dark:bg-slate-800/50 rounded-b-2xl border-t border-gray-100 dark:border-slate-700">
                        <button type="submit" :disabled="isSubmitting" class="inline-flex w-full sm:w-auto justify-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-blue-500/30 hover:bg-blue-700 focus:outline-none transition-all items-center gap-2">
                            <i class="ph ph-spinner animate-spin text-lg" x-show="isSubmitting" style="display: none;"></i>
                            <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                        </button>
                        <button type="button" @click="editModalOpen = false" class="mt-3 sm:mt-0 inline-flex w-full sm:w-auto justify-center rounded-xl bg-white dark:bg-slate-700 px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-slate-200 shadow-sm border border-gray-200 dark:border-slate-600 hover:bg-gray-50 dark:hover:bg-slate-600 focus:outline-none transition-colors">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
<?= $this->endSection() ?>
