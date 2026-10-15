<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Master Data</span></li>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Tahun Ajaran</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Kelola Tahun Ajaran
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Manajemen data tahun ajaran dan semester aktif. Semua data operasional akan merujuk ke tahun ajaran yang sedang aktif.
<?= $this->endSection() ?>

<?= $this->section('page_actions') ?>
<button @click="$dispatch('open-modal')" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors shadow-sm flex items-center gap-2">
    <i class="ph ph-plus-circle text-lg"></i>
    Tambah Tahun Ajaran
</button>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div x-data="{ modalOpen: false, editMode: false, form: {id: '', tahun: '', semester: 'Ganjil', is_active: false} }" @open-modal.window="modalOpen = true; editMode = false; form = {id: '', tahun: '', semester: 'Ganjil', is_active: false};" @edit-modal.window="modalOpen = true; editMode = true; form = $event.detail;">
    
    <!-- Tabel Data -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-800">
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Tahun Ajaran</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Semester</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    <?php if (empty($tahun_ajaran)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-slate-400">
                            Belum ada data tahun ajaran.
                        </td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php foreach ($tahun_ajaran as $ta): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors group">
                        <td class="px-6 py-4">
                            <span class="text-sm font-medium text-gray-900 dark:text-white"><?= esc($ta['tahun']) ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600 dark:text-slate-300"><?= esc($ta['semester']) ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($ta['is_active']): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                                    Aktif
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-slate-700 dark:text-slate-300 border border-gray-200 dark:border-slate-600">
                                    Tidak Aktif
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="$dispatch('edit-modal', {id: '<?= $ta['id'] ?>', tahun: '<?= esc($ta['tahun']) ?>', semester: '<?= esc($ta['semester']) ?>', is_active: <?= $ta['is_active'] ? 'true' : 'false' ?>})" class="p-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-lg transition-colors" title="Edit">
                                    <i class="ph ph-pencil-simple text-lg"></i>
                                </button>
                                <button onclick="confirmAction('Hapus Tahun Ajaran?', 'Data yang dihapus tidak bisa dikembalikan!', 'Ya, Hapus', () => { window.location.href='<?= base_url('master-data/tahun-ajaran/delete/'.$ta['id']) ?>' })" class="p-1.5 text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors" title="Hapus">
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
                    
                    <form action="<?= base_url('master-data/tahun-ajaran/save') ?>" method="POST" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" x-model="form.id">

                        <div class="px-6 py-6">
                            <!-- Header Seamless -->
                            <div class="mb-6">
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white" id="modal-title" x-text="editMode ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran'"></h3>
                                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Isi formulir di bawah ini dengan benar.</p>
                            </div>
                            
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Tahun Ajaran <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="ph ph-calendar-blank text-gray-400 text-lg"></i>
                                        </div>
                                        <input type="text" name="tahun" x-model="form.tahun" placeholder="Contoh: 2026/2027" required class="block w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm">
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Semester <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="ph ph-books text-gray-400 text-lg"></i>
                                        </div>
                                        <select name="semester" x-model="form.semester" required class="block w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm appearance-none">
                                            <option value="Ganjil">Ganjil</option>
                                            <option value="Genap">Genap</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                            <i class="ph ph-caret-down text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center pt-2">
                                    <input id="is_active" name="is_active" type="checkbox" value="1" x-model="form.is_active" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:checked:bg-blue-500">
                                    <label for="is_active" class="ml-2 block text-sm font-medium text-gray-900 dark:text-slate-200">
                                        Jadikan Tahun Ajaran Aktif Saat Ini
                                    </label>
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
