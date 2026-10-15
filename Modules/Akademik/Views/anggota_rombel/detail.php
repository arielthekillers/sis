<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><a href="<?= base_url('akademik/anggota-rombel') ?>" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Penempatan Siswa</a></li>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Kelas <?= esc($rombel['nama_kelas']) ?></span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Daftar Siswa Kelas <?= esc($rombel['nama_kelas']) ?>
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Mengelola data peserta didik yang masuk dalam Rombongan Belajar ini.
<?= $this->endSection() ?>

<?= $this->section('page_actions') ?>
<button @click="$dispatch('open-modal')" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors shadow-sm flex items-center gap-2">
    <i class="ph ph-user-plus text-lg"></i>
    Tambah Siswa ke Kelas
</button>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
    <div class="p-4 border-b border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-800/50 flex justify-between items-center">
        <h3 class="font-semibold text-gray-800 dark:text-slate-200">Total Siswa: <?= count($anggota) ?> Orang</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-800">
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider w-16">No</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Nama Siswa</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">NIS/NISN</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Jenis Kelamin</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                <?php if (empty($anggota)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-slate-400">
                        Belum ada siswa di kelas ini.
                    </td>
                </tr>
                <?php endif; ?>
                
                <?php $no=1; foreach ($anggota as $s): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors group">
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-slate-400"><?= $no++ ?></td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                            <?= esc($s['nama_lengkap']) ?>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-700 dark:text-slate-300"><?= esc($s['nis'] ?: '-') ?></div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-slate-300">
                        <?= $s['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="confirmAction('Keluarkan Siswa?', 'Siswa akan dikeluarkan dari kelas <?= esc($rombel['nama_kelas']) ?>!', 'Ya, Keluarkan', () => { window.location.href='<?= base_url('akademik/anggota-rombel/remove/'.$s['rs_id']) ?>' })" class="p-1.5 text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors" title="Keluarkan dari kelas">
                                <i class="ph ph-user-minus text-lg"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Siswa -->
<div x-data="{ modalOpen: false, search: '', checkAll: false }" @open-modal.window="modalOpen = true; search = ''; checkAll = false;" x-show="modalOpen" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
    <div x-show="modalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="modalOpen" @click.away="modalOpen = false" x-transition class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-100 dark:border-slate-700 flex flex-col max-h-[85vh]">
                
                <form action="<?= base_url('akademik/anggota-rombel/add-siswa') ?>" method="POST" class="flex flex-col h-full" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                    <?= csrf_field() ?>
                    <input type="hidden" name="rombel_id" value="<?= $rombel['id'] ?>">
                    
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-700 shrink-0">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Pilih Siswa ke Kelas <?= esc($rombel['nama_kelas']) ?></h3>
                        
                        <!-- Search Box -->
                        <div class="relative">
                            <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" x-model="search" placeholder="Cari nama siswa..." class="block w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-lg text-gray-900 dark:text-white placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all text-sm">
                        </div>
                    </div>

                    <!-- List Siswa -->
                    <div class="p-2 overflow-y-auto custom-scrollbar flex-1 bg-gray-50/30 dark:bg-slate-900/20">
                        <?php if(empty($calon_siswa)): ?>
                            <div class="text-center py-10 text-gray-500 dark:text-slate-400 text-sm">
                                Tidak ada data siswa aktif yang tersedia.
                            </div>
                        <?php else: ?>
                            <div class="space-y-1">
                                <?php foreach($calon_siswa as $cs): ?>
                                <label class="flex items-center p-3 rounded-lg hover:bg-white dark:hover:bg-slate-800 cursor-pointer transition-colors border border-transparent hover:border-gray-100 dark:hover:border-slate-700 hover:shadow-sm"
                                       x-show="search === '' || '<?= strtolower(esc($cs['nama_lengkap'])) ?>'.includes(search.toLowerCase())">
                                    <input type="checkbox" name="siswa_ids[]" value="<?= $cs['id'] ?>" class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 checkbox-siswa">
                                    <div class="ml-4 flex-1">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-slate-200"><?= esc($cs['nama_lengkap']) ?></div>
                                        <div class="text-xs text-gray-500 dark:text-slate-400 mt-0.5"><?= esc($cs['nis'] ?: 'Tanpa NIS') ?> • <?= $cs['jenis_kelamin'] == 'L' ? 'L' : 'P' ?></div>
                                    </div>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 bg-white dark:bg-slate-800 border-t border-gray-100 dark:border-slate-700 flex items-center justify-between shrink-0">
                        <label class="flex items-center text-sm text-gray-600 dark:text-slate-400 cursor-pointer">
                            <input type="checkbox" x-model="checkAll" @change="document.querySelectorAll('.checkbox-siswa').forEach(cb => { if(cb.closest('label').style.display !== 'none') cb.checked = checkAll; })" class="w-4 h-4 text-blue-600 mr-2 rounded">
                            Pilih Semua (yang tampil)
                        </label>
                        
                        <div class="flex gap-3">
                            <button type="button" @click="modalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-slate-200 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition-colors">Batal</button>
                            <button type="submit" :disabled="isSubmitting" class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 focus:ring-4 focus:ring-blue-500/20 shadow-lg shadow-blue-500/30 transition-all">
                                <i class="ph ph-spinner animate-spin" x-show="isSubmitting" style="display: none;"></i>
                                Masukkan ke Kelas
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
