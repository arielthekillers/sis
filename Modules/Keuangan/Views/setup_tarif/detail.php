<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><a href="<?= base_url('keuangan/setup-tarif') ?>" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Setup Tarif Siswa</a></li>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Profil: <?= esc($siswa['nama_lengkap']) ?></span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Setup Tarif Tagihan
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Atur nominal spesifik untuk siswa <strong><?= esc($siswa['nama_lengkap']) ?></strong> (NIS: <?= esc($siswa['nis'] ?: '-') ?>).
<?= $this->endSection() ?>

<?= $this->section('page_actions') ?>
<button @click="$dispatch('open-modal')" class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors shadow-sm flex items-center gap-2" <?= empty($available_jenis) ? 'disabled title="Semua jenis tagihan sudah ditambahkan"' : '' ?>>
    <i class="ph ph-plus-circle text-lg"></i>
    Tambah Komponen Tagihan
</button>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden" 
     x-data="{ editModalOpen: false, form: {id: '', nama: '', nominal: ''} }" 
     @edit-modal.window="editModalOpen = true; form = $event.detail;">
    
    <div class="p-5 border-b border-gray-100 dark:border-slate-700/50 bg-gray-50/50 dark:bg-slate-800/50">
        <h3 class="font-bold text-gray-800 dark:text-white">Keranjang Tagihan Aktif</h3>
        <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Komponen biaya ini akan otomatis menjadi invoice *(Faktur)* saat siklus penagihan dijalankan.</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-800">
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Komponen Tagihan</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Siklus</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-right">Nominal Tarif (Rp)</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                <?php if (empty($setup)): ?>
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 dark:bg-slate-700 mb-3 text-gray-400">
                            <i class="ph ph-basket text-2xl"></i>
                        </div>
                        <p class="text-gray-500 dark:text-slate-400 text-sm">Keranjang kosong. Siswa ini belum dikenakan tarif apapun.</p>
                    </td>
                </tr>
                <?php endif; ?>
                
                <?php foreach ($setup as $s): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors group">
                    <td class="px-6 py-4">
                        <span class="text-sm font-semibold text-gray-900 dark:text-white"><?= esc($s['nama_pembayaran']) ?></span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs text-gray-500 dark:text-slate-400"><?= esc($s['tipe_siklus']) ?></span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="text-sm font-mono font-bold text-gray-900 dark:text-white"><?= rupiah($s['nominal']) ?></span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="confirmAction('Hapus dari Keranjang?', 'Siswa tidak akan ditagih lagi untuk komponen ini!', 'Ya, Hapus', () => { window.location.href='<?= base_url('keuangan/setup-tarif/delete/'.$s['id']) ?>' })" class="p-1.5 text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors" title="Hapus">
                                <i class="ph ph-trash text-lg"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<!-- Modal Tambah Komponen -->
<div x-data="{ modalOpen: false, form: {jenis_id: ''} }" @open-modal.window="modalOpen = true; form = {jenis_id: ''};" x-show="modalOpen" class="relative z-50" style="display: none;">
    <div x-show="modalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="modalOpen" @click.away="modalOpen = false" x-transition class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-100 dark:border-slate-700">
                
                <form action="<?= base_url('keuangan/setup-tarif/save') ?>" method="POST" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                    <?= csrf_field() ?>
                    <input type="hidden" name="siswa_id" value="<?= $siswa['id'] ?>">

                    <div class="px-6 py-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-5">Tambah Tarif Siswa</h3>
                        
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Pilih Jenis Tagihan <span class="text-red-500">*</span></label>
                                <select name="jenis_pembayaran_id" x-model="form.jenis_id" required class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all sm:text-sm">
                                    <option value="">-- Pilih --</option>
                                    <?php foreach($available_jenis as $aj): ?>
                                        <option value="<?= $aj['id'] ?>"><?= esc($aj['nama_pembayaran']) ?> (<?= esc($aj['tipe_siklus']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 flex flex-row-reverse gap-3 bg-gray-50 dark:bg-slate-800/50 border-t border-gray-100 dark:border-slate-700">
                        <button type="submit" :disabled="isSubmitting || form.jenis_id === ''" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-emerald-500/30 hover:bg-emerald-700 transition-all disabled:opacity-50">
                            <i class="ph ph-spinner animate-spin text-lg" x-show="isSubmitting" style="display: none;"></i>
                            <span x-text="isSubmitting ? 'Menyimpan...' : 'Tambahkan'"></span>
                        </button>
                        <button type="button" @click="modalOpen = false" class="inline-flex justify-center rounded-xl bg-white dark:bg-slate-700 px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-slate-200 border border-gray-200 dark:border-slate-600 hover:bg-gray-50 dark:hover:bg-slate-600 transition-colors">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
