<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><a href="<?= base_url('civitas-akademika/guru-staf') ?>" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Data Guru & Staf</a></li>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Profil Pendidik</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Profil: <?= esc($guru['sapaan']) ?> <?= esc($guru['nama_lengkap']) ?>
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
NIP / ID: <?= esc($guru['nip'] ?: '-') ?> • Jabatan: <?= esc($guru['jabatan'] ?: 'Guru Kelas') ?>
<?= $this->endSection() ?>

<?= $this->section('page_actions') ?>
<a href="<?= base_url('civitas-akademika/guru-staf') ?>" class="bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 px-4 py-2 rounded-xl text-sm font-medium hover:bg-gray-200 dark:hover:bg-slate-700 transition-colors flex items-center gap-2">
    <i class="ph ph-arrow-left"></i> Kembali ke Daftar
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div x-data="{ activeTab: '<?= session()->getFlashdata('new_account') ? 'akun' : 'profil' ?>' }">

    <!-- Tab Navigasi -->
    <div class="flex space-x-1 border-b border-gray-200 dark:border-slate-800 mb-6 overflow-x-auto">
        <button @click="activeTab = 'profil'" :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'profil', 'border-transparent text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-300 hover:border-gray-300 dark:hover:border-slate-700': activeTab !== 'profil' }" class="px-5 py-3 border-b-2 font-medium text-sm transition-colors flex items-center gap-2 whitespace-nowrap">
            <i class="ph ph-user text-lg"></i> Biodata Pendidik
        </button>
        <button @click="activeTab = 'akun'" :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'akun', 'border-transparent text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-300 hover:border-gray-300 dark:hover:border-slate-700': activeTab !== 'akun' }" class="px-5 py-3 border-b-2 font-medium text-sm transition-colors flex items-center gap-2 whitespace-nowrap">
            <i class="ph ph-shield-check text-lg"></i> Akun Login SIS
            <?php if(!empty($linkedUser)): ?>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 font-bold">Aktif</span>
            <?php else: ?>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 font-bold">Belum Ada</span>
            <?php endif; ?>
        </button>
        <button @click="activeTab = 'kelas'" :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'kelas', 'border-transparent text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-300 hover:border-gray-300 dark:hover:border-slate-700': activeTab !== 'kelas' }" class="px-5 py-3 border-b-2 font-medium text-sm transition-colors flex items-center gap-2 whitespace-nowrap">
            <i class="ph ph-chalkboard text-lg"></i> Kelas yang Diampu
            <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 font-bold"><?= count($kelasDiampu) ?></span>
        </button>
    </div>

    <!-- KONTEN TAB 1: BIODATA PROFIL GURU/STAF -->
    <div x-show="activeTab === 'profil'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;" x-cloak>
        <div class="space-y-6">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 flex flex-col md:flex-row gap-8">
                <!-- Foto Profil -->
                <div class="flex-shrink-0 flex flex-col items-center">
                    <?php if(!empty($guru['foto'])): ?>
                        <img src="<?= base_url('uploads/profiles/' . $guru['foto']) ?>" alt="Foto <?= esc($guru['nama_lengkap']) ?>" class="w-44 h-44 object-cover rounded-2xl border-4 border-gray-100 dark:border-slate-700 shadow-sm">
                    <?php else: ?>
                        <div class="w-44 h-44 bg-gradient-to-br from-indigo-50 to-blue-100 dark:from-slate-700 dark:to-slate-800 rounded-2xl flex items-center justify-center text-indigo-500 dark:text-indigo-400 font-extrabold text-5xl border-4 border-gray-50 dark:border-slate-800 shadow-sm">
                            <?= strtoupper(substr($guru['nama_panggilan'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>

                    <span class="mt-4 px-3 py-1 rounded-full text-xs font-semibold <?= $guru['is_active'] ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' ?>">
                        <?= $guru['is_active'] ? '● Status: Aktif Bertugas' : '● Status: Nonaktif' ?>
                    </span>
                </div>

                <!-- Detail Biodata Grid -->
                <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <p class="text-xs text-gray-400 dark:text-slate-500 mb-1">Nama Lengkap & Gelar</p>
                        <p class="font-bold text-gray-900 dark:text-white text-base">
                            <?= esc($guru['sapaan']) ?> <?= esc($guru['nama_lengkap']) ?>
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400 dark:text-slate-500 mb-1">Nama Panggilan</p>
                        <p class="font-semibold text-gray-800 dark:text-slate-200"><?= esc($guru['nama_panggilan']) ?></p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400 dark:text-slate-500 mb-1">Nomor Induk Pegawai (NIP/ID)</p>
                        <p class="font-mono font-medium text-gray-800 dark:text-slate-200"><?= esc($guru['nip'] ?: '-') ?></p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400 dark:text-slate-500 mb-1">Jabatan & Tugas</p>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                            <?= esc($guru['jabatan'] ?: 'Guru Kelas') ?>
                        </span>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400 dark:text-slate-500 mb-1">Status Kepegawaian</p>
                        <p class="font-medium text-gray-800 dark:text-slate-200"><?= esc($guru['status_pegawai'] ?: 'Tetap') ?></p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400 dark:text-slate-500 mb-1">Jenis Kelamin</p>
                        <p class="font-medium text-gray-800 dark:text-slate-200"><?= $guru['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400 dark:text-slate-500 mb-1">Kontak WhatsApp / No. HP</p>
                        <?php if(!empty($guru['no_hp'])): ?>
                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $guru['no_hp']) ?>" target="_blank" class="inline-flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 font-semibold hover:underline">
                                <i class="ph ph-whatsapp-logo text-base"></i>
                                <?= esc($guru['no_hp']) ?>
                            </a>
                        <?php else: ?>
                            <p class="text-gray-400">-</p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400 dark:text-slate-500 mb-1">Tahun Masuk / Bergabung</p>
                        <p class="font-medium text-gray-800 dark:text-slate-200"><?= esc($guru['tahun_masuk'] ?: '-') ?></p>
                    </div>
                </div>
            </div>

            <!-- Kartu Statistik Singkat -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-gray-100 dark:border-slate-800 flex items-center gap-4 shadow-sm">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-2xl">
                        <i class="ph ph-chalkboard"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-slate-500">Kelas Binaan</p>
                        <h4 class="text-xl font-bold text-gray-900 dark:text-white"><?= count($kelasDiampu) ?> Rombel</h4>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-gray-100 dark:border-slate-800 flex items-center gap-4 shadow-sm">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl">
                        <i class="ph ph-notebook"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-slate-500">Catatan Jurnal Dibuat</p>
                        <h4 class="text-xl font-bold text-gray-900 dark:text-white"><?= $totalJurnal ?> Entri</h4>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-gray-100 dark:border-slate-800 flex items-center gap-4 shadow-sm">
                    <div class="w-12 h-12 rounded-xl <?= !empty($linkedUser) ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400' ?> flex items-center justify-center text-2xl">
                        <i class="ph <?= !empty($linkedUser) ? 'ph-shield-check' : 'ph-shield-warning' ?>"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-slate-500">Akun Login SIS</p>
                        <h4 class="text-sm font-bold <?= !empty($linkedUser) ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' ?>">
                            <?= !empty($linkedUser) ? 'Terhubung (' . esc($linkedUser->username) . ')' : 'Belum Dibuat' ?>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KONTEN TAB 2: AKUN LOGIN SIS (PEMBUATAN AKUN LANGSUNG & BAGIKAN) -->
    <div x-show="activeTab === 'akun'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;" x-cloak>
        <div class="space-y-6" x-data="{
            formMode: 'buat',
            password: '',
            copied: false,
            resetModalOpen: false,
            resetPasswordVal: '',
            generatePassword() {
                const pool = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ@#';
                let res = '';
                for (let i = 0; i < 8; i++) {
                    res += pool.charAt(Math.floor(Math.random() * pool.length));
                }
                this.password = res;
            },
            generateResetPassword() {
                const pool = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ@#';
                let res = '';
                for (let i = 0; i < 8; i++) {
                    res += pool.charAt(Math.floor(Math.random() * pool.length));
                }
                this.resetPasswordVal = res;
            },
            init() {
                this.generatePassword();
                this.generateResetPassword();
            },
            copyText(text) {
                navigator.clipboard.writeText(text).then(() => {
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2500);
                });
            }
        }">

            <!-- Banner Notifikasi Akun Baru yang Baru Saja Dibuat -->
            <?php $newAcc = session()->getFlashdata('new_account'); ?>
            <?php if ($newAcc): ?>
                <?php 
                    $waPesan = "Halo " . $newAcc['nama_guru'] . ",\n\nBerikut adalah informasi akun login Sistem Informasi Sekolah (SIS):\n\n🌐 Link Login: " . $newAcc['login_url'] . "\n👤 Username: *" . $newAcc['username'] . "*\n📧 Email: *" . $newAcc['email'] . "*\n🔑 Password: *" . $newAcc['password'] . "*\n💼 Peran: *" . $newAcc['group'] . "*\n\nSilakan gunakan akun ini untuk mengakses Portal Pendidik. Terima kasih.";
                    $waUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $newAcc['no_hp']) . "?text=" . urlencode($waPesan);
                    $clipText = addslashes($waPesan);
                ?>
                <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl text-white p-6 shadow-xl shadow-emerald-500/20">
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-4">
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-md mb-2">
                                <i class="ph ph-check-circle"></i> Akun Login Berhasil Dibuat & Ditautkan!
                            </span>
                            <h4 class="text-xl font-black">Data Akun: <?= esc($newAcc['nama_guru']) ?></h4>
                            <p class="text-xs text-emerald-100 mt-0.5">Informasi akun ini siap disalin atau dikirimkan langsung ke WhatsApp pendidik.</p>
                        </div>

                        <div class="flex items-center gap-2 flex-wrap">
                            <button 
                                type="button" 
                                @click="copyText(`<?= $clipText ?>`)" 
                                class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold bg-white text-emerald-800 hover:bg-emerald-50 shadow-md transition-all"
                            >
                                <i class="ph" :class="copied ? 'ph-check text-emerald-600' : 'ph-copy'"></i>
                                <span x-text="copied ? 'Tersalin!' : 'Salin Info Akun'">Salin Info Akun</span>
                            </button>
                            <?php if (!empty($newAcc['no_hp'])): ?>
                                <a 
                                    href="<?= $waUrl ?>" 
                                    target="_blank" 
                                    class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-900/40 hover:bg-emerald-900/60 text-white border border-white/20 shadow-md transition-all"
                                >
                                    <i class="ph ph-whatsapp-logo text-base"></i>
                                    <span>Kirim ke WhatsApp Guru</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Grid Rincian Kredensial Akun -->
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 bg-white/10 backdrop-blur-md rounded-xl p-4 text-xs">
                        <div>
                            <span class="text-emerald-200 block text-[11px] mb-0.5">Username:</span>
                            <strong class="text-white font-mono text-sm"><?= esc($newAcc['username']) ?></strong>
                        </div>
                        <div>
                            <span class="text-emerald-200 block text-[11px] mb-0.5">Email:</span>
                            <strong class="text-white text-sm truncate block"><?= esc($newAcc['email']) ?></strong>
                        </div>
                        <div>
                            <span class="text-emerald-200 block text-[11px] mb-0.5">Password Sementara:</span>
                            <strong class="text-amber-200 font-mono text-sm bg-black/20 px-2 py-0.5 rounded"><?= esc($newAcc['password']) ?></strong>
                        </div>
                        <div>
                            <span class="text-emerald-200 block text-[11px] mb-0.5">Hak Akses (Role):</span>
                            <strong class="text-white text-sm"><?= esc($newAcc['group']) ?></strong>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- KONDISI 1: JIKA GURU SUDAH MEMILIKI AKUN LOGIN -->
            <?php if(!empty($linkedUser)): ?>
                <?php 
                    $roleTitles = [];
                    foreach($userGroups as $grp) {
                        $roleTitles[] = config('AuthGroups')->groups[$grp]['title'] ?? ucfirst($grp);
                    }
                    $roleString = implode(', ', $roleTitles) ?: 'Wali Kelas / Guru';

                    $infoText = "Halo " . $guru['sapaan'] . " " . $guru['nama_lengkap'] . ",\n\nBerikut informasi akun login Sistem Informasi Sekolah (SIS):\n\n🌐 Link Login: " . base_url('login') . "\n👤 Username: *" . $linkedUser->username . "*\n📧 Email: *" . $linkedUser->email . "*\n🔑 Password: *" . ($linkedUser->initial_password ?? '-') . "*\n💼 Peran: *" . $roleString . "*\n\nSilakan gunakan akun ini untuk mengakses Portal Pendidik. Terima kasih.";
                    $waSendUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $guru['no_hp'] ?? '') . "?text=" . urlencode($infoText);
                ?>
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-gray-100 dark:border-slate-700/60">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-3xl">
                                <i class="ph ph-shield-check"></i>
                            </div>
                            <div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                                    ● Akun Terhubung & Aktif
                                </span>
                                <h4 class="text-xl font-bold text-gray-900 dark:text-white mt-1">
                                    <?= esc($linkedUser->username) ?>
                                </h4>
                                <p class="text-xs text-gray-400"><?= esc($linkedUser->email) ?></p>
                            </div>
                        </div>

                        <!-- Tombol Bagikan & Lepas Tautan -->
                        <div class="flex items-center gap-2 flex-wrap">
                            <!-- Tombol Reset Password Baru -->
                            <button 
                                type="button" 
                                @click="generateResetPassword(); resetModalOpen = true" 
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-amber-500 hover:bg-amber-600 text-white transition-colors shadow-sm"
                                title="Buat password baru dan salin kredensial lengkap"
                            >
                                <i class="ph ph-key text-base"></i>
                                <span>Reset Password</span>
                            </button>

                            <button 
                                type="button" 
                                @click="copyText(`<?= addslashes($infoText) ?>`)" 
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white transition-colors shadow-sm shadow-blue-500/20"
                                title="Salin kredensial lengkap beserta password"
                            >
                                <i class="ph" :class="copied ? 'ph-check text-white' : 'ph-copy'"></i>
                                <span x-text="copied ? 'Tersalin!' : 'Salin Info Login'">Salin Info Login</span>
                            </button>

                            <?php if(!empty($guru['no_hp'])): ?>
                                <a 
                                    href="<?= $waSendUrl ?>" 
                                    target="_blank" 
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:hover:bg-emerald-900/50 dark:text-emerald-300 transition-colors"
                                >
                                    <i class="ph ph-whatsapp-logo text-base"></i>
                                    <span>Kirim ke WhatsApp</span>
                                </a>
                            <?php endif; ?>

                            <button 
                                type="button"
                                onclick="confirmAction('Lepas Tautan Akun?', 'Guru ini tidak akan bisa login ke Portal Wali Kelas sampai ditautkan akun baru.', 'Ya, Lepas', () => { window.location.href='<?= base_url('civitas-akademika/guru-staf/hapus-tautan-user/' . $guru['id']) ?>' })"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors"
                            >
                                <i class="ph ph-link-break text-base"></i>
                                <span>Lepas Tautan</span>
                            </button>
                        </div>
                    </div>

                    <!-- Rincian Hak Akses & Password Akun -->
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mt-6 text-xs">
                        <!-- Peran -->
                        <div class="bg-gray-50 dark:bg-slate-700/50 p-4 rounded-xl">
                            <span class="text-gray-400 block mb-1">Peran / Hak Akses (Role):</span>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <?php foreach($userGroups as $grp): ?>
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                                        <?= esc(config('AuthGroups')->groups[$grp]['title'] ?? ucfirst($grp)) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Password Login Aktif (Dengan Toggle Show/Hide & Quick Copy) -->
                        <div class="bg-gray-50 dark:bg-slate-700/50 p-4 rounded-xl" x-data="{ showPwd: false }">
                            <span class="text-gray-400 block mb-1">Password Login:</span>
                            <div class="flex items-center gap-2 mt-0.5">
                                <code class="font-mono font-bold text-xs text-gray-900 dark:text-white bg-white dark:bg-slate-800 px-2 py-1 rounded border border-gray-200 dark:border-slate-600 tracking-wider" x-text="showPwd ? '<?= esc($linkedUser->initial_password ?? '-') ?>' : '••••••••'"></code>
                                <button 
                                    type="button" 
                                    @click="showPwd = !showPwd" 
                                    class="p-1 rounded text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 transition-colors"
                                    :title="showPwd ? 'Sembunyikan' : 'Tampilkan Password'"
                                >
                                    <i class="ph" :class="showPwd ? 'ph-eye-slash' : 'ph-eye'"></i>
                                </button>
                                <button 
                                    type="button" 
                                    @click="copyText('<?= esc($linkedUser->initial_password ?? '') ?>')" 
                                    class="p-1 rounded text-gray-400 hover:text-blue-600 transition-colors"
                                    title="Salin Password Saja"
                                >
                                    <i class="ph ph-copy"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Status Aktivasi -->
                        <div class="bg-gray-50 dark:bg-slate-700/50 p-4 rounded-xl">
                            <span class="text-gray-400 block mb-1">Status Aktivasi:</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                <i class="ph ph-check-circle"></i> Akun Aktif
                            </span>
                        </div>

                        <!-- Akses Portal -->
                        <div class="bg-gray-50 dark:bg-slate-700/50 p-4 rounded-xl">
                            <span class="text-gray-400 block mb-1">Akses Portal:</span>
                            <a href="<?= base_url('wali-kelas/dashboard') ?>" class="text-blue-600 dark:text-blue-400 font-bold hover:underline flex items-center gap-1">
                                <span>Portal Wali Kelas</span>
                                <i class="ph ph-arrow-square-out"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Modal Reset Password Guru (Alpine.js) -->
                <div 
                    x-show="resetModalOpen" 
                    x-cloak 
                    class="fixed inset-0 z-50 overflow-y-auto"
                    role="dialog" 
                    aria-modal="true"
                    style="display: none;"
                >
                    <div 
                        x-show="resetModalOpen" 
                        x-transition.opacity 
                        @click="resetModalOpen = false" 
                        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
                    ></div>

                    <div class="flex min-h-screen items-center justify-center p-4">
                        <div 
                            x-show="resetModalOpen" 
                            x-transition:enter="ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="ease-in duration-200"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="relative w-full max-w-md bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-gray-100 dark:border-slate-700 p-6 md:p-8"
                        >
                            <form action="<?= base_url('civitas-akademika/guru-staf/reset-password') ?>" method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="guru_id" value="<?= esc($guru['id']) ?>">
                                <input type="hidden" name="user_id" value="<?= esc($linkedUser->id) ?>">

                                <div class="flex items-start justify-between mb-5">
                                    <div>
                                        <span class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                                            Keamanan Akun
                                        </span>
                                        <h3 class="text-xl font-extrabold text-gray-900 dark:text-white mt-1">Reset Password Pendidik</h3>
                                        <p class="text-xs text-gray-400 mt-1">Akun: <strong><?= esc($linkedUser->username) ?></strong></p>
                                    </div>
                                    <button type="button" @click="resetModalOpen = false" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-slate-300 rounded-xl">
                                        <i class="ph ph-x text-lg"></i>
                                    </button>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <div class="flex items-center justify-between mb-1.5">
                                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300">Password Baru *</label>
                                            <button type="button" @click="generateResetPassword()" class="text-[11px] font-bold text-amber-600 dark:text-amber-400 hover:underline flex items-center gap-1">
                                                <i class="ph ph-arrows-clockwise"></i> Acak Ulang
                                            </button>
                                        </div>
                                        <div class="relative">
                                            <i class="ph ph-lock-key absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                                            <input 
                                                type="text" 
                                                name="password" 
                                                x-model="resetPasswordVal" 
                                                required 
                                                minlength="8" 
                                                class="w-full pl-10 pr-3 py-2.5 bg-gray-50 dark:bg-slate-700/50 border border-transparent focus:bg-white focus:border-amber-500 rounded-xl text-sm font-mono font-bold text-gray-900 dark:text-white"
                                            >
                                        </div>
                                        <span class="text-[11px] text-gray-400 dark:text-slate-500 mt-1.5 block leading-tight">
                                            Password baru akan otomatis diaktifkan dan siap disalin/dikirimkan ke WhatsApp guru.
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-slate-700">
                                    <button type="button" @click="resetModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">Batal</button>
                                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white shadow-md shadow-amber-500/25 transition-all">Simpan & Aktifkan Password Baru</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            <!-- KONDISI 2: JIKA BELUM MEMILIKI AKUN (SEDIAKAN FORM BUAT & TAUTKAN) -->
            <?php else: ?>
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
                    
                    <!-- Header Form & Switcher Mode -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-gray-100 dark:border-slate-700/60">
                        <div>
                            <h4 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <i class="ph ph-user-plus text-blue-600 dark:text-blue-400 text-lg"></i>
                                Buat Akun Login untuk <?= esc($guru['sapaan']) ?> <?= esc($guru['nama_lengkap']) ?>
                            </h4>
                            <p class="text-xs text-gray-500 dark:text-slate-400">Pendidik membutuhkan akun ini untuk masuk ke Portal Wali Kelas dan menginput jurnal perkembangan murid.</p>
                        </div>

                        <!-- Toggle Pilihan Mode -->
                        <div class="inline-flex p-1 bg-gray-100 dark:bg-slate-700/60 rounded-xl text-xs font-semibold self-start sm:self-auto">
                            <button 
                                type="button" 
                                @click="formMode = 'buat'" 
                                :class="formMode === 'buat' ? 'bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-gray-500 dark:text-slate-400 hover:text-gray-700'"
                                class="px-3.5 py-1.5 rounded-lg transition-all flex items-center gap-1.5"
                            >
                                <i class="ph ph-plus-circle"></i> Buat Akun Baru
                            </button>
                            <button 
                                type="button" 
                                @click="formMode = 'tautkan'" 
                                :class="formMode === 'tautkan' ? 'bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-gray-500 dark:text-slate-400 hover:text-gray-700'"
                                class="px-3.5 py-1.5 rounded-lg transition-all flex items-center gap-1.5"
                            >
                                <i class="ph ph-link"></i> Tautkan Akun Yang Ada
                            </button>
                        </div>
                    </div>

                    <!-- MODE 1: BUAT AKUN BARU LANGSUNG TAUTKAN (DEFAULT) -->
                    <div x-show="formMode === 'buat'" x-transition>
                        <?php 
                            $cleanPanggilan = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $guru['nama_panggilan'] ?: 'guru' . $guru['id']));
                            $cleanSapaan = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $guru['sapaan'] ?: 'miss'));
                            $suggestedUsername = $cleanSapaan . '.' . $cleanPanggilan;
                            $suggestedEmail = $cleanPanggilan . '@sekolah.id';
                        ?>
                        <form action="<?= base_url('civitas-akademika/guru-staf/buat-tautkan-user') ?>" method="post" class="space-y-4">
                            <?= csrf_field() ?>
                            <input type="hidden" name="guru_id" value="<?= esc($guru['id']) ?>">

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <!-- Username -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Username Login *</label>
                                    <div class="relative">
                                        <i class="ph ph-user absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                                        <input 
                                            type="text" 
                                            name="username" 
                                            value="<?= esc($suggestedUsername) ?>" 
                                            required 
                                            class="w-full pl-10 pr-3 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 font-mono font-medium"
                                        >
                                    </div>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Email *</label>
                                    <div class="relative">
                                        <i class="ph ph-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                                        <input 
                                            type="email" 
                                            name="email" 
                                            value="<?= esc($suggestedEmail) ?>" 
                                            required 
                                            class="w-full pl-10 pr-3 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:bg-white focus:border-blue-500"
                                        >
                                    </div>
                                </div>

                                <!-- Role / Hak Akses -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Hak Akses (Role) *</label>
                                    <div class="relative">
                                        <i class="ph ph-identification-badge absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                                        <select name="group" required class="w-full pl-10 pr-3 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 appearance-none font-medium">
                                            <option value="wali_kelas" selected>Wali Kelas / Guru</option>
                                            <option value="user">Karyawan Biasa</option>
                                            <option value="hr_staff">Staff HR</option>
                                            <option value="finance_staff">Staff Keuangan</option>
                                            <option value="superadmin">Super Admin</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Password Acak (Random Generator) -->
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300">Password Acak *</label>
                                        <button type="button" @click="generatePassword()" class="text-[11px] font-bold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                                            <i class="ph ph-arrows-clockwise"></i> Acak Ulang
                                        </button>
                                    </div>
                                    <div class="relative">
                                        <i class="ph ph-lock-key absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                                        <input 
                                            type="text" 
                                            name="password" 
                                            x-model="password" 
                                            required 
                                            class="w-full pl-10 pr-3 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 font-mono font-bold"
                                        >
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-2">
                                <span class="text-[11px] text-gray-400 dark:text-slate-500 flex items-center gap-1">
                                    <i class="ph ph-info"></i> Password otomatis di-generate secara acak. Anda dapat membagikannya ke WhatsApp guru setelah disimpan.
                                </span>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-xl text-xs transition-all shadow-md shadow-blue-500/25 flex items-center gap-2">
                                    <i class="ph ph-user-plus text-sm"></i>
                                    <span>Buat Akun & Tautkan Sekarang</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- MODE 2: TAUTKAN AKUN YANG SUDAH ADA -->
                    <div x-show="formMode === 'tautkan'" x-transition style="display: none;">
                        <form action="<?= base_url('civitas-akademika/guru-staf/tautkan-user') ?>" method="post" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                            <?= csrf_field() ?>
                            <input type="hidden" name="guru_id" value="<?= esc($guru['id']) ?>">

                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Pilih Akun User Terdaftar *</label>
                                <select name="user_id" required class="w-full px-3 py-2.5 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:bg-white focus:border-blue-500">
                                    <option value="">-- Pilih Akun yang Tersedia --</option>
                                    <?php foreach($availableUsers as $u): ?>
                                        <option value="<?= $u->id ?>"><?= esc($u->username) ?> (<?= esc($u->email) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2.5 rounded-xl text-xs transition-all shadow-md shadow-emerald-500/20 flex items-center justify-center gap-1.5">
                                    <i class="ph ph-link"></i>
                                    <span>Tautkan Akun Terpilih</span>
                                </button>
                            </div>
                        </form>
                        <?php if(empty($availableUsers)): ?>
                            <p class="text-xs text-amber-600 dark:text-amber-400 mt-3 flex items-center gap-1">
                                <i class="ph ph-warning-circle"></i> Tidak ada akun user menganggur. Silakan gunakan tab <em>Buat Akun Baru</em>.
                            </p>
                        <?php endif; ?>
                    </div>

                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- KONTEN TAB 3: KELAS YANG DIAMPU -->
    <div x-show="activeTab === 'kelas'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;" x-cloak>
        <div class="space-y-4">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h4 class="text-base font-bold text-gray-900 dark:text-white">Rombongan Belajar yang Ditugaskan</h4>
                    <p class="text-xs text-gray-500 dark:text-slate-400">Kelas di mana pendidik ini tercatat sebagai wali kelas / guru pendamping.</p>
                </div>
                <span class="text-xs font-semibold px-3 py-1 rounded-full bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                    Total: <?= count($kelasDiampu) ?> Rombel
                </span>
            </div>

            <?php if(empty($kelasDiampu)): ?>
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-10 text-center border border-gray-100 dark:border-slate-800">
                    <i class="ph ph-chalkboard text-4xl text-gray-400 mx-auto mb-2"></i>
                    <h4 class="text-base font-bold text-gray-800 dark:text-slate-200">Belum Ada Kelas yang Diampu</h4>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                        Guru ini belum ditugaskan sebagai wali kelas pada menu Akademik > Rombongan Belajar.
                    </p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <?php foreach($kelasDiampu as $k): ?>
                        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-slate-800 hover:shadow-md transition-all flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-bold text-sm flex items-center justify-center">
                                        <?= esc($k['tingkat']) ?>
                                    </span>
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                        TA: <?= esc($k['tahun_ajaran']) ?>
                                    </span>
                                </div>
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-1"><?= esc($k['nama_kelas']) ?></h4>
                                <p class="text-xs text-gray-400 mb-4"><?= esc($k['keterangan'] ?: 'Rombel jenjang ' . $k['tingkat']) ?></p>
                            </div>

                            <div class="pt-3 border-t border-gray-100 dark:border-slate-700/60 flex items-center justify-between text-xs">
                                <span class="text-gray-500 dark:text-slate-400 flex items-center gap-1">
                                    <i class="ph ph-users text-blue-500"></i>
                                    <strong><?= $k['total_siswa'] ?? 0 ?></strong> Murid
                                </span>
                                <a href="<?= base_url('wali-kelas/kelas/' . $k['id']) ?>" class="text-blue-600 dark:text-blue-400 font-bold hover:underline flex items-center gap-1">
                                    <span>Buka Kelas</span>
                                    <i class="ph ph-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>
<?= $this->endSection() ?>

