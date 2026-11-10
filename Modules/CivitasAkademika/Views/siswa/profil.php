<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><a href="<?= base_url('civitas-akademika/siswa') ?>" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Data Siswa</a></li>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Profil Siswa</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Profil: <?= esc($siswa['nama_lengkap']) ?>
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
NIS: <?= esc($siswa['nis'] ?? '-') ?> | NISN: <?= esc($siswa['nisn'] ?? '-') ?>
<?= $this->endSection() ?>

<?= $this->section('page_actions') ?>
<a href="<?= base_url('civitas-akademika/siswa') ?>" class="bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-200 dark:hover:bg-slate-700 transition-colors flex items-center gap-2">
    <i class="ph ph-arrow-left"></i> Kembali
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div x-data="{ activeTab: '<?= session()->getFlashdata('new_account') ? 'ortu' : 'profil' ?>' }">
    <!-- Tab Navigasi -->
    <div class="flex space-x-1 border-b border-gray-200 dark:border-slate-800 mb-6 overflow-x-auto">
        <button @click="activeTab = 'profil'" :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'profil', 'border-transparent text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-300 hover:border-gray-300 dark:hover:border-slate-700': activeTab !== 'profil' }" class="px-5 py-3 border-b-2 font-medium text-sm transition-colors flex items-center gap-2 whitespace-nowrap">
            <i class="ph ph-user text-lg"></i> Biodata Profil
        </button>
        <button @click="activeTab = 'kelas'" :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'kelas', 'border-transparent text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-300 hover:border-gray-300 dark:hover:border-slate-700': activeTab !== 'kelas' }" class="px-5 py-3 border-b-2 font-medium text-sm transition-colors flex items-center gap-2 whitespace-nowrap">
            <i class="ph ph-books text-lg"></i> History Kelas
        </button>
        <button @click="activeTab = 'pembayaran'" :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'pembayaran', 'border-transparent text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-300 hover:border-gray-300 dark:hover:border-slate-700': activeTab !== 'pembayaran' }" class="px-5 py-3 border-b-2 font-medium text-sm transition-colors flex items-center gap-2 whitespace-nowrap">
            <i class="ph ph-wallet text-lg"></i> History Pembayaran
        </button>
        <button @click="activeTab = 'ortu'" :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'ortu', 'border-transparent text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-300 hover:border-gray-300 dark:hover:border-slate-700': activeTab !== 'ortu' }" class="px-5 py-3 border-b-2 font-medium text-sm transition-colors flex items-center gap-2 whitespace-nowrap">
            <i class="ph ph-users-three text-lg"></i> Akun Orang Tua
            <?php if(!empty($orangTuaList)): ?>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 font-bold"><?= count($orangTuaList) ?></span>
            <?php endif; ?>
        </button>
        <button @click="activeTab = 'perkembangan'" :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'perkembangan', 'border-transparent text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-300 hover:border-gray-300 dark:hover:border-slate-700': activeTab !== 'perkembangan' }" class="px-5 py-3 border-b-2 font-medium text-sm transition-colors flex items-center gap-2 whitespace-nowrap">
            <i class="ph ph-notebook text-lg"></i> Jurnal Perkembangan
            <?php if(!empty($perkembanganList)): ?>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 font-bold"><?= count($perkembanganList) ?></span>
            <?php endif; ?>
        </button>
    </div>

    <!-- Konten Tab Profil -->
    <div x-show="activeTab === 'profil'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 flex flex-col md:flex-row gap-8">
            <!-- Foto Profil -->
            <div class="flex-shrink-0 flex flex-col items-center">
                <?php if(!empty($siswa['foto'])): ?>
                    <img src="<?= base_url('uploads/profiles/' . $siswa['foto']) ?>" alt="Foto <?= esc($siswa['nama_lengkap']) ?>" class="w-40 h-40 object-cover rounded-xl border-4 border-gray-100 dark:border-slate-700 shadow-sm">
                <?php else: ?>
                    <div class="w-40 h-40 bg-gray-100 dark:bg-slate-700 rounded-xl flex items-center justify-center text-gray-400 border-4 border-gray-50 dark:border-slate-800 shadow-sm">
                        <i class="ph ph-user text-6xl"></i>
                    </div>
                <?php endif; ?>
                <span class="mt-4 px-3 py-1 rounded-full text-xs font-semibold <?= ($siswa['status_siswa'] ?? 'Aktif') == 'Aktif' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' ?>">
                    Status: <?= esc($siswa['status_siswa'] ?? 'Aktif') ?>
                </span>
                <span class="mt-2 px-3 py-1 rounded-full text-xs font-semibold <?= ($siswa['program'] ?? 'Reguler') == 'Fullday' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 border border-amber-200 dark:border-amber-800' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' ?>">
                    Program: <?= esc($siswa['program'] ?? 'Reguler') ?>
                </span>
            </div>

            <!-- Detail Data -->
            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mb-1">Nama Lengkap</p>
                    <p class="font-medium text-gray-900 dark:text-slate-200"><?= esc($siswa['nama_lengkap']) ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mb-1">Nama Panggilan</p>
                    <p class="font-medium text-gray-900 dark:text-slate-200"><?= esc($siswa['nama_panggilan'] ?: '-') ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mb-1">Jenis Kelamin</p>
                    <p class="font-medium text-gray-900 dark:text-slate-200"><?= $siswa['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mb-1">Tempat, Tanggal Lahir</p>
                    <p class="font-medium text-gray-900 dark:text-slate-200"><?= esc($siswa['tempat_lahir'] ?: '-') ?>, <?= !empty($siswa['tanggal_lahir']) ? tanggal_indo($siswa['tanggal_lahir']) : '-' ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mb-1">Agama</p>
                    <p class="font-medium text-gray-900 dark:text-slate-200"><?= esc($siswa['agama'] ?: '-') ?></p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-sm text-gray-500 dark:text-slate-400 mb-1">Alamat</p>
                    <p class="font-medium text-gray-900 dark:text-slate-200"><?= esc($siswa['alamat'] ?: '-') ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mb-1">Nama Ayah</p>
                    <p class="font-medium text-gray-900 dark:text-slate-200"><?= esc($siswa['nama_ayah'] ?: '-') ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mb-1">Nama Ibu</p>
                    <p class="font-medium text-gray-900 dark:text-slate-200"><?= esc($siswa['nama_ibu'] ?: '-') ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mb-1">No. HP Orang Tua</p>
                    <p class="font-medium text-gray-900 dark:text-slate-200"><?= esc($siswa['no_hp_ortu'] ?: '-') ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Konten Tab Kelas -->
    <div x-show="activeTab === 'kelas'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;" x-cloak>
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-slate-900/50 border-b border-gray-200 dark:border-slate-700 text-sm font-medium text-gray-500 dark:text-slate-400">
                            <th class="py-3 px-4">Tahun Ajaran</th>
                            <th class="py-3 px-4">Semester</th>
                            <th class="py-3 px-4">Kelas/Rombel</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-800 text-sm">
                        <?php if (empty($historyKelas)): ?>
                        <tr>
                            <td colspan="3" class="py-6 text-center text-gray-500 dark:text-slate-400">
                                <i class="ph ph-folder-open text-3xl mb-2"></i>
                                <p>Belum ada riwayat kelas.</p>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach($historyKelas as $k): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                                <td class="py-3 px-4"><?= esc($k['tahun'] ?? '-') ?></td>
                                <td class="py-3 px-4"><?= esc($k['semester'] ?? '-') ?></td>
                                <td class="py-3 px-4 font-medium text-gray-900 dark:text-slate-200"><?= esc($k['nama_rombel'] ?? '-') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Konten Tab Pembayaran -->
    <div x-show="activeTab === 'pembayaran'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;" x-cloak>
        <div class="space-y-4">
            <div class="bg-blue-50/60 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-900/40 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <i class="ph ph-shield-check text-2xl text-blue-600 dark:text-blue-400 shrink-0"></i>
                    <div>
                        <h4 class="text-xs font-bold text-blue-900 dark:text-blue-300">Pengecekan Status SPP Siswa (Tabel 12 Bulan)</h4>
                        <p class="text-[11px] text-blue-700 dark:text-blue-400 mt-0.5">Lihat matriks status lunas Januari s/d Desember dan konfirmasi keamanan pembayaran ortu.</p>
                    </div>
                </div>
                <a href="<?= base_url('keuangan/status-spp/' . $siswa['id']) ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm transition-all shrink-0">
                    <span>Buka Status SPP</span>
                    <i class="ph ph-arrow-right"></i>
                </a>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-slate-900/50 border-b border-gray-200 dark:border-slate-700 text-sm font-medium text-gray-500 dark:text-slate-400">
                                <th class="py-3 px-4">No. Kwitansi</th>
                                <th class="py-3 px-4">Tanggal Bayar</th>
                                <th class="py-3 px-4">Metode</th>
                                <th class="py-3 px-4 text-right">Total Dibayar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-800 text-sm">
                            <?php if (empty($historyPembayaran)): ?>
                            <tr>
                                <td colspan="4" class="py-6 text-center text-gray-500 dark:text-slate-400">
                                    <i class="ph ph-wallet text-3xl mb-2"></i>
                                    <p>Belum ada riwayat pembayaran kasir.</p>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach($historyPembayaran as $p): ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                                    <td class="py-3 px-4 font-mono font-medium text-blue-600 dark:text-blue-400"><?= esc($p['no_kwitansi'] ?? '-') ?></td>
                                    <td class="py-3 px-4"><?= !empty($p['tanggal_bayar']) ? tanggal_indo($p['tanggal_bayar']) : '-' ?></td>
                                    <td class="py-3 px-4"><span class="px-2 py-0.5 rounded text-xs bg-gray-100 dark:bg-slate-700 font-semibold"><?= esc($p['metode_pembayaran'] ?? 'Tunai') ?></span></td>
                                    <td class="py-3 px-4 text-right font-semibold text-gray-900 dark:text-slate-200"><?= rupiah($p['total_bayar'] ?? 0) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Konten Tab Akun Orang Tua -->
    <div x-show="activeTab === 'ortu'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;" x-cloak>
        <div class="space-y-6" x-data="{
            formMode: 'buat',
            password: '',
            copied: false,
            resetModalOpen: false,
            resetUserId: null,
            resetUsername: '',
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
            openResetModal(userId, username) {
                this.resetUserId = userId;
                this.resetUsername = username;
                this.generateResetPassword();
                this.resetModalOpen = true;
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

            <!-- Banner Informasi Akun Baru Berhasil Dibuat (Siap Copy & Share WA) -->
            <?php $newAcc = session()->getFlashdata('new_account'); ?>
            <?php if ($newAcc): ?>
                <?php 
                    $waPesan = "Halo Bapak/Ibu,\n\nBerikut informasi akun Portal Sekolah untuk ananda *" . $newAcc['nama_siswa'] . "*:\n\n🌐 Link Login: " . $newAcc['login_url'] . "\n👤 Username: *" . $newAcc['username'] . "*\n📧 Email: *" . $newAcc['email'] . "*\n🔑 Password: *" . $newAcc['password'] . "*\n\nSilakan gunakan akun ini untuk memantau perkembangan ananda dan riwayat pembayaran SPP. Terima kasih.";
                    $waUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $newAcc['no_hp_ortu']) . "?text=" . urlencode($waPesan);
                    $clipText = addslashes($waPesan);
                ?>
                <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl text-white p-6 shadow-xl shadow-emerald-500/20">
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-4">
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-md mb-2">
                                <i class="ph ph-check-circle"></i> Akun Berhasil Dibuat & Ditautkan!
                            </span>
                            <h4 class="text-xl font-black">Data Akun Wali Murid Ananda <?= esc($newAcc['nama_siswa']) ?></h4>
                            <p class="text-xs text-emerald-100 mt-0.5">Informasi akun ini siap disalin atau dikirim langsung ke kontak WhatsApp orang tua.</p>
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
                            <?php if (!empty($newAcc['no_hp_ortu'])): ?>
                                <a 
                                    href="<?= $waUrl ?>" 
                                    target="_blank" 
                                    class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-900/40 hover:bg-emerald-900/60 text-white border border-white/20 shadow-md transition-all"
                                >
                                    <i class="ph ph-whatsapp-logo text-base"></i>
                                    <span>Kirim ke WhatsApp Ortu</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Card Rincian Kredensial -->
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
                            <span class="text-emerald-200 block text-[11px] mb-0.5">Hubungan:</span>
                            <strong class="text-white text-sm"><?= esc($newAcc['hubungan']) ?></strong>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Form Card dengan Pilihan Mode (Buat Akun Baru vs Tautkan Akun Lama) -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
                
                <!-- Header & Switcher Mode -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-gray-100 dark:border-slate-700/60">
                    <div>
                        <h4 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="ph ph-user-gear text-blue-600 dark:text-blue-400 text-lg"></i>
                            Kelola Akun Login Orang Tua
                        </h4>
                        <p class="text-xs text-gray-500 dark:text-slate-400">Hubungkan orang tua agar dapat memantau perkembangan dan tagihan ananda <?= esc($siswa['nama_lengkap']) ?>.</p>
                    </div>

                    <!-- Toggle Button Pilihan Mode -->
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

                <!-- MODE 1: FORM BUAT AKUN BARU & LANGSUNG TAUTKAN (DEFAULT) -->
                <div x-show="formMode === 'buat'" x-transition>
                    <?php 
                        $suggestedUsername = 'ortu_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $siswa['nama_panggilan'] ?: ($siswa['nis'] ?: 'siswa' . $siswa['id'])));
                        $suggestedEmail = $suggestedUsername . '@sekolah.id';
                    ?>
                    <form action="<?= base_url('civitas-akademika/siswa/buat-tautkan-orang-tua') ?>" method="post" class="space-y-4">
                        <?= csrf_field() ?>
                        <input type="hidden" name="siswa_id" value="<?= esc($siswa['id']) ?>">

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
                                        class="w-full pl-10 pr-3 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 font-mono"
                                    >
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Email Login *</label>
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

                            <!-- Hubungan -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Hubungan Keluarga *</label>
                                <div class="relative">
                                    <i class="ph ph-users absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                                    <select name="hubungan" required class="w-full pl-10 pr-3 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 appearance-none">
                                        <option value="ayah">Ayah Kandung</option>
                                        <option value="ibu">Ibu Kandung</option>
                                        <option value="wali">Wali / Keluarga</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Password Acak (Random Password Generator) -->
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
                                <i class="ph ph-info"></i> Password otomatis dibuatkan secara acak. Anda dapat menyalin data akun setelah disimpan.
                            </span>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-xl text-xs transition-all shadow-md shadow-blue-500/25 flex items-center gap-2">
                                <i class="ph ph-user-plus text-sm"></i>
                                <span>Buat Akun & Tautkan Sekarang</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- MODE 2: FORM TAUTKAN AKUN YANG SUDAH ADA -->
                <div x-show="formMode === 'tautkan'" x-transition style="display: none;">
                    <form action="<?= base_url('civitas-akademika/siswa/tautkan-orang-tua') ?>" method="post" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <?= csrf_field() ?>
                        <input type="hidden" name="siswa_id" value="<?= esc($siswa['id']) ?>">
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Pilih Akun Wali Murid *</label>
                            <select name="user_id" required class="w-full px-3 py-2.5 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:bg-white focus:border-blue-500">
                                <option value="">-- Pilih Akun Terdaftar --</option>
                                <?php foreach($availableOrtuUsers as $u): ?>
                                    <option value="<?= $u->id ?>"><?= esc($u->username) ?> (<?= esc($u->email) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Hubungan Keluarga *</label>
                            <select name="hubungan" required class="w-full px-3 py-2.5 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:bg-white focus:border-blue-500">
                                <option value="ayah">Ayah Kandung</option>
                                <option value="ibu">Ibu Kandung</option>
                                <option value="wali">Wali / Keluarga</option>
                            </select>
                        </div>

                        <div>
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2.5 rounded-xl text-xs transition-all shadow-md shadow-emerald-500/20 flex items-center justify-center gap-1.5">
                                <i class="ph ph-link"></i>
                                <span>Tautkan Akun</span>
                            </button>
                        </div>
                    </form>
                    <?php if(empty($availableOrtuUsers)): ?>
                        <p class="text-xs text-amber-600 dark:text-amber-400 mt-3 flex items-center gap-1">
                            <i class="ph ph-warning-circle"></i> Belum ada akun pengguna dengan role <strong>Wali Murid</strong> yang bisa dipilih. Silakan gunakan tab <em>Buat Akun Baru</em>.
                        </p>
                    <?php endif; ?>
                </div>

            </div>

            <!-- Daftar Akun yang Sudah Terhubung -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
                <div class="p-5 border-b border-gray-100 dark:border-slate-700/60 flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">Akun Login yang Terhubung ke Ananda</h4>
                        <p class="text-xs text-gray-400 mt-0.5">Akun ini memiliki hak akses untuk memantau data ananda di Portal Orang Tua.</p>
                    </div>
                    <span class="text-xs font-semibold px-3 py-1 rounded-full bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                        <?= count($orangTuaList) ?> Akun Terdaftar
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 dark:bg-slate-900/50 text-gray-500 dark:text-slate-400 border-b border-gray-100 dark:border-slate-700">
                            <tr>
                                <th class="py-3 px-4 font-semibold">Username Akun</th>
                                <th class="py-3 px-4 font-semibold">Email</th>
                                <th class="py-3 px-4 font-semibold">Hubungan</th>
                                <th class="py-3 px-4 font-semibold">Password</th>
                                <th class="py-3 px-4 font-semibold">Waktu Ditautkan</th>
                                <th class="py-3 px-4 text-center font-semibold">Bagikan Info & Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-800 font-medium">
                            <?php if(empty($orangTuaList)): ?>
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-gray-400">
                                        <i class="ph ph-user-circle text-3xl mb-1 block"></i>
                                        Belum ada akun orang tua yang ditautkan ke ananda ini. Buat akun pertama menggunakan form di atas.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($orangTuaList as $ot): ?>
                                    <?php 
                                        $shareText = "Halo Bapak/Ibu,\n\nBerikut informasi akun Portal Sekolah untuk ananda *" . $siswa['nama_lengkap'] . "*:\n\n🌐 Link Login: " . base_url('login') . "\n👤 Username: *" . $ot['username'] . "*\n📧 Email: *" . $ot['email'] . "*\n🔑 Password: *" . ($ot['initial_password'] ?? '-') . "*\n\nSilakan gunakan akun ini untuk memantau perkembangan dan tagihan SPP ananda. Terima kasih.";
                                        $shareWaUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $siswa['no_hp_ortu'] ?? '') . "?text=" . urlencode($shareText);
                                    ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                                        <td class="py-3 px-4 font-bold font-mono text-gray-900 dark:text-white"><?= esc($ot['username']) ?></td>
                                        <td class="py-3 px-4 text-gray-500 dark:text-slate-400"><?= esc($ot['email'] ?? '-') ?></td>
                                        <td class="py-3 px-4">
                                            <span class="px-2.5 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                                <?= ucfirst(esc($ot['hubungan'])) ?>
                                            </span>
                                        </td>
                                        <td class="py-3 px-4" x-data="{ showRowPwd: false }">
                                            <div class="flex items-center gap-1.5">
                                                <code class="font-mono text-xs text-gray-800 dark:text-gray-200 bg-gray-100 dark:bg-slate-700 px-1.5 py-0.5 rounded" x-text="showRowPwd ? '<?= esc($ot['initial_password'] ?? '-') ?>' : '••••••••'"></code>
                                                <button 
                                                    type="button" 
                                                    @click="showRowPwd = !showRowPwd" 
                                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200"
                                                    :title="showRowPwd ? 'Sembunyikan' : 'Tampilkan Password'"
                                                >
                                                    <i class="ph" :class="showRowPwd ? 'ph-eye-slash' : 'ph-eye'"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-gray-400"><?= tanggal_indo($ot['created_at'], true) ?></td>
                                        <td class="py-3 px-4 text-center">
                                            <div class="flex items-center justify-center gap-1.5">
                                                <!-- Reset Password Modal Trigger -->
                                                <button 
                                                    type="button"
                                                    @click="openResetModal(<?= (int)$ot['user_id'] ?>, '<?= esc($ot['username']) ?>')"
                                                    class="p-1.5 rounded-lg text-amber-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-slate-700 transition-colors"
                                                    title="Reset Password Akun"
                                                >
                                                    <i class="ph ph-key text-base"></i>
                                                </button>

                                                <!-- Salin Teks Info Login (Lengkap dengan Password) -->
                                                <button 
                                                    type="button"
                                                    @click="copyText(`<?= addslashes($shareText) ?>`)" 
                                                    class="p-1.5 rounded-lg text-gray-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-slate-700 transition-colors"
                                                    title="Salin Format Info Login Akun (Termasuk Password)"
                                                >
                                                    <i class="ph ph-copy text-base"></i>
                                                </button>

                                                <!-- WhatsApp Share Button -->
                                                <?php if(!empty($siswa['no_hp_ortu'])): ?>
                                                    <a 
                                                        href="<?= $shareWaUrl ?>" 
                                                        target="_blank" 
                                                        class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 dark:hover:bg-slate-700 transition-colors"
                                                        title="Kirim Format Login via WhatsApp"
                                                    >
                                                        <i class="ph ph-whatsapp-logo text-base"></i>
                                                    </a>
                                                <?php endif; ?>

                                                <!-- Lepas Tautan -->
                                                <button 
                                                    type="button"
                                                    onclick="confirmAction('Lepas Tautan?', 'Akun orang tua ini tidak akan bisa melihat data ananda lagi.', 'Ya, Lepas', () => { window.location.href='<?= base_url('civitas-akademika/siswa/hapus-tautan-orang-tua/' . $ot['id']) ?>' })"
                                                    class="p-1.5 rounded-lg text-rose-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-slate-700 transition-colors"
                                                    title="Lepas Tautan"
                                                >
                                                    <i class="ph ph-trash text-base"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Reset Password Orang Tua (Alpine.js) -->
            <div 
                x-show="resetModalOpen" 
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                style="display: none;"
                x-cloak
            >
                <div 
                    class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-md w-full p-6 text-left border border-gray-100 dark:border-slate-700 relative"
                    @click.away="resetModalOpen = false"
                >
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl">
                                <i class="ph ph-key"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">Reset Password Orang Tua</h3>
                                <p class="text-xs text-gray-400">Akun: <span class="font-mono font-bold text-gray-700 dark:text-slate-300" x-text="resetUsername"></span></p>
                            </div>
                        </div>
                        <button type="button" @click="resetModalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200">
                            <i class="ph ph-x text-lg"></i>
                        </button>
                    </div>

                    <form action="<?= base_url('civitas-akademika/siswa/reset-password-ortu') ?>" method="post" class="space-y-4">
                        <?= csrf_field() ?>
                        <input type="hidden" name="siswa_id" value="<?= esc($siswa['id']) ?>">
                        <input type="hidden" name="user_id" :value="resetUserId">

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
                                    class="w-full pl-10 pr-3 py-2.5 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:bg-white focus:border-amber-500 font-mono font-bold"
                                >
                            </div>
                            <span class="text-[11px] text-gray-400 mt-1 block">Setelah disimpan, Anda dapat langsung menyalin atau mengirimkan kredensial baru ini via WhatsApp.</span>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2">
                            <button 
                                type="button" 
                                @click="resetModalOpen = false" 
                                class="px-4 py-2 rounded-xl text-xs font-medium text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700"
                            >
                                Batal
                            </button>
                            <button 
                                type="submit" 
                                class="px-5 py-2 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white shadow-md shadow-amber-500/25 transition-all flex items-center gap-1.5"
                            >
                                <i class="ph ph-check"></i>
                                <span>Simpan & Reset</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

        <!-- Konten Tab Jurnal Perkembangan Siswa -->
        <div x-show="activeTab === 'perkembangan'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
            <div class="space-y-4">
                <?php if(empty($perkembanganList)): ?>
                    <div class="bg-white dark:bg-slate-800 rounded-xl p-10 text-center border border-gray-100 dark:border-slate-800">
                        <i class="ph ph-notebook text-3xl text-gray-400 mx-auto mb-2"></i>
                        <p class="text-sm font-semibold text-gray-700 dark:text-slate-300">Belum ada catatan perkembangan</p>
                        <p class="text-xs text-gray-400 mt-1">Catatan perkembangan diisi oleh wali kelas/pendidik.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($perkembanganList as $pl): ?>
                        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-gray-100 dark:border-slate-800 shadow-sm">
                            <div class="flex items-center justify-between mb-2">
                                <span class="px-2.5 py-0.5 rounded text-xs font-semibold bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                    <?= esc($pl['kategori']) ?>
                                </span>
                                <span class="text-xs text-gray-400"><?= tanggal_indo($pl['tanggal'], true) ?></span>
                            </div>
                            <h4 class="font-bold text-gray-900 dark:text-white text-base mb-1"><?= esc($pl['judul']) ?></h4>
                            <p class="text-sm text-gray-600 dark:text-slate-300 leading-relaxed whitespace-pre-line mb-3"><?= esc($pl['catatan']) ?></p>
                            <?php if(!empty($pl['foto'])): ?>
                                <img src="<?= base_url('uploads/perkembangan/' . $pl['foto']) ?>" alt="Dokumentasi" class="max-h-48 rounded-lg mb-3">
                            <?php endif; ?>
                            <div class="text-xs text-gray-400 pt-2 border-t border-gray-100 dark:border-slate-700 flex justify-between">
                                <span>Pendidik: <strong><?= esc($pl['sapaan_guru'] ?? '') ?> <?= esc($pl['nama_guru'] ?? '-') ?></strong></span>
                                <span class="<?= $pl['status'] === 'published' ? 'text-emerald-600 font-semibold' : 'text-amber-600' ?>"><?= ucfirst($pl['status']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
