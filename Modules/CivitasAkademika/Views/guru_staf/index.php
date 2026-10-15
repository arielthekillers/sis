<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Civitas Akademika</span></li>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Data Guru & Staf</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Kelola Data Guru & Staf
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Manajemen data pendidik dan tenaga kependidikan di sekolah.
<?= $this->endSection() ?>

<?= $this->section('page_actions') ?>
<button @click="$dispatch('open-modal')" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors shadow-sm flex items-center gap-2">
    <i class="ph ph-plus-circle text-lg"></i>
    Tambah Data
</button>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div x-data="{ modalOpen: false, editMode: false, form: {id: '', nip: '', nama_lengkap: '', nama_panggilan: '', sapaan: 'Miss', jenis_kelamin: 'P', no_hp: '', jabatan: 'Guru Kelas', tahun_masuk: '', is_active: true} }" @open-modal.window="modalOpen = true; editMode = false; form = {id: '', nip: '', nama_lengkap: '', nama_panggilan: '', sapaan: 'Miss', jenis_kelamin: 'P', no_hp: '', jabatan: 'Guru Kelas', tahun_masuk: '', is_active: true};" @edit-modal.window="modalOpen = true; editMode = true; form = $event.detail; form.is_active = (form.is_active == 1 || form.is_active == true);">
    
    <!-- Tabel Data -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-800">
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Nama Lengkap & Panggilan</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Jabatan</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">NIP / ID</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    <?php if (empty($guru_staf)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-slate-400">
                            Belum ada data Guru & Staf.
                        </td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php foreach ($guru_staf as $g): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <?php if($g['foto']): ?>
                                    <img src="<?= base_url('uploads/profiles/thumb_' . $g['foto']) ?>" alt="Foto" class="w-10 h-10 rounded-full object-cover ring-2 ring-white dark:ring-slate-800 shadow-sm">
                                <?php else: ?>
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300 flex items-center justify-center font-bold text-sm ring-2 ring-white dark:ring-slate-800 shadow-sm">
                                        <?= substr($g['nama_panggilan'], 0, 1) ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                        <?= esc($g['sapaan']) ?> <?= esc($g['nama_lengkap']) ?>
                                        <?php if(!$g['is_active']): ?>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-400">Nonaktif</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">
                                        Panggilan: <span class="font-medium text-gray-700 dark:text-slate-300"><?= esc($g['nama_panggilan']) ?></span> • <?= $g['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?>
                                        <?php if($g['tahun_masuk']): ?> • Angkatan <?= esc($g['tahun_masuk']) ?><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold <?= $g['jabatan'] == 'Kepala Sekolah' ? 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300' : ($g['jabatan'] == 'Staff TU' ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300' : ($g['jabatan'] == 'Guru Pendamping' ? 'bg-sky-100 text-sky-700 dark:bg-sky-500/20 dark:text-sky-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300')) ?>">
                                <?= esc($g['jabatan'] ?: 'Guru Kelas') ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-700 dark:text-slate-300 font-mono"><?= esc($g['nip'] ?: '-') ?></span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="$dispatch('edit-modal', {id: '<?= $g['id'] ?>', nip: '<?= esc($g['nip']) ?>', nama_lengkap: '<?= esc($g['nama_lengkap']) ?>', nama_panggilan: '<?= esc($g['nama_panggilan']) ?>', sapaan: '<?= esc($g['sapaan']) ?>', jenis_kelamin: '<?= esc($g['jenis_kelamin']) ?>', no_hp: '<?= esc($g['no_hp']) ?>', jabatan: '<?= esc($g['jabatan'] ?? 'Guru Kelas') ?>', tahun_masuk: '<?= esc($g['tahun_masuk']) ?>', is_active: '<?= esc($g['is_active']) ?>'})" class="p-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-lg transition-colors" title="Edit">
                                    <i class="ph ph-pencil-simple text-lg"></i>
                                </button>
                                <button onclick="confirmAction('Hapus Data?', 'Data yang dihapus tidak bisa dikembalikan!', 'Ya, Hapus', () => { window.location.href='<?= base_url('civitas-akademika/guru-staf/delete/'.$g['id']) ?>' })" class="p-1.5 text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors" title="Hapus">
                                    <i class="ph ph-trash text-lg"></i>
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
            <?= $pager->links('guru') ?>
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
                     class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-100 dark:border-slate-700">
                    
                    <form action="<?= base_url('civitas-akademika/guru-staf/save') ?>" method="POST" enctype="multipart/form-data" x-data="{ isSubmitting: false, previewPhoto: null }" @submit="isSubmitting = true">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" x-model="form.id">

                        <div class="px-6 py-6 max-h-[80vh] overflow-y-auto custom-scrollbar">
                            <!-- Header Seamless -->
                            <div class="mb-6">
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white" id="modal-title" x-text="editMode ? 'Edit Data Guru & Staf' : 'Tambah Guru & Staf'"></h3>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="ph ph-user text-gray-400 text-lg"></i>
                                        </div>
                                        <input type="text" name="nama_lengkap" x-model="form.nama_lengkap" placeholder="Nama Lengkap beserta Gelar (jika ada)" required class="block w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Nama Panggilan <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="ph ph-smiley text-gray-400 text-lg"></i>
                                        </div>
                                        <input type="text" name="nama_panggilan" x-model="form.nama_panggilan" placeholder="Nama Panggilan" required class="block w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">NIP / ID (Opsional)</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="ph ph-identification-card text-gray-400 text-lg"></i>
                                        </div>
                                        <input type="text" name="nip" x-model="form.nip" placeholder="NIP / NIK" class="block w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm font-mono">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Jenis Kelamin <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="ph ph-gender-intersex text-gray-400 text-lg"></i>
                                        </div>
                                        <select name="jenis_kelamin" x-model="form.jenis_kelamin" @change="form.sapaan = (form.jenis_kelamin == 'L' ? 'Bapak' : 'Ibu')" class="block w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm appearance-none">
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                            <i class="ph ph-caret-down text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Panggilan / Sapaan <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="ph ph-megaphone-simple text-gray-400 text-lg"></i>
                                        </div>
                                        <select name="sapaan" x-model="form.sapaan" class="block w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm appearance-none">
                                            <!-- Pilihan dinamis berdasarkan jenis kelamin (reaktif Alpine) -->
                                            <template x-if="form.jenis_kelamin == 'L'">
                                                <optgroup label="Laki-laki">
                                                    <option value="Bapak">Bapak</option>
                                                    <option value="Ustadz">Ustadz</option>
                                                    <option value="Mr.">Mr.</option>
                                                    <option value="Sir">Sir</option>
                                                </optgroup>
                                            </template>
                                            <template x-if="form.jenis_kelamin == 'P'">
                                                <optgroup label="Perempuan">
                                                    <option value="Ibu">Ibu</option>
                                                    <option value="Ustadzah">Ustadzah</option>
                                                    <option value="Miss">Miss</option>
                                                    <option value="Mrs.">Mrs.</option>
                                                </optgroup>
                                            </template>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                            <i class="ph ph-caret-down text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">No. HP / WhatsApp</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="ph ph-phone text-gray-400 text-lg"></i>
                                        </div>
                                        <input type="text" name="no_hp" x-model="form.no_hp" placeholder="Contoh: 08123456789" class="block w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm font-mono">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Jabatan <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="ph ph-identification-badge text-gray-400 text-lg"></i>
                                        </div>
                                        <select name="jabatan" x-model="form.jabatan" required class="block w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm appearance-none">
                                            <option value="Guru Kelas">Guru Kelas</option>
                                            <option value="Kepala Sekolah">Kepala Sekolah</option>
                                            <option value="Staff TU">Staff TU</option>
                                            <option value="Guru Pendamping">Guru Pendamping</option>
                                            <option value="Lainnya">Lainnya</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                            <i class="ph ph-caret-down text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Tahun Masuk (Opsional)</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="ph ph-calendar text-gray-400 text-lg"></i>
                                        </div>
                                        <input type="number" name="tahun_masuk" x-model="form.tahun_masuk" placeholder="Contoh: 2022" min="1990" max="<?= date('Y') + 1 ?>" class="block w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm font-mono">
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Foto Profil</label>
                                    <div class="mt-1 flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-slate-700 border border-gray-200 dark:border-slate-600 flex items-center justify-center overflow-hidden shrink-0">
                                            <template x-if="previewPhoto">
                                                <img :src="previewPhoto" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!previewPhoto">
                                                <i class="ph ph-camera text-gray-400 text-xl"></i>
                                            </template>
                                        </div>
                                        <div class="flex-1">
                                            <input type="file" name="foto" id="foto" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 dark:file:bg-blue-500/10 dark:file:text-blue-400 hover:file:bg-blue-100 dark:hover:file:bg-blue-500/20 transition-all cursor-pointer"
                                                @change="
                                                    const file = $event.target.files[0];
                                                    if (file) {
                                                        const reader = new FileReader();
                                                        reader.onload = (e) => previewPhoto = e.target.result;
                                                        reader.readAsDataURL(file);
                                                    } else {
                                                        previewPhoto = null;
                                                    }
                                                ">
                                            <p class="mt-1 text-xs text-gray-400">JPG, PNG maksimal 2MB. Akan di-crop persegi otomatis.</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="sm:col-span-2 pt-2 border-t border-gray-100 dark:border-slate-700 mt-2">
                                    <label class="flex items-center cursor-pointer">
                                        <div class="relative">
                                            <input type="checkbox" name="is_active" class="sr-only" x-model="form.is_active" value="1">
                                            <div class="block w-10 h-6 rounded-full transition-colors duration-300" :class="form.is_active ? 'bg-blue-500' : 'bg-gray-200 dark:bg-slate-700'"></div>
                                            <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform duration-300 shadow-sm" :class="form.is_active ? 'translate-x-4' : 'translate-x-0'"></div>
                                        </div>
                                        <div class="ml-3 text-sm font-medium text-gray-700 dark:text-slate-300">
                                            Status Pegawai Aktif
                                        </div>
                                    </label>
                                </div>

                            </div>
                        </div>

                        <!-- Footer Seamless -->
                        <div class="px-6 py-4 flex flex-row-reverse gap-3 bg-gray-50 dark:bg-slate-800/50 rounded-b-2xl border-t border-gray-100 dark:border-slate-700">
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
