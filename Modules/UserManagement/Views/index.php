<?= $this->extend('layout/main') ?>

<!-- Breadcrumb -->
<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Manajemen Pengguna</span></li>
<?= $this->endSection() ?>

<!-- Judul -->
<?= $this->section('page_title') ?>
Manajemen Pengguna
<?= $this->endSection() ?>

<!-- Sub Judul -->
<?= $this->section('page_subtitle') ?>
Kelola data karyawan, hak akses (jabatan), dan foto profil mereka.
<?= $this->endSection() ?>

<!-- Tombol Tambah (Hanya Muncul Jika Punya Akses users.create) -->
<?= $this->section('page_actions') ?>
<?php if (auth()->user()->can('users.create')): ?>
<button x-data @click="$dispatch('open-modal-tambah')" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors shadow-sm flex items-center gap-2">
    <i class="ph ph-plus-circle text-lg"></i>
    Tambah Pengguna
</button>
<?php endif; ?>
<?= $this->endSection() ?>

<!-- Konten Utama -->
<?= $this->section('content') ?>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
    
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600 dark:text-slate-300">
            <thead class="bg-gray-50 dark:bg-slate-900/50 text-gray-700 dark:text-slate-200 border-b border-gray-100 dark:border-slate-700">
                <tr>
                    <th class="px-6 py-4 font-semibold">Pengguna</th>
                    <th class="px-6 py-4 font-semibold">Email</th>
                    <th class="px-6 py-4 font-semibold">Jabatan (Role)</th>
                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700/50">
                <?php if (empty($users)): ?>
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                        Belum ada data pengguna (akun) yang terdaftar.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <!-- Placeholder Foto Profil -->
                                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                                    <?= strtoupper(substr($u->username, 0, 1)) ?>
                                </div>
                                <div class="font-medium text-gray-900 dark:text-slate-200">
                                    <?= esc($u->username) ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4"><?= esc($u->email ?? '-') ?></td>
                        <td class="px-6 py-4">
                            <?php 
                                $groups = $u->getGroups();
                                if (empty($groups)) {
                                    echo '<span class="px-2 py-1 bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-400 rounded text-xs font-medium">Belum ada jabatan</span>';
                                } else {
                                    foreach ($groups as $g) {
                                        // Bisa dikasih warna spesifik berdasarkan role nanti
                                        echo '<span class="px-2 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-800 rounded text-xs font-medium mr-1">' . esc($g) . '</span>';
                                    }
                                }
                            ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <?php if (auth()->user()->can('users.edit')): ?>
                                <button x-data @click="$dispatch('open-modal-edit', { id: '<?= $u->id ?>', username: '<?= esc($u->username) ?>', email: '<?= esc($u->email) ?>', group: '<?= esc($groups[0] ?? '') ?>' })" class="text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 p-2 rounded-lg transition-colors" title="Ubah Peran & Data">
                                    <i class="ph ph-pencil-simple text-lg"></i>
                                </button>
                                <?php endif; ?>
                                
                                <?php if (auth()->user()->can('users.delete')): ?>
                                <button onclick="confirmAction('Yakin hapus akun?', 'Akun <?= esc($u->username) ?> akan dihapus permanen!', 'Ya, Hapus', () => { window.location.href='<?= base_url('users/delete/' . $u->id) ?>' })" class="text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 p-2 rounded-lg transition-colors" title="Hapus Akun">
                                    <i class="ph ph-trash text-lg"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginasi Tailwind -->
    <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-800">
        <?= $pager->links() ?>
    </div>
</div>

<!-- Modal Tambah Pengguna (Alpine.js) -->
<div id="modalTambah" x-data="{ open: false }" @open-modal-tambah.window="open = true" x-show="open" style="display: none;" class="relative z-50">
    <div x-show="open" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-200" 
             x-transition:enter-start="opacity-0 scale-95" 
             x-transition:enter-end="opacity-100 scale-100" 
             x-transition:leave="transition ease-in duration-150" 
             x-transition:leave-start="opacity-100 scale-100" 
             x-transition:leave-end="opacity-0 scale-95" 
             class="bg-white dark:bg-slate-800 rounded-xl shadow-xl w-full max-w-lg overflow-hidden border border-gray-100 dark:border-slate-700"
             @click.outside="open = false">
            
            <div class="px-8 pt-8 pb-2 flex justify-between items-start bg-white dark:bg-slate-800">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-slate-100">Tambah Pengguna</h3>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Lengkapi form berikut untuk membuat akun.</p>
                </div>
                <button @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 mt-1">
                    <i class="ph ph-x text-xl"></i>
                </button>
            </div>
            
            <form action="<?= base_url('users/store') ?>" method="POST" autocomplete="off" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                <?= csrf_field() ?>
                <div class="px-8 py-4 space-y-5">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Username</label>
                        <div class="relative">
                            <i class="ph ph-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input type="text" name="username" required autocomplete="off" x-init="$watch('open', val => { if(val) setTimeout(() => $el.focus(), 200) })" class="w-full rounded-xl border-transparent bg-gray-50 dark:bg-slate-700/50 text-gray-900 dark:text-slate-100 hover:bg-white dark:hover:bg-slate-700 focus:bg-white dark:focus:bg-slate-700 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 pl-11 pr-4 py-2.5 outline-none border transition-all" placeholder="Contoh: andi.finance">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Email</label>
                        <div class="relative">
                            <i class="ph ph-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input type="email" name="email" required class="w-full rounded-xl border-transparent bg-gray-50 dark:bg-slate-700/50 text-gray-900 dark:text-slate-100 hover:bg-white dark:hover:bg-slate-700 focus:bg-white dark:focus:bg-slate-700 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 pl-11 pr-4 py-2.5 outline-none border transition-all" placeholder="andi@perusahaan.com">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Password</label>
                        <div class="relative">
                            <i class="ph ph-lock-key absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input type="password" name="password" required minlength="8" autocomplete="new-password" class="w-full rounded-xl border-transparent bg-gray-50 dark:bg-slate-700/50 text-gray-900 dark:text-slate-100 hover:bg-white dark:hover:bg-slate-700 focus:bg-white dark:focus:bg-slate-700 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 pl-11 pr-4 py-2.5 outline-none border transition-all" placeholder="Minimal 8 karakter">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Jabatan (Hak Akses)</label>
                        <div class="relative">
                            <i class="ph ph-identification-card absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                            <select name="group" required class="w-full rounded-xl border-transparent bg-gray-50 dark:bg-slate-700/50 text-gray-900 dark:text-slate-100 hover:bg-white dark:hover:bg-slate-700 focus:bg-white dark:focus:bg-slate-700 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 pl-11 pr-4 py-2.5 outline-none border transition-all appearance-none">
                                <option value="">-- Pilih Jabatan --</option>
                                <?php foreach($available_groups as $groupKey => $groupInfo): ?>
                                    <option value="<?= esc($groupKey) ?>"><?= esc($groupInfo['title']) ?> (<?= esc($groupInfo['description']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <i class="ph ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                        </div>
                    </div>

                </div>
                <div class="px-8 pb-8 pt-4 bg-white dark:bg-slate-800 flex justify-end gap-3 rounded-b-xl">
                    <button type="button" @click="open = false" class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 dark:text-slate-300 bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 transition-colors">Batal</button>
                    <button type="submit" :disabled="isSubmitting" class="bg-blue-600 text-white px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/30 flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed min-w-[130px]">
                        <i x-show="isSubmitting" class="ph ph-spinner animate-spin text-lg" style="display: none;"></i>
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Buat Akun'">Buat Akun</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<!-- Modal Edit Pengguna (Alpine.js) -->
<?= $this->section('content') ?>
<div id="modalEdit" x-data="{ open: false, id: '', username: '', email: '', group: '' }" 
     @open-modal-edit.window="open = true; id = $event.detail.id; username = $event.detail.username; email = $event.detail.email; group = $event.detail.group;" 
     x-show="open" style="display: none;" class="relative z-50">
    <div x-show="open" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-200" 
             x-transition:enter-start="opacity-0 scale-95" 
             x-transition:enter-end="opacity-100 scale-100" 
             x-transition:leave="transition ease-in duration-150" 
             x-transition:leave-start="opacity-100 scale-100" 
             x-transition:leave-end="opacity-0 scale-95" 
             class="bg-white dark:bg-slate-800 rounded-xl shadow-xl w-full max-w-lg overflow-hidden border border-gray-100 dark:border-slate-700"
             @click.outside="open = false">
            
            <div class="px-8 pt-8 pb-2 flex justify-between items-start bg-white dark:bg-slate-800">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-slate-100">Ubah Pengguna</h3>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Sesuaikan informasi dan hak akses.</p>
                </div>
                <button @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 mt-1">
                    <i class="ph ph-x text-xl"></i>
                </button>
            </div>
            
            <form action="<?= base_url('users/update') ?>" method="POST" autocomplete="off" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                <?= csrf_field() ?>
                <input type="hidden" name="id" x-model="id">
                <div class="px-8 py-4 space-y-5">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Username</label>
                        <div class="relative">
                            <i class="ph ph-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input type="text" name="username" required autocomplete="off" x-model="username" class="w-full rounded-xl border-transparent bg-gray-50 dark:bg-slate-700/50 text-gray-900 dark:text-slate-100 hover:bg-white dark:hover:bg-slate-700 focus:bg-white dark:focus:bg-slate-700 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 pl-11 pr-4 py-2.5 outline-none border transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Email</label>
                        <div class="relative">
                            <i class="ph ph-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input type="email" name="email" required x-model="email" class="w-full rounded-xl border-transparent bg-gray-50 dark:bg-slate-700/50 text-gray-900 dark:text-slate-100 hover:bg-white dark:hover:bg-slate-700 focus:bg-white dark:focus:bg-slate-700 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 pl-11 pr-4 py-2.5 outline-none border transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Password Baru (Opsional)</label>
                        <div class="relative">
                            <i class="ph ph-lock-key absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input type="password" name="password" minlength="8" autocomplete="new-password" class="w-full rounded-xl border-transparent bg-gray-50 dark:bg-slate-700/50 text-gray-900 dark:text-slate-100 hover:bg-white dark:hover:bg-slate-700 focus:bg-white dark:focus:bg-slate-700 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 pl-11 pr-4 py-2.5 outline-none border transition-all" placeholder="Kosongkan jika tidak diubah">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Jabatan (Hak Akses)</label>
                        <div class="relative">
                            <i class="ph ph-identification-card absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                            <select name="group" required x-model="group" class="w-full rounded-xl border-transparent bg-gray-50 dark:bg-slate-700/50 text-gray-900 dark:text-slate-100 hover:bg-white dark:hover:bg-slate-700 focus:bg-white dark:focus:bg-slate-700 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 pl-11 pr-4 py-2.5 outline-none border transition-all appearance-none">
                                <option value="">-- Pilih Jabatan --</option>
                                <?php foreach($available_groups as $groupKey => $groupInfo): ?>
                                    <option value="<?= esc($groupKey) ?>"><?= esc($groupInfo['title']) ?> (<?= esc($groupInfo['description']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <i class="ph ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                        </div>
                    </div>

                </div>
                <div class="px-8 pb-8 pt-4 bg-white dark:bg-slate-800 flex justify-end gap-3 rounded-b-xl">
                    <button type="button" @click="open = false" class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 dark:text-slate-300 bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 transition-colors">Batal</button>
                    <button type="submit" :disabled="isSubmitting" class="bg-emerald-600 text-white px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-500/30 flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed min-w-[170px]">
                        <i x-show="isSubmitting" class="ph ph-spinner animate-spin text-lg" style="display: none;"></i>
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Perubahan'">Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
