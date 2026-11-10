<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><a href="<?= base_url('wali-kelas/dashboard') ?>" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Portal Pendidik</a></li>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Profil & Akun</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Profil & Akun Pendidik
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Kelola biodata diri, perbarui foto profil, dan atur kata sandi akun login SIS Anda.
<?= $this->endSection() ?>

<?= $this->section('page_actions') ?>
<a href="<?= base_url('wali-kelas/dashboard') ?>" class="bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 px-3.5 py-2 rounded-xl text-xs font-semibold hover:bg-gray-200 dark:hover:bg-slate-700 transition-colors flex items-center gap-1.5">
    <i class="ph ph-arrow-left"></i> Kembali ke Beranda
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6" x-data="{
    activeTab: 'biodata',
    showPwd: false,
    showConfirmPwd: false
}">

    <!-- Header Profil Card -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <?php if(!empty($guru['foto'])): ?>
                <img src="<?= base_url('uploads/profiles/' . $guru['foto']) ?>" alt="<?= esc($guru['nama_lengkap']) ?>" class="w-20 h-20 rounded-2xl object-cover border-2 border-white dark:border-slate-700 shadow-lg">
            <?php else: ?>
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-black text-2xl flex items-center justify-center shadow-lg">
                    <?= strtoupper(substr($guru['nama_panggilan'] ?: $guru['nama_lengkap'], 0, 2)) ?>
                </div>
            <?php endif; ?>

            <div>
                <div class="flex items-center gap-2 flex-wrap mb-1.5">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                        <?= esc($guru['sapaan']) ?>
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300">
                        Wali Kelas / Guru
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                        ● <?= esc($guru['status_pegawai'] ?: 'Aktif') ?>
                    </span>
                </div>

                <h3 class="text-2xl font-black text-gray-900 dark:text-white">
                    <?= esc($guru['sapaan']) ?> <?= esc($guru['nama_lengkap']) ?>
                </h3>

                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5 flex items-center gap-3">
                    <span>NIP: <strong class="font-mono text-gray-700 dark:text-slate-200"><?= esc($guru['nip'] ?: '-') ?></strong></span>
                    <span>•</span>
                    <span>Username: <strong class="font-mono text-gray-700 dark:text-slate-200"><?= esc($user->username ?? '-') ?></strong></span>
                    <span>•</span>
                    <span>WhatsApp: <strong class="font-mono text-emerald-600 dark:text-emerald-400"><?= esc($guru['no_hp'] ?: '-') ?></strong></span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 self-stretch sm:self-auto justify-end">
            <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                <?= count($kelasBinaan) ?> Rombel Diampu
            </span>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-gray-200 dark:border-slate-800">
        <button 
            type="button" 
            @click="activeTab = 'biodata'" 
            :class="activeTab === 'biodata' ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-500 dark:text-slate-400 hover:text-gray-700'"
            class="px-4 py-3 text-xs flex items-center gap-2 transition-all"
        >
            <i class="ph ph-identification-card text-base"></i>
            <span>Biodata Pendidik</span>
        </button>

        <button 
            type="button" 
            @click="activeTab = 'keamanan'" 
            :class="activeTab === 'keamanan' ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-500 dark:text-slate-400 hover:text-gray-700'"
            class="px-4 py-3 text-xs flex items-center gap-2 transition-all"
        >
            <i class="ph ph-lock-key text-base"></i>
            <span>Keamanan & Ubah Password</span>
        </button>
    </div>

    <!-- TAB 1: BIODATA & EDIT PROFIL -->
    <div x-show="activeTab === 'biodata'" class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Form Edit Biodata (2 Kolom) -->
        <div class="md:col-span-2 bg-white dark:bg-slate-800 rounded-2xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm space-y-5">
            <div class="pb-3 border-b border-gray-100 dark:border-slate-700/60">
                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Perbarui Biodata Diri</h4>
                <p class="text-xs text-gray-400 mt-0.5">Ubah nama panggilan, nomor WhatsApp, atau foto profil Anda.</p>
            </div>

            <form action="<?= base_url('wali-kelas/update-profil') ?>" method="post" enctype="multipart/form-data" class="space-y-4">
                <?= csrf_field() ?>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Sapaan (Readonly) -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-slate-400 mb-1.5">Sapaan Pendidik</label>
                        <input type="text" value="<?= esc($guru['sapaan']) ?>" disabled class="w-full px-3 py-2.5 bg-gray-100 dark:bg-slate-700/30 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-500 dark:text-slate-400 cursor-not-allowed">
                    </div>

                    <!-- Nama Lengkap & Gelar (Readonly) -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-slate-400 mb-1.5">Nama Lengkap & Gelar</label>
                        <input type="text" value="<?= esc($guru['nama_lengkap']) ?>" disabled class="w-full px-3 py-2.5 bg-gray-100 dark:bg-slate-700/30 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-500 dark:text-slate-400 cursor-not-allowed">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Nama Panggilan (Editable) -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Nama Panggilan *</label>
                        <div class="relative">
                            <i class="ph ph-user absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                            <input 
                                type="text" 
                                name="nama_panggilan" 
                                value="<?= esc($guru['nama_panggilan']) ?>" 
                                required 
                                class="w-full pl-10 pr-3 py-2.5 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 font-medium"
                            >
                        </div>
                    </div>

                    <!-- No WhatsApp (Editable) -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">No. WhatsApp / HP *</label>
                        <div class="relative">
                            <i class="ph ph-whatsapp-logo absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                            <input 
                                type="text" 
                                name="no_hp" 
                                value="<?= esc($guru['no_hp']) ?>" 
                                required 
                                placeholder="Contoh: 08123456789" 
                                class="w-full pl-10 pr-3 py-2.5 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 font-mono"
                            >
                        </div>
                    </div>
                </div>

                <!-- Foto Profil Baru -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Ganti Foto Profil (WebP Otomatis)</label>
                    <input type="file" name="foto" accept="image/*" class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-slate-700 dark:file:text-slate-300">
                    <span class="text-[11px] text-gray-400 mt-1 block">Format: JPG, PNG, atau WebP. Gambar otomatis dipotong persegi dan dikompresi.</span>
                </div>

                <div class="pt-3 border-t border-gray-100 dark:border-slate-700 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-500/25 transition-all flex items-center gap-1.5">
                        <i class="ph ph-check"></i>
                        <span>Simpan Perubahan Biodata</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Kolom Kanan: Rombel yang Diampu -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm space-y-4">
            <h4 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2 pb-3 border-b border-gray-100 dark:border-slate-700/60">
                <i class="ph ph-chalkboard text-indigo-600 dark:text-indigo-400 text-lg"></i>
                Kelas yang Diampu
            </h4>
            <div class="space-y-2.5">
                <?php if(empty($kelasBinaan)): ?>
                    <p class="text-xs text-gray-400 italic">Belum ada rombel yang ditugaskan.</p>
                <?php else: ?>
                    <?php foreach($kelasBinaan as $kb): ?>
                        <div class="p-3 bg-gray-50 dark:bg-slate-700/40 rounded-xl flex items-center justify-between">
                            <div>
                                <h5 class="text-xs font-bold text-gray-900 dark:text-white"><?= esc($kb['nama_rombel']) ?></h5>
                                <span class="text-[11px] text-gray-400">Tingkat <?= esc($kb['tingkat']) ?> • <?= $kb['total_siswa'] ?> Siswa</span>
                            </div>
                            <a href="<?= base_url('wali-kelas/murid?rombel_id=' . $kb['id']) ?>" class="p-1.5 rounded-lg text-blue-600 hover:bg-blue-50 dark:hover:bg-slate-600 transition-colors" title="Buka Data Murid">
                                <i class="ph ph-arrow-square-out text-base"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- TAB 2: KEAMANAN & UBAH PASSWORD -->
    <div x-show="activeTab === 'keamanan'" style="display: none;" class="max-w-2xl bg-white dark:bg-slate-800 rounded-2xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm space-y-5">
        <div class="pb-3 border-b border-gray-100 dark:border-slate-700/60">
            <h4 class="text-sm font-bold text-gray-900 dark:text-white">Ubah Password Akun SIS</h4>
            <p class="text-xs text-gray-400 mt-0.5">Pastikan password baru Anda kuat dan mudah diingat (minimal 8 karakter).</p>
        </div>

        <!-- Info Kredensial Saat Ini -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50 dark:bg-slate-700/40 p-4 rounded-xl text-xs mb-4">
            <div>
                <span class="text-gray-400 block text-[11px] mb-0.5">Username Login:</span>
                <strong class="font-mono text-gray-900 dark:text-white text-sm"><?= esc($user->username ?? '-') ?></strong>
            </div>
            <div>
                <span class="text-gray-400 block text-[11px] mb-0.5">Email Terdaftar:</span>
                <strong class="text-gray-900 dark:text-white text-sm"><?= esc($user->email ?? '-') ?></strong>
            </div>
        </div>

        <form action="<?= base_url('wali-kelas/update-password') ?>" method="post" class="space-y-4">
            <?= csrf_field() ?>

            <!-- Password Baru -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Password Baru *</label>
                <div class="relative">
                    <i class="ph ph-lock-key absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                    <input 
                        :type="showPwd ? 'text' : 'password'" 
                        name="password_baru" 
                        required 
                        minlength="8" 
                        placeholder="Minimal 8 karakter..." 
                        class="w-full pl-10 pr-10 py-2.5 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 font-mono font-bold"
                    >
                    <button type="button" @click="showPwd = !showPwd" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-slate-200">
                        <i class="ph" :class="showPwd ? 'ph-eye-slash' : 'ph-eye'"></i>
                    </button>
                </div>
            </div>

            <!-- Konfirmasi Password Baru -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Konfirmasi Password Baru *</label>
                <div class="relative">
                    <i class="ph ph-check-circle absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                    <input 
                        :type="showConfirmPwd ? 'text' : 'password'" 
                        name="konfirmasi_password" 
                        required 
                        minlength="8" 
                        placeholder="Ulangi password baru Anda..." 
                        class="w-full pl-10 pr-10 py-2.5 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 font-mono font-bold"
                    >
                    <button type="button" @click="showConfirmPwd = !showConfirmPwd" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-slate-200">
                        <i class="ph" :class="showConfirmPwd ? 'ph-eye-slash' : 'ph-eye'"></i>
                    </button>
                </div>
            </div>

            <div class="pt-3 border-t border-gray-100 dark:border-slate-700 flex justify-end">
                <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white shadow-md shadow-amber-500/25 transition-all flex items-center gap-1.5">
                    <i class="ph ph-key"></i>
                    <span>Simpan Password Baru</span>
                </button>
            </div>
        </form>
    </div>

</div>
<?= $this->endSection() ?>

