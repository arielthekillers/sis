<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><a href="<?= base_url('wali-murid/dashboard') ?>" class="text-gray-500 hover:text-gray-900 dark:text-slate-400">Portal Orang Tua</a></li>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Profil & Akun</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Profil & Pengaturan Akun
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Kelola informasi akun wali murid dan perbarui kata sandi untuk keamanan akses SIS.
<?= $this->endSection() ?>

<?= $this->section('page_actions') ?>
<a href="<?= base_url('wali-murid/dashboard') ?>" class="bg-gray-100 hover:bg-gray-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-gray-700 dark:text-slate-200 px-4 py-2 rounded-xl text-xs md:text-sm font-semibold transition-all flex items-center gap-2">
    <i class="ph ph-arrow-left"></i>
    <span>Kembali ke Beranda</span>
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kolom Kiri (2 Span): Biodata Akun & Data Ananda Terhubung -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Card Informasi Akun Login -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
                <div class="flex items-center gap-3 pb-4 border-b border-gray-100 dark:border-slate-700/60 mb-5">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl">
                        <i class="ph ph-user-circle"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Informasi Akun Wali Murid</h3>
                        <p class="text-xs text-gray-500 dark:text-slate-400">Identitas akun yang digunakan untuk masuk ke Sistem Informasi Sekolah.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-4 bg-gray-50 dark:bg-slate-700/40 rounded-xl border border-gray-100 dark:border-slate-700">
                        <span class="text-gray-400 font-medium block mb-1">Username Login:</span>
                        <span class="font-mono font-bold text-gray-900 dark:text-white text-sm"><?= esc($currentUser->username) ?></span>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-slate-700/40 rounded-xl border border-gray-100 dark:border-slate-700">
                        <span class="text-gray-400 font-medium block mb-1">Email Terdaftar:</span>
                        <span class="font-bold text-gray-900 dark:text-white text-sm"><?= esc($currentUser->email ?: 'Tidak ada email') ?></span>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-slate-700/40 rounded-xl border border-gray-100 dark:border-slate-700">
                        <span class="text-gray-400 font-medium block mb-1">Hak Akses (Role):</span>
                        <span class="inline-flex items-center gap-1.5 font-bold text-purple-700 dark:text-purple-300">
                            <i class="ph ph-shield-check"></i>
                            Wali Murid / Orang Tua
                        </span>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-slate-700/40 rounded-xl border border-gray-100 dark:border-slate-700">
                        <span class="text-gray-400 font-medium block mb-1">Status Akun:</span>
                        <span class="inline-flex items-center gap-1.5 font-bold text-emerald-600 dark:text-emerald-400">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Aktif & Terverifikasi
                        </span>
                    </div>
                </div>
            </div>

            <!-- Card Daftar Ananda yang Tertaut -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
                <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-slate-700/60 mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl">
                            <i class="ph ph-baby"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">Ananda yang Tertaut pada Akun Ini</h3>
                            <p class="text-xs text-gray-500 dark:text-slate-400">Data putra-putri yang terdaftar dalam satu keluarga.</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                        <?= count($detailAnakList) ?> Murid
                    </span>
                </div>

                <div class="space-y-4">
                    <?php foreach($detailAnakList as $da): ?>
                        <div class="p-5 rounded-2xl border border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/30 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <?php if(!empty($da['foto'])): ?>
                                    <img src="<?= base_url('uploads/profiles/' . $da['foto']) ?>" alt="<?= esc($da['nama_lengkap']) ?>" class="w-14 h-14 object-cover rounded-2xl border-2 border-white dark:border-slate-700 shadow-sm">
                                <?php else: ?>
                                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white font-black text-xl flex items-center justify-center border-2 border-white dark:border-slate-700 shadow-sm">
                                        <?= strtoupper(substr($da['nama_lengkap'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="text-base font-bold text-gray-900 dark:text-white"><?= esc($da['nama_lengkap']) ?></h4>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= ($da['program'] ?? 'Reguler') === 'Fullday' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300' : 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300' ?>">
                                            <?= esc($da['program'] ?? 'Reguler') ?>
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">
                                        NIS: <span class="font-mono font-medium text-gray-700 dark:text-slate-300"><?= esc($da['nis'] ?: '-') ?></span> &bull; 
                                        Kelas: <strong><?= esc($da['kelas']['nama_kelas'] ?? 'Belum Ditentukan') ?></strong> &bull; 
                                        Hubungan: <span class="capitalize font-semibold text-blue-600 dark:text-blue-400"><?= esc($da['hubungan']) ?></span>
                                    </p>
                                </div>
                            </div>

                            <a href="<?= base_url('wali-murid/dashboard?anak_id=' . $da['id']) ?>" class="px-3.5 py-1.5 rounded-xl bg-white dark:bg-slate-800 hover:bg-gray-100 dark:hover:bg-slate-700 text-xs font-bold text-blue-600 dark:text-blue-400 border border-gray-200 dark:border-slate-600 transition-colors shrink-0 flex items-center gap-1.5">
                                <span>Pilih Ananda</span>
                                <i class="ph ph-arrow-right"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan (1 Span): Ubah Password Akun -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6" x-data="{ isSubmitting: false, showOld: false, showNew: false, showConfirm: false }">
                <div class="flex items-center gap-3 pb-4 border-b border-gray-100 dark:border-slate-700/60 mb-5">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl">
                        <i class="ph ph-key"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Ubah Password</h3>
                        <p class="text-xs text-gray-500 dark:text-slate-400">Gunakan kombinasi minimal 6 karakter.</p>
                    </div>
                </div>

                <form action="<?= base_url('wali-murid/update-password') ?>" method="post" @submit="isSubmitting = true" class="space-y-4 text-xs">
                    <?= csrf_field() ?>

                    <!-- Password Saat Ini -->
                    <div>
                        <label class="block font-semibold text-gray-700 dark:text-slate-300 mb-1.5">
                            Password Saat Ini <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base">
                                <i class="ph ph-lock"></i>
                            </span>
                            <input 
                                :type="showOld ? 'text' : 'password'" 
                                name="password_sekarang" 
                                required 
                                placeholder="Masukkan password saat ini" 
                                class="w-full pl-10 pr-10 py-2.5 rounded-xl bg-gray-50 dark:bg-slate-700/50 border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-blue-500 transition-all text-xs"
                            >
                            <button type="button" @click="showOld = !showOld" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-white">
                                <i :class="showOld ? 'ph ph-eye-slash' : 'ph ph-eye'" class="text-base"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Password Baru -->
                    <div>
                        <label class="block font-semibold text-gray-700 dark:text-slate-300 mb-1.5">
                            Password Baru <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base">
                                <i class="ph ph-lock-key"></i>
                            </span>
                            <input 
                                :type="showNew ? 'text' : 'password'" 
                                name="password_baru" 
                                required 
                                minlength="6"
                                placeholder="Minimal 6 karakter" 
                                class="w-full pl-10 pr-10 py-2.5 rounded-xl bg-gray-50 dark:bg-slate-700/50 border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-blue-500 transition-all text-xs"
                            >
                            <button type="button" @click="showNew = !showNew" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-white">
                                <i :class="showNew ? 'ph ph-eye-slash' : 'ph ph-eye'" class="text-base"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Konfirmasi Password Baru -->
                    <div>
                        <label class="block font-semibold text-gray-700 dark:text-slate-300 mb-1.5">
                            Ulangi Password Baru <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base">
                                <i class="ph ph-shield-check"></i>
                            </span>
                            <input 
                                :type="showConfirm ? 'text' : 'password'" 
                                name="konfirmasi_password" 
                                required 
                                minlength="6"
                                placeholder="Ulangi password baru" 
                                class="w-full pl-10 pr-10 py-2.5 rounded-xl bg-gray-50 dark:bg-slate-700/50 border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-blue-500 transition-all text-xs"
                            >
                            <button type="button" @click="showConfirm = !showConfirm" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-white">
                                <i :class="showConfirm ? 'ph ph-eye-slash' : 'ph ph-eye'" class="text-base"></i>
                            </button>
                        </div>
                    </div>

                    <button 
                        type="submit" 
                        :disabled="isSubmitting"
                        class="w-full mt-2 py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-lg shadow-blue-500/25 transition-all flex items-center justify-center gap-2 disabled:opacity-50"
                    >
                        <i x-show="!isSubmitting" class="ph ph-floppy-disk text-base"></i>
                        <i x-show="isSubmitting" class="ph ph-spinner animate-spin text-base"></i>
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Password Baru'"></span>
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>
<?= $this->endSection() ?>

