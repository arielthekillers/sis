<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Master Data</span></li>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Mata Pelajaran</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Kelola Mata Pelajaran
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Manajemen data referensi mata pelajaran yang diajarkan di sekolah.
<?= $this->endSection() ?>

<?= $this->section('page_actions') ?>
<button @click="$dispatch('open-modal')" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors shadow-sm flex items-center gap-2">
    <i class="ph ph-plus-circle text-lg"></i>
    Tambah Mapel
</button>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div x-data="{ modalOpen: false, editMode: false, form: {id: '', kode_mapel: '', nama_mapel: '', kelompok: ''} }" @open-modal.window="modalOpen = true; editMode = false; form = {id: '', kode_mapel: '', nama_mapel: '', kelompok: 'A (Muatan Nasional)'};" @edit-modal.window="modalOpen = true; editMode = true; form = $event.detail;">
    
    <!-- Tabel Data -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-800">
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider w-32">Kode Mapel</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Nama Mata Pelajaran</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Kelompok</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    <?php if (empty($mata_pelajaran)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-slate-400">
                            Belum ada data mata pelajaran.
                        </td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php foreach ($mata_pelajaran as $m): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors group">
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-slate-700 dark:text-slate-300 border border-gray-200 dark:border-slate-600 font-mono tracking-wider">
                                <?= esc($m['kode_mapel']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-semibold text-gray-900 dark:text-white"><?= esc($m['nama_mapel']) ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-500 dark:text-slate-400"><?= esc($m['kelompok'] ?: '-') ?></span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="$dispatch('edit-modal', {id: '<?= $m['id'] ?>', kode_mapel: '<?= esc($m['kode_mapel']) ?>', nama_mapel: '<?= esc($m['nama_mapel']) ?>', kelompok: '<?= esc($m['kelompok']) ?>'})" class="p-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-lg transition-colors" title="Edit">
                                    <i class="ph ph-pencil-simple text-lg"></i>
                                </button>
                                <button onclick="confirmAction('Hapus Mapel?', 'Data yang dihapus tidak bisa dikembalikan!', 'Ya, Hapus', () => { window.location.href='<?= base_url('master-data/mata-pelajaran/delete/'.$m['id']) ?>' })" class="p-1.5 text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors" title="Hapus">
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

    <!-- Modal Form Alpine.js -->
    <div x-show="modalOpen" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <!-- Backdrop -->
        <div x-show="modalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <!-- Panel Modal -->
                <div x-show="modalOpen" 
                     @click.away="modalOpen = false"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100 dark:border-slate-700">
                    
                    <form action="<?= base_url('master-data/mata-pelajaran/save') ?>" method="POST" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" x-model="form.id">

                        <div class="px-6 py-6">
                            <!-- Header Seamless -->
                            <div class="mb-6">
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white" id="modal-title" x-text="editMode ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran'"></h3>
                            </div>
                            
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Kode Mapel <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="ph ph-hash text-gray-400 text-lg"></i>
                                        </div>
                                        <input type="text" name="kode_mapel" x-model="form.kode_mapel" placeholder="Contoh: MAT, IND, ING" required class="block w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm font-mono uppercase">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Kode ini harus unik dan tidak boleh sama dengan mapel lain.</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Nama Mata Pelajaran <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="ph ph-books text-gray-400 text-lg"></i>
                                        </div>
                                        <input type="text" name="nama_mapel" x-model="form.nama_mapel" placeholder="Contoh: Matematika, Bahasa Indonesia" required class="block w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Kelompok (Opsional)</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="ph ph-folder-open text-gray-400 text-lg"></i>
                                        </div>
                                        <select name="kelompok" x-model="form.kelompok" class="block w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm appearance-none">
                                            <option value="">-- Tidak Ada Kelompok --</option>
                                            <option value="A (Muatan Nasional)">Kelompok A (Muatan Nasional)</option>
                                            <option value="B (Muatan Kewilayahan)">Kelompok B (Muatan Kewilayahan)</option>
                                            <option value="C (Peminatan)">Kelompok C (Peminatan Akademik)</option>
                                            <option value="Lintas Minat">Lintas Minat</option>
                                            <option value="Muatan Lokal">Muatan Lokal</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                            <i class="ph ph-caret-down text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Seamless -->
                        <div class="px-6 py-4 flex flex-row-reverse gap-3">
                            <button type="submit" :disabled="isSubmitting" class="inline-flex w-full sm:w-auto justify-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-blue-500/30 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500/20 disabled:opacity-70 transition-all items-center gap-2">
                                <i class="ph ph-spinner animate-spin text-lg" x-show="isSubmitting" style="display: none;"></i>
                                <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Data'"></span>
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
</div>
<?= $this->endSection() ?>
