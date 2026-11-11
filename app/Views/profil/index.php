<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Profil & Akun Saya</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Profil & Pengaturan Akun Saya
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Kelola informasi akun login Anda, perbarui alamat email, dan ganti kata sandi.
<?= $this->endSection() ?>

<?= $this->section('page_actions') ?>
<a href="<?= base_url('sis') ?>" class="bg-gray-100 hover:bg-gray-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-gray-700 dark:text-slate-200 px-4 py-2 rounded-xl text-xs md:text-sm font-semibold transition-all flex items-center gap-2">
    <i class="ph ph-arrow-left"></i>
    <span>Ke Dashboard Utama</span>
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <!-- Header Profil Card -->
    <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-slate-900 text-white rounded-2xl p-6 shadow-md relative overflow-hidden">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-6 relative z-10">
            <div class="flex items-center gap-5 text-center sm:text-left">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($currentUser->username) ?>&size=128&background=random" alt="Avatar" class="w-20 h-20 rounded-2xl border-4 border-white/20 shadow-lg shrink-0">
                <div>
                    <div class="flex items-center gap-2 flex-wrap justify-center sm:justify-start mb-1">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/20 text-white backdrop-blur">
                            <?= esc($groupTitles[0] ?? 'Pengguna') ?>
                        </span>
                        <span class="inline-flex items-center gap-1 text-[11px] text-emerald-300 font-semibold">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Akun Aktif
                        </span>
                    </div>
                    <h2 class="text-2xl font-black text-white"><?= esc($currentUser->username) ?></h2>
                    <p class="text-xs text-blue-100 mt-0.5 flex items-center gap-2 justify-center sm:justify-start">
                        <i class="ph ph-envelope text-sm"></i>
                        <span><?= esc($currentUser->email ?: 'Belum ada email') ?></span>
                    </p>
                </div>
            </div>

            <div class="bg-white/10 backdrop-blur rounded-2xl p-4 border border-white/10 text-center shrink-0 text-xs space-y-1">
                <span class="text-blue-200 block text-[11px]">Terdaftar Sejak:</span>
                <span class="font-bold text-white block text-sm"><?= tanggal_indo($currentUser->created_at, true) ?></span>
                <span class="text-[10px] text-blue-200 block">ID Pengguna: #<?= esc($currentUser->id) ?></span>
            </div>
        </div>
    </div>

    <!-- Grid 2 Kolom: Edit Akun & Ubah Password -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Kolom Kiri: Edit Informasi Akun -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6" x-data="{ isSubmittingAkun: false }">
            <div class="flex items-center gap-3 pb-4 border-b border-gray-100 dark:border-slate-700/60 mb-5">
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl shrink-0">
                    <i class="ph ph-user-gear"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Informasi Akun Login</h3>
                    <p class="text-xs text-gray-500 dark:text-slate-400">Perbarui username atau email yang digunakan untuk masuk ke sistem.</p>
                </div>
            </div>

            <form action="<?= base_url('profil/update-akun') ?>" method="POST" @submit="isSubmittingAkun = true" class="space-y-4 text-xs">
                <?= csrf_field() ?>

                <!-- Username -->
                <div>
                    <label class="block font-semibold text-gray-700 dark:text-slate-300 mb-1.5">
                        Username Login <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base">
                            <i class="ph ph-user"></i>
                        </span>
                        <input 
                            type="text" 
                            name="username" 
                            value="<?= esc(old('username', $currentUser->username)) ?>" 
                            required 
                            minlength="3"
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-gray-50 dark:bg-slate-700/50 border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-blue-500 transition-all text-xs font-medium"
                        >
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label class="block font-semibold text-gray-700 dark:text-slate-300 mb-1.5">
                        Alamat Email <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base">
                            <i class="ph ph-envelope-simple"></i>
                        </span>
                        <input 
                            type="email" 
                            name="email" 
                            value="<?= esc(old('email', $currentUser->email)) ?>" 
                            required 
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-gray-50 dark:bg-slate-700/50 border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-blue-500 transition-all text-xs font-medium"
                        >
                    </div>
                </div>

                <!-- Peran / Hak Akses (Readonly) -->
                <div>
                    <label class="block font-semibold text-gray-700 dark:text-slate-300 mb-1.5">
                        Peran & Hak Akses
                    </label>
                    <div class="flex items-center gap-2 flex-wrap">
                        <?php foreach($groupTitles as $title): ?>
                            <span class="px-3 py-1.5 rounded-xl bg-gray-100 dark:bg-slate-700 text-gray-800 dark:text-slate-200 font-bold flex items-center gap-1.5">
                                <i class="ph ph-shield-check text-blue-500"></i>
                                <?= esc($title) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="pt-3">
                    <button 
                        type="submit" 
                        :disabled="isSubmittingAkun"
                        class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/25 transition-all flex items-center gap-2 disabled:opacity-50"
                    >
                        <i x-show="!isSubmittingAkun" class="ph ph-floppy-disk text-base"></i>
                        <i x-show="isSubmittingAkun" class="ph ph-spinner animate-spin text-base"></i>
                        <span x-text="isSubmittingAkun ? 'Menyimpan...' : 'Simpan Perubahan Akun'"></span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Kolom Kanan: Ubah Kata Sandi -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6" x-data="{ isSubmittingPass: false, showOld: false, showNew: false, showConfirm: false }">
            <div class="flex items-center gap-3 pb-4 border-b border-gray-100 dark:border-slate-700/60 mb-5">
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl shrink-0">
                    <i class="ph ph-lock-key"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Ubah Kata Sandi</h3>
                    <p class="text-xs text-gray-500 dark:text-slate-400">Pastikan gunakan kombinasi sandi minimal 6 karakter demi keamanan akun.</p>
                </div>
            </div>

            <form action="<?= base_url('profil/update-password') ?>" method="POST" @submit="isSubmittingPass = true" class="space-y-4 text-xs">
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
                            placeholder="Ketik password saat ini"
                            class="w-full pl-10 pr-10 py-2.5 rounded-xl bg-gray-50 dark:bg-slate-700/50 border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-blue-500 transition-all text-xs font-mono"
                        >
                        <button type="button" @click="showOld = !showOld" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-white p-1">
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
                            <i class="ph ph-key"></i>
                        </span>
                        <input 
                            :type="showNew ? 'text' : 'password'" 
                            name="password_baru" 
                            required 
                            minlength="6"
                            placeholder="Minimal 6 karakter"
                            class="w-full pl-10 pr-10 py-2.5 rounded-xl bg-gray-50 dark:bg-slate-700/50 border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-blue-500 transition-all text-xs font-mono"
                        >
                        <button type="button" @click="showNew = !showNew" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-white p-1">
                            <i :class="showNew ? 'ph ph-eye-slash' : 'ph ph-eye'" class="text-base"></i>
                        </button>
                    </div>
                </div>

                <!-- Konfirmasi Password Baru -->
                <div>
                    <label class="block font-semibold text-gray-700 dark:text-slate-300 mb-1.5">
                        Konfirmasi Password Baru <span class="text-rose-500">*</span>
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
                            class="w-full pl-10 pr-10 py-2.5 rounded-xl bg-gray-50 dark:bg-slate-700/50 border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-blue-500 transition-all text-xs font-mono"
                        >
                        <button type="button" @click="showConfirm = !showConfirm" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-white p-1">
                            <i :class="showConfirm ? 'ph ph-eye-slash' : 'ph ph-eye'" class="text-base"></i>
                        </button>
                    </div>
                </div>

                <div class="pt-3">
                    <button 
                        type="submit" 
                        :disabled="isSubmittingPass"
                        class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-md shadow-amber-500/25 transition-all flex items-center gap-2 disabled:opacity-50"
                    >
                        <i x-show="!isSubmittingPass" class="ph ph-lock-key text-base"></i>
                        <i x-show="isSubmittingPass" class="ph ph-spinner animate-spin text-base"></i>
                        <span x-text="isSubmittingPass ? 'Menyimpan...' : 'Perbarui Password'"></span>
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>
<?= $this->endSection() ?>

