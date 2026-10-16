<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Civitas Akademika</span></li>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Data Siswa</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Kelola Data Siswa
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Manajemen data induk seluruh peserta didik (Master Siswa).
<?= $this->endSection() ?>

<?= $this->section('page_actions') ?>
<button @click="$dispatch('open-modal')" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors shadow-sm flex items-center gap-2">
    <i class="ph ph-plus-circle text-lg"></i>
    Tambah Siswa
</button>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div x-data="{ modalOpen: false, editMode: false, form: {id: '', nis: '', nisn: '', nama_lengkap: '', nama_panggilan: '', jenis_kelamin: 'L', tempat_lahir: '', tanggal_lahir: '', agama: '', alamat: '', nama_ayah: '', nama_ibu: '', no_hp_ortu: '', status_siswa: 'Aktif', is_active: true} }" 
     @open-modal.window="modalOpen = true; editMode = false; form = {id: '', nis: '', nisn: '', nama_lengkap: '', nama_panggilan: '', jenis_kelamin: 'L', tempat_lahir: '', tanggal_lahir: '', agama: '', alamat: '', nama_ayah: '', nama_ibu: '', no_hp_ortu: '', status_siswa: 'Aktif', is_active: true};" 
     @edit-modal.window="modalOpen = true; editMode = true; form = $event.detail; form.is_active = (form.is_active == 1 || form.is_active == true);">
    
    <!-- Tabel Data -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-800">
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Siswa</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">NIS / NISN</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    <?php if (empty($siswa)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-slate-400">
                            Belum ada data Siswa.
                        </td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php foreach ($siswa as $s): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <?php if($s['foto']): ?>
                                    <img src="<?= base_url('uploads/profiles/thumb_' . $s['foto']) ?>" alt="Foto" class="w-10 h-10 rounded-full object-cover ring-2 ring-white dark:ring-slate-800 shadow-sm">
                                <?php else: ?>
                                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300 flex items-center justify-center font-bold text-sm ring-2 ring-white dark:ring-slate-800 shadow-sm">
                                        <?= substr($s['nama_lengkap'], 0, 1) ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                        <?= esc($s['nama_lengkap']) ?>
                                        <?php if(!$s['is_active']): ?>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-400">Nonaktif</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">
                                        <?= $s['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?> 
                                        <?= $s['nama_panggilan'] ? ' • Panggilan: ' . esc($s['nama_panggilan']) : '' ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-mono text-gray-700 dark:text-slate-300"><?= esc($s['nis'] ?: '-') ?></div>
                            <div class="text-xs text-gray-500 dark:text-slate-400 mt-0.5"><?= esc($s['nisn'] ?: 'Tanpa NISN') ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <?php 
                                $statusColor = 'bg-gray-100 text-gray-800 dark:bg-gray-500/20 dark:text-gray-400';
                                if($s['status_siswa'] == 'Aktif') $statusColor = 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400';
                                if($s['status_siswa'] == 'Lulus') $statusColor = 'bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-400';
                                if($s['status_siswa'] == 'Pindah') $statusColor = 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-400';
                                if($s['status_siswa'] == 'Drop Out') $statusColor = 'bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-400';
                            ?>
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium <?= $statusColor ?>">
                                <?= esc($s['status_siswa']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <?php
                                    $modalData = [
                                        'id' => $s['id'],
                                        'nis' => $s['nis'],
                                        'nisn' => $s['nisn'],
                                        'nama_lengkap' => $s['nama_lengkap'],
                                        'nama_panggilan' => $s['nama_panggilan'],
                                        'jenis_kelamin' => $s['jenis_kelamin'],
                                        'tempat_lahir' => $s['tempat_lahir'],
                                        'tanggal_lahir' => $s['tanggal_lahir'],
                                        'agama' => $s['agama'],
                                        'alamat' => $s['alamat'],
                                        'nama_ayah' => $s['nama_ayah'],
                                        'nama_ibu' => $s['nama_ibu'],
                                        'no_hp_ortu' => $s['no_hp_ortu'],
                                        'status_siswa' => $s['status_siswa'],
                                        'is_active' => $s['is_active']
                                    ];
                                ?>
                                <a href="<?= base_url('keuangan/status-spp/'.$s['id']) ?>" class="p-1.5 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 rounded-lg transition-colors" title="Cek Status SPP (Aman / Tunggakan)">
                                    <i class="ph ph-shield-check text-lg"></i>
                                </a>
                                <a href="<?= base_url('civitas-akademika/siswa/profil/'.$s['id']) ?>" class="p-1.5 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 rounded-lg transition-colors" title="Lihat Profil">
                                    <i class="ph ph-user-circle text-lg"></i>
                                </a>
                                <button @click="$dispatch('edit-modal', <?= htmlspecialchars(json_encode($modalData), ENT_QUOTES, 'UTF-8') ?>)" class="p-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-lg transition-colors" title="Edit">
                                    <i class="ph ph-pencil-simple text-lg"></i>
                                </button>
                                <button onclick="confirmAction('Hapus Data?', 'Data yang dihapus tidak bisa dikembalikan!', 'Ya, Hapus', () => { window.location.href='<?= base_url('civitas-akademika/siswa/delete/'.$s['id']) ?>' })" class="p-1.5 text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors" title="Hapus">
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
            <?= $pager->links('siswa') ?>
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
                     class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl border border-gray-100 dark:border-slate-700">
                    
                    <form action="<?= base_url('civitas-akademika/siswa/save') ?>" method="POST" enctype="multipart/form-data" x-data="{ isSubmitting: false, previewPhoto: null, tab: 'pribadi' }" @submit="isSubmitting = true">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" x-model="form.id">

                        <div class="px-6 py-6 max-h-[80vh] overflow-y-auto custom-scrollbar">
                            <!-- Header Seamless -->
                            <div class="mb-4">
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white" id="modal-title" x-text="editMode ? 'Edit Data Siswa' : 'Tambah Siswa Baru'"></h3>
                            </div>
                            
                            <!-- Tabs (Tailwind style) -->
                            <div class="border-b border-gray-200 dark:border-slate-700 mb-5">
                                <nav class="-mb-px flex space-x-6">
                                    <button type="button" @click="tab = 'pribadi'" class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors" :class="tab === 'pribadi' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-slate-400 dark:hover:text-slate-300'">Data Pribadi</button>
                                    <button type="button" @click="tab = 'ortu'" class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors" :class="tab === 'ortu' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-slate-400 dark:hover:text-slate-300'">Keluarga & Kontak</button>
                                    <button type="button" @click="tab = 'akademik'" class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors" :class="tab === 'akademik' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-slate-400 dark:hover:text-slate-300'">Data Akademik</button>
                                </nav>
                            </div>

                            <!-- Tab 1: Pribadi -->
                            <div x-show="tab === 'pribadi'" class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                
                                <div class="sm:col-span-2 flex items-center gap-4 mb-2">
                                    <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-slate-700 border border-gray-200 dark:border-slate-600 flex items-center justify-center overflow-hidden shrink-0 shadow-sm">
                                        <template x-if="previewPhoto">
                                            <img :src="previewPhoto" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!previewPhoto">
                                            <i class="ph ph-camera text-gray-400 text-2xl"></i>
                                        </template>
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Foto Profil</label>
                                        <input type="file" name="foto" id="foto_siswa" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 dark:file:bg-blue-500/10 dark:file:text-blue-400 hover:file:bg-blue-100 dark:hover:file:bg-blue-500/20 transition-all cursor-pointer"
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
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                                    <input type="text" name="nama_lengkap" x-model="form.nama_lengkap" placeholder="Sesuai Akta Kelahiran" required class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Nama Panggilan</label>
                                    <input type="text" name="nama_panggilan" x-model="form.nama_panggilan" placeholder="Opsional" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Jenis Kelamin <span class="text-red-500">*</span></label>
                                    <select name="jenis_kelamin" x-model="form.jenis_kelamin" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm">
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" x-model="form.tempat_lahir" placeholder="Kota/Kabupaten" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" x-model="form.tanggal_lahir" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Agama</label>
                                    <select name="agama" x-model="form.agama" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm">
                                        <option value="">Pilih Agama</option>
                                        <option value="Islam">Islam</option>
                                        <option value="Kristen">Kristen</option>
                                        <option value="Katolik">Katolik</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Buddha">Buddha</option>
                                        <option value="Konghucu">Konghucu</option>
                                    </select>
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Alamat Tempat Tinggal</label>
                                    <textarea name="alamat" x-model="form.alamat" rows="2" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm"></textarea>
                                </div>
                            </div>

                            <!-- Tab 2: Orang Tua -->
                            <div x-show="tab === 'ortu'" class="grid grid-cols-1 sm:grid-cols-2 gap-5" style="display: none;">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Nama Ayah Kandung</label>
                                    <input type="text" name="nama_ayah" x-model="form.nama_ayah" placeholder="Sesuai Kartu Keluarga" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Nama Ibu Kandung</label>
                                    <input type="text" name="nama_ibu" x-model="form.nama_ibu" placeholder="Sesuai Kartu Keluarga" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">No HP Orang Tua / Wali</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="ph ph-whatsapp logo text-gray-400 text-lg"></i>
                                        </div>
                                        <input type="text" name="no_hp_ortu" x-model="form.no_hp_ortu" placeholder="08..." class="block w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm font-mono">
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 3: Akademik -->
                            <div x-show="tab === 'akademik'" class="grid grid-cols-1 sm:grid-cols-2 gap-5" style="display: none;">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">NIS (Nomor Induk Siswa)</label>
                                    <input type="text" name="nis" x-model="form.nis" placeholder="Opsional / Sesuai Aturan Sekolah" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm font-mono">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">NISN (Nasional)</label>
                                    <input type="text" name="nisn" x-model="form.nisn" placeholder="Hanya untuk SD/SMP/SMA" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm font-mono">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Status Siswa</label>
                                    <select name="status_siswa" x-model="form.status_siswa" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all sm:text-sm">
                                        <option value="Aktif">Aktif</option>
                                        <option value="Lulus">Lulus</option>
                                        <option value="Pindah">Pindah / Mutasi Keluar</option>
                                        <option value="Drop Out">Drop Out / Berhenti</option>
                                    </select>
                                </div>
                                
                                <div class="sm:col-span-2 pt-2 border-t border-gray-100 dark:border-slate-700 mt-2">
                                    <label class="flex items-center cursor-pointer">
                                        <div class="relative">
                                            <input type="checkbox" name="is_active" class="sr-only" x-model="form.is_active" value="1">
                                            <div class="block w-10 h-6 rounded-full transition-colors duration-300" :class="form.is_active ? 'bg-blue-500' : 'bg-gray-200 dark:bg-slate-700'"></div>
                                            <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform duration-300 shadow-sm" :class="form.is_active ? 'translate-x-4' : 'translate-x-0'"></div>
                                        </div>
                                        <div class="ml-3 text-sm font-medium text-gray-700 dark:text-slate-300">
                                            Status Data Induk Aktif (Bisa Login)
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
