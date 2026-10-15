<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Keuangan & Pembayaran</span></li>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Master Jenis Tagihan</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Kamus Jenis Tagihan
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Mengelola komponen tarif yang akan dibebankan kepada siswa (contoh: SPP, Uang Gedung, dll).
<?= $this->endSection() ?>

<?= $this->section('page_actions') ?>
<button @click="$dispatch('open-modal')" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors shadow-sm flex items-center gap-2">
    <i class="ph ph-plus-circle text-lg"></i>
    Tambah Jenis Tagihan
</button>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div x-data="{ modalOpen: false, editMode: false, form: {id: '', nama_pembayaran: '', tipe_siklus: 'Bulanan', is_active: true} }" 
     @open-modal.window="modalOpen = true; editMode = false; form = {id: '', nama_pembayaran: '', tipe_siklus: 'Bulanan', is_active: true};" 
     @edit-modal.window="modalOpen = true; editMode = true; form = $event.detail; form.is_active = (form.is_active == 1 || form.is_active == true);">
    
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-800">
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Nama Pembayaran/Tagihan</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-right">Nominal (Rp)</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Siklus Penagihan</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    <?php if (empty($jenis)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-slate-400">
                            Belum ada master tagihan.
                        </td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php foreach ($jenis as $j): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors group">
                        <td class="px-6 py-4">
                            <span class="text-sm font-semibold text-gray-900 dark:text-white"><?= esc($j['nama_pembayaran']) ?></span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-sm font-mono font-bold text-gray-900 dark:text-white"><?= rupiah($j['nominal']) ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <?php if($j['tipe_siklus'] == 'Bulanan'): ?>
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 border border-blue-100 dark:border-blue-800/30">
                                    <i class="ph ph-calendar-blank"></i> Rutin Bulanan
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-xs font-medium bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 border border-amber-100 dark:border-amber-800/30">
                                    <i class="ph ph-lightning"></i> Sekali Bayar / Bebas
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if($j['is_active']): ?>
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400">
                                    Aktif
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-400">
                                    Tidak Aktif
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <?php
                                    $modalData = [
                                        'id' => $j['id'],
                                        'nama_pembayaran' => $j['nama_pembayaran'],
                                        'nominal' => number_format($j['nominal'], 0, ',', '.'),
                                        'tipe_siklus' => $j['tipe_siklus'],
                                        'is_active' => $j['is_active']
                                    ];
                                ?>
                                <button @click="$dispatch('edit-modal', <?= htmlspecialchars(json_encode($modalData), ENT_QUOTES, 'UTF-8') ?>)" class="p-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-lg transition-colors" title="Edit">
                                    <i class="ph ph-pencil-simple text-lg"></i>
                                </button>
                                <button onclick="confirmAction('Hapus Data?', 'Data tidak bisa dikembalikan!', 'Ya, Hapus', () => { window.location.href='<?= base_url('keuangan/jenis-pembayaran/delete/'.$j['id']) ?>' })" class="p-1.5 text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors" title="Hapus">
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

    <!-- Modal Form -->
    <div x-show="modalOpen" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div x-show="modalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="modalOpen" 
                     @click.away="modalOpen = false"
                     x-transition
                     class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100 dark:border-slate-700">
                    
                    <form action="<?= base_url('keuangan/jenis-pembayaran/save') ?>" method="POST" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" x-model="form.id">

                        <div class="px-6 py-6">
                            <div class="mb-6">
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white" id="modal-title" x-text="editMode ? 'Edit Jenis Tagihan' : 'Tambah Jenis Tagihan'"></h3>
                            </div>
                            
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Nama Tagihan <span class="text-red-500">*</span></label>
                                    <input type="text" name="nama_pembayaran" x-model="form.nama_pembayaran" placeholder="Contoh: SPP Reguler" required class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Nominal Tarif (Rp) <span class="text-red-500">*</span></label>
                                    <input type="text" name="nominal" x-model="form.nominal" x-on:input="form.nominal = form.nominal.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')" required class="block w-full px-4 py-3 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white font-mono text-lg focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Siklus Penagihan</label>
                                    <select name="tipe_siklus" x-model="form.tipe_siklus" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm">
                                        <option value="Bulanan">Rutin Bulanan (Tiap Bulan)</option>
                                        <option value="Sekali Bayar">Sekali Bayar (Insidental)</option>
                                    </select>
                                </div>
                                
                                <div class="pt-2 border-t border-gray-100 dark:border-slate-700">
                                    <label class="flex items-center cursor-pointer">
                                        <div class="relative">
                                            <input type="checkbox" name="is_active" class="sr-only" x-model="form.is_active" value="1">
                                            <div class="block w-10 h-6 rounded-full transition-colors duration-300" :class="form.is_active ? 'bg-blue-500' : 'bg-gray-200 dark:bg-slate-700'"></div>
                                            <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform duration-300 shadow-sm" :class="form.is_active ? 'translate-x-4' : 'translate-x-0'"></div>
                                        </div>
                                        <div class="ml-3 text-sm font-medium text-gray-700 dark:text-slate-300">
                                            Tagihan Masih Berlaku (Aktif)
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-4 flex flex-row-reverse gap-3 bg-gray-50 dark:bg-slate-800/50 border-t border-gray-100 dark:border-slate-700">
                            <button type="submit" :disabled="isSubmitting" class="inline-flex w-full sm:w-auto justify-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-blue-500/30 hover:bg-blue-700 transition-all items-center gap-2">
                                <i class="ph ph-spinner animate-spin text-lg" x-show="isSubmitting" style="display: none;"></i>
                                <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan'"></span>
                            </button>
                            <button type="button" @click="modalOpen = false" class="mt-3 sm:mt-0 inline-flex w-full sm:w-auto justify-center rounded-xl bg-white dark:bg-slate-700 px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-slate-200 border border-gray-200 dark:border-slate-600 hover:bg-gray-50 dark:hover:bg-slate-600 transition-colors">
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
