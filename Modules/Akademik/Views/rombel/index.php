<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Akademik</span></li>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Rombongan Belajar</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Kelola Rombongan Belajar
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
<div class="flex items-center gap-2 mt-1">
    <span>Manajemen tingkatan dan nama rombongan belajar di sekolah. Menampilkan Rombel untuk Tahun Ajaran: </span>
    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
        <?= esc($tahun_aktif) ?>
    </span>
</div>
<?= $this->endSection() ?>

<?= $this->section('page_actions') ?>
<button @click="$dispatch('open-modal')" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors shadow-sm flex items-center gap-2">
    <i class="ph ph-plus-circle text-lg"></i>
    Tambah Rombel
</button>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div x-data="{ modalOpen: false, editMode: false, form: {id: '', tingkat: '', nama_kelas: '', keterangan: '', wali_kelas: []} }" @open-modal.window="modalOpen = true; editMode = false; form = {id: '', tingkat: '', nama_kelas: '', keterangan: '', wali_kelas: []};" @edit-modal.window="modalOpen = true; editMode = true; form = $event.detail;">
    
    <!-- Tabel Data -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-800">
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Tingkat</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Nama Rombel</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Wali Kelas</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Keterangan</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    <?php if (empty($rombel)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-slate-400">
                            Belum ada data Rombongan Belajar.
                        </td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php foreach ($rombel as $k): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors group">
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 font-bold">
                                <?= esc($k['tingkat']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-semibold text-gray-900 dark:text-white"><?= esc($k['nama_kelas']) ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <?php 
                            $wkArray = $k['wali_kelas'] ? json_decode($k['wali_kelas'], true) : [];
                            if (!empty($wkArray)):
                            ?>
                                <div class="flex flex-wrap gap-1.5">
                                    <?php foreach($wkArray as $wkId): ?>
                                        <?php if(isset($guruMap[$wkId])): ?>
                                            <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-xs font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800/30">
                                                <i class="ph ph-user-circle text-sm"></i>
                                                <?= esc($guruMap[$wkId]) ?>
                                            </span>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <span class="text-xs text-gray-400 italic">Belum diatur</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-500 dark:text-slate-400"><?= esc($k['keterangan'] ?: '-') ?></span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <?php
                                    $modalData = [
                                        'id' => $k['id'],
                                        'tingkat' => $k['tingkat'],
                                        'nama_kelas' => $k['nama_kelas'],
                                        'keterangan' => $k['keterangan'],
                                        'wali_kelas' => $k['wali_kelas'] ? json_decode($k['wali_kelas'], true) : []
                                    ];
                                ?>
                                <button @click="$dispatch('edit-modal', <?= htmlspecialchars(json_encode($modalData), ENT_QUOTES, 'UTF-8') ?>)" class="p-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-lg transition-colors" title="Edit">
                                    <i class="ph ph-pencil-simple text-lg"></i>
                                </button>
                                <button onclick="confirmAction('Hapus Rombel?', 'Data yang dihapus tidak bisa dikembalikan!', 'Ya, Hapus', () => { window.location.href='<?= base_url('akademik/rombel/delete/'.$k['id']) ?>' })" class="p-1.5 text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors" title="Hapus">
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
                    
                    <form action="<?= base_url('akademik/rombel/save') ?>" method="POST" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" x-model="form.id">

                        <div class="px-6 py-6">
                            <!-- Header Seamless -->
                            <div class="mb-6">
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white" id="modal-title" x-text="editMode ? 'Edit Data Rombel' : 'Tambah Rombel Baru'"></h3>
                            </div>
                            
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Tingkat Kelas <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="ph ph-sort-ascending text-gray-400 text-lg"></i>
                                        </div>
                                        <input type="text" name="tingkat" x-model="form.tingkat" placeholder="Contoh: PG, TK-A, 1, 10..." required class="block w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm">
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Nama Rombel <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="ph ph-chalkboard-teacher text-gray-400 text-lg"></i>
                                        </div>
                                        <input type="text" name="nama_kelas" x-model="form.nama_kelas" placeholder="Contoh: X-IPA-1, X-IPS-2" required class="block w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm">
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Wali Kelas</label>
                                    <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl border border-gray-100 dark:border-slate-700 p-3 max-h-48 overflow-y-auto custom-scrollbar">
                                        <?php if(empty($guru)): ?>
                                            <p class="text-xs text-gray-500 dark:text-slate-400">Belum ada data Guru Aktif.</p>
                                        <?php else: ?>
                                            <div class="space-y-2">
                                                <?php foreach($guru as $g): ?>
                                                <label class="flex items-center p-2 rounded hover:bg-white dark:hover:bg-slate-800 cursor-pointer transition-colors">
                                                    <input type="checkbox" name="wali_kelas[]" value="<?= $g['id'] ?>" x-model="form.wali_kelas" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                                    <span class="ml-3 text-sm text-gray-900 dark:text-slate-200">
                                                        <?= esc($g['sapaan']) ?> <?= esc($g['nama_lengkap']) ?>
                                                    </span>
                                                </label>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-[11px] text-gray-500 mt-1.5"><i class="ph ph-info"></i> Anda bisa memilih lebih dari satu guru sebagai wali kelas.</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Keterangan (Opsional)</label>
                                    <div class="relative">
                                        <textarea name="keterangan" x-model="form.keterangan" rows="2" placeholder="Informasi tambahan..." class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm resize-none"></textarea>
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
