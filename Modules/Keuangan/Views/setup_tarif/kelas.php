<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><a href="<?= base_url('keuangan/setup-tarif') ?>" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Setup Tarif Siswa</a></li>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Kelas <?= esc($rombel['nama_kelas']) ?></span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Setup Tarif Kelas <?= esc($rombel['nama_kelas']) ?>
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Pilih siswa untuk mengatur komponen dan nominal tagihan mereka masing-masing, atau gunakan fitur Bulk Setup untuk menetapkan tagihan ke seluruh kelas.
<?= $this->endSection() ?>

<?= $this->section('page_actions') ?>
<button @click="$dispatch('open-modal')" class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors shadow-sm flex items-center gap-2">
    <i class="ph ph-stack"></i> Bulk Setup Kelas
</button>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div x-data="{
    selectedStudents: [],
    selectAll: false,
    modalOpen: false,
    form: {jenis_id: '', action: 'add'},
    allStudentIds: <?= htmlspecialchars(json_encode(array_column($siswa, 'id'))) ?>,
    toggleAll() {
        if (this.selectAll) {
            this.selectedStudents = [...this.allStudentIds];
        } else {
            this.selectedStudents = [];
        }
    }
}" @open-modal.window="modalOpen = true; form.jenis_id = '';">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-800">
                    <th class="px-6 py-4 w-12 text-center">
                        <input type="checkbox" x-model="selectAll" @change="toggleAll" class="rounded border-gray-300 text-emerald-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-600 dark:bg-slate-700 dark:focus:ring-offset-slate-800">
                    </th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider w-16">No</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Nama Siswa</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">NIS/NISN</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-center">Komponen Tarif Aktif</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                <?php if (empty($siswa)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-slate-400">
                        Belum ada siswa di kelas ini. Masukkan siswa lewat menu Akademik > Penempatan Siswa.
                    </td>
                </tr>
                <?php endif; ?>
                
                <?php $no=1; foreach ($siswa as $s): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors group">
                    <td class="px-6 py-4 text-center">
                        <input type="checkbox" value="<?= $s['id'] ?>" x-model="selectedStudents" class="rounded border-gray-300 text-emerald-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-600 dark:bg-slate-700 dark:focus:ring-offset-slate-800">
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-slate-400"><?= $no++ ?></td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                            <?= esc($s['nama_lengkap']) ?>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-700 dark:text-slate-300"><?= esc($s['nis'] ?: '-') ?></div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <?php if($s['total_setup'] > 0): ?>
                            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400">
                                <?= $s['total_setup'] ?> Tagihan
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-400">
                                Belum Disetup
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="<?= base_url('keuangan/setup-tarif/detail/' . $s['id']) ?>" class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-emerald-700 bg-emerald-50 dark:bg-emerald-500/10 dark:text-emerald-400 rounded-lg hover:bg-emerald-100 dark:hover:bg-emerald-500/20 transition-colors border border-emerald-200 dark:border-emerald-800/30">
                            <i class="ph ph-gear"></i> Setup Tarif
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Modal Bulk Setup -->
    <div x-show="modalOpen" class="relative z-50" style="display: none;">
        <div x-show="modalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div @click.away="modalOpen = false" x-show="modalOpen" x-transition class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-100 dark:border-slate-700">
                <form action="<?= base_url('keuangan/setup-tarif/bulk-save') ?>" method="POST" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                    <?= csrf_field() ?>
                    <input type="hidden" name="rombel_id" value="<?= $rombel['id'] ?>">
                    <input type="hidden" name="siswa_ids" :value="JSON.stringify(selectedStudents)">

                    <div class="px-6 py-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Bulk Setup Tarif</h3>
                        <p class="text-sm text-gray-500 dark:text-slate-400 mb-5">
                            Menerapkan aksi ke <span class="font-bold text-emerald-600 dark:text-emerald-400" x-text="selectedStudents.length"></span> siswa terpilih.
                        </p>
                        
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Aksi <span class="text-red-500">*</span></label>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="bulk_action" value="add" x-model="form.action" class="text-emerald-600 focus:ring-emerald-500">
                                        <span class="text-sm text-gray-700 dark:text-slate-300">Tambahkan Tarif</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="bulk_action" value="remove" x-model="form.action" class="text-red-600 focus:ring-red-500">
                                        <span class="text-sm text-gray-700 dark:text-slate-300">Hapus Tarif</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Pilih Jenis Tagihan <span class="text-red-500">*</span></label>
                                <select name="jenis_pembayaran_id" x-model="form.jenis_id" required class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all sm:text-sm">
                                    <option value="">-- Pilih --</option>
                                    <?php foreach($jenis_pembayaran as $aj): ?>
                                        <option value="<?= $aj['id'] ?>"><?= esc($aj['nama_pembayaran']) ?> (<?= esc($aj['tipe_siklus']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 flex flex-row-reverse gap-3 bg-gray-50 dark:bg-slate-800/50 border-t border-gray-100 dark:border-slate-700">
                        <button type="submit" :disabled="isSubmitting || form.jenis_id === '' || selectedStudents.length === 0" :class="form.action === 'add' ? 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-500/30' : 'bg-red-600 hover:bg-red-700 shadow-red-500/30'" class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-medium text-white shadow-lg transition-all disabled:opacity-50">
                            <i class="ph ph-spinner animate-spin text-lg" x-show="isSubmitting" style="display: none;"></i>
                            <span x-text="isSubmitting ? 'Memproses...' : (form.action === 'add' ? 'Terapkan Tarif' : 'Hapus Tarif')"></span>
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
