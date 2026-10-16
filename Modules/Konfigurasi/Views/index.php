<?= $this->extend('layout/main') ?>

<!-- 1. Breadcrumb -->
<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Konfigurasi Sistem</span></li>
<?= $this->endSection() ?>

<!-- 2. Judul Utama -->
<?= $this->section('page_title') ?>
Konfigurasi Sistem
<?= $this->endSection() ?>

<!-- 3. Sub Judul -->
<?= $this->section('page_subtitle') ?>
Kelola profil lembaga, pejabat penandatangan, format kuitansi, integrasi WhatsApp (Wapify), dan preferensi sistem.
<?= $this->endSection() ?>

<!-- 4. Tombol Aksi Kanan Atas -->
<?= $this->section('page_actions') ?>
<button type="submit" form="form-konfigurasi" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-lg shadow-blue-500/25 active:scale-95">
    <i class="ph ph-floppy-disk text-lg"></i>
    <span>Simpan Perubahan</span>
</button>
<?= $this->endSection() ?>

<!-- 5. Konten Utama -->
<?= $this->section('content') ?>
<div x-data="{ 
    activeTab: 'identitas',
    isSubmitting: false,
    showApiKey: false,
    isTestingConn: false,
    connStatus: null,
    connMessage: '',
    isTestingSend: false,
    testPhone: '<?= esc($settings['school_whatsapp'] ?? '') ?>',
    testMessage: 'Halo! Ini adalah pesan pengujian koneksi WhatsApp Wapify dari <?= esc(app_setting('app_name', 'SIS')) ?>.',
    logoPreview: '<?= base_url($settings['school_logo'] ?? 'uploads/logo.png') ?>',
    faviconPreview: '<?= base_url($settings['school_favicon'] ?? 'uploads/logo.png') ?>',
    previewImage(event, target) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                if (target === 'logo') this.logoPreview = e.target.result;
                if (target === 'favicon') this.faviconPreview = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    },
    async testWapifyConnection() {
        this.isTestingConn = true;
        this.connStatus = null;
        this.connMessage = '';

        const form = document.getElementById('form-konfigurasi');
        const formData = new FormData();
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
        formData.append('api_url', form.querySelector('[name=wapify_api_url]').value);
        formData.append('api_key', form.querySelector('[name=wapify_api_key]').value);
        formData.append('session', form.querySelector('[name=wapify_session]').value);

        try {
            const res = await fetch('<?= base_url('konfigurasi/test-wapify-connection') ?>', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            
            if (data.status === 'success') {
                if (data.is_connected) {
                    this.connStatus = 'connected';
                    this.connMessage = 'Sesi ' + data.target_session + ' terhubung & aktif (WORKING)!';
                    Swal.fire({
                        icon: 'success',
                        title: 'Koneksi Berhasil!',
                        text: 'Sesi WhatsApp ' + data.target_session + ' aktif dan siap mengirim pesan.',
                        timer: 3000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                } else {
                    this.connStatus = 'disconnected';
                    this.connMessage = 'Sesi ditemukan di akun Anda, tetapi belum terhubung / belum scan QR WhatsApp.';
                    Swal.fire({
                        icon: 'warning',
                        title: 'Sesi Belum Terhubung',
                        text: 'Pastikan sesi ' + data.target_session + ' sudah di-scan di dashboard Wapify.',
                    });
                }
            } else {
                this.connStatus = 'error';
                this.connMessage = data.message || 'Gagal terhubung ke Wapify API.';
                Swal.fire({
                    icon: 'error',
                    title: 'Koneksi Gagal',
                    text: this.connMessage,
                });
            }
        } catch (err) {
            this.connStatus = 'error';
            this.connMessage = 'Kesalahan jaringan: ' + err.message;
        } finally {
            this.isTestingConn = false;
        }
    },
    async sendWapifyTest() {
        if (!this.testPhone) {
            Swal.fire('Perhatian', 'Silakan masukkan nomor WhatsApp tujuan tes.', 'warning');
            return;
        }

        this.isTestingSend = true;
        const form = document.getElementById('form-konfigurasi');
        const formData = new FormData();
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
        formData.append('api_url', form.querySelector('[name=wapify_api_url]').value);
        formData.append('api_key', form.querySelector('[name=wapify_api_key]').value);
        formData.append('session', form.querySelector('[name=wapify_session]').value);
        formData.append('phone', this.testPhone);
        formData.append('message', this.testMessage);

        try {
            const res = await fetch('<?= base_url('konfigurasi/test-wapify-send') ?>', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Pesan Terkirim!',
                    text: 'Pesan WhatsApp berhasil dikirim ke ' + this.testPhone,
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Kirim Pesan',
                    text: data.message || 'Terjadi kesalahan saat pengiriman pesan.',
                });
            }
        } catch (err) {
            Swal.fire('Error', 'Kesalahan jaringan: ' + err.message, 'error');
        } finally {
            this.isTestingSend = false;
        }
    }
}" class="space-y-6">

    <!-- Card Wrapper -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
        
        <!-- Tab Navigation Bar -->
        <div class="flex items-center gap-2 border-b border-gray-100 dark:border-slate-800 px-6 pt-4 bg-gray-50/50 dark:bg-slate-900/40 overflow-x-auto custom-scrollbar">
            <button type="button" @click="activeTab = 'identitas'" 
                :class="activeTab === 'identitas' ? 'text-blue-600 dark:text-blue-400 border-blue-600 dark:border-blue-400 font-semibold' : 'text-gray-500 dark:text-slate-400 border-transparent hover:text-gray-700 dark:hover:text-slate-300 font-medium'"
                class="flex items-center gap-2 py-3 px-4 border-b-2 text-sm transition-all whitespace-nowrap">
                <i class="ph ph-buildings text-lg"></i>
                <span>Profil & Branding Lembaga</span>
            </button>

            <button type="button" @click="activeTab = 'pejabat'" 
                :class="activeTab === 'pejabat' ? 'text-blue-600 dark:text-blue-400 border-blue-600 dark:border-blue-400 font-semibold' : 'text-gray-500 dark:text-slate-400 border-transparent hover:text-gray-700 dark:hover:text-slate-300 font-medium'"
                class="flex items-center gap-2 py-3 px-4 border-b-2 text-sm transition-all whitespace-nowrap">
                <i class="ph ph-user-check text-lg"></i>
                <span>Pejabat & Penandatangan</span>
            </button>

            <button type="button" @click="activeTab = 'keuangan'" 
                :class="activeTab === 'keuangan' ? 'text-blue-600 dark:text-blue-400 border-blue-600 dark:border-blue-400 font-semibold' : 'text-gray-500 dark:text-slate-400 border-transparent hover:text-gray-700 dark:hover:text-slate-300 font-medium'"
                class="flex items-center gap-2 py-3 px-4 border-b-2 text-sm transition-all whitespace-nowrap">
                <i class="ph ph-receipt text-lg"></i>
                <span>Keuangan & Kuitansi</span>
            </button>

            <button type="button" @click="activeTab = 'wapify'" 
                :class="activeTab === 'wapify' ? 'text-emerald-600 dark:text-emerald-400 border-emerald-600 dark:border-emerald-400 font-semibold' : 'text-gray-500 dark:text-slate-400 border-transparent hover:text-gray-700 dark:hover:text-slate-300 font-medium'"
                class="flex items-center gap-2 py-3 px-4 border-b-2 text-sm transition-all whitespace-nowrap">
                <i class="ph ph-whatsapp-logo text-lg text-emerald-500"></i>
                <span>Integrasi WhatsApp (Wapify)</span>
            </button>

            <button type="button" @click="activeTab = 'sistem'" 
                :class="activeTab === 'sistem' ? 'text-blue-600 dark:text-blue-400 border-blue-600 dark:border-blue-400 font-semibold' : 'text-gray-500 dark:text-slate-400 border-transparent hover:text-gray-700 dark:hover:text-slate-300 font-medium'"
                class="flex items-center gap-2 py-3 px-4 border-b-2 text-sm transition-all whitespace-nowrap">
                <i class="ph ph-sliders text-lg"></i>
                <span>Pengaturan Sistem</span>
            </button>
        </div>

        <!-- Form Elements -->
        <form id="form-konfigurasi" action="<?= base_url('konfigurasi/save') ?>" method="POST" enctype="multipart/form-data" @submit="isSubmitting = true" class="p-6 sm:p-8">
            <?= csrf_field() ?>

            <!-- ========================================== -->
            <!-- TAB 1: IDENTITAS & BRANDING LEMBAGA        -->
            <!-- ========================================== -->
            <div x-show="activeTab === 'identitas'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-8">
                
                <!-- Section: Logo & Brand Visual -->
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Identitas Visual & Logo</h3>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mb-5">Logo utama akan tampil pada navbar, sidebar portal, serta kop resmi kuitansi/laporan.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-5 rounded-2xl bg-gray-50/70 dark:bg-slate-900/50 border border-gray-100 dark:border-slate-800">
                        <!-- Logo Sekolah -->
                        <div class="flex items-center gap-5">
                            <div class="w-20 h-20 rounded-2xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 flex items-center justify-center p-2 overflow-hidden shadow-sm shrink-0">
                                <img :src="logoPreview" alt="Logo Preview" class="max-h-full max-w-full object-contain">
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Logo Utama Sekolah</label>
                                <p class="text-[11px] text-gray-500 dark:text-slate-400 mb-2">Format PNG/WebP transparan (Maks. 2MB)</p>
                                <input type="file" name="school_logo" accept="image/*" @change="previewImage($event, 'logo')" class="block w-full text-xs text-gray-500 dark:text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-600/20 dark:file:text-blue-300">
                            </div>
                        </div>

                        <!-- Favicon -->
                        <div class="flex items-center gap-5">
                            <div class="w-20 h-20 rounded-2xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 flex items-center justify-center p-2 overflow-hidden shadow-sm shrink-0">
                                <img :src="faviconPreview" alt="Favicon Preview" class="w-8 h-8 object-contain">
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Favicon Browser</label>
                                <p class="text-[11px] text-gray-500 dark:text-slate-400 mb-2">Ikon kecil tab browser (Rasio 1:1, Maks. 1MB)</p>
                                <input type="file" name="school_favicon" accept="image/*" @change="previewImage($event, 'favicon')" class="block w-full text-xs text-gray-500 dark:text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-600/20 dark:file:text-blue-300">
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100 dark:border-slate-800">

                <!-- Section: Informasi Umum Sekolah -->
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Informasi & Profil Sekolah</h3>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mb-5">Data legalitas dan penamaan aplikasi.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        
                        <!-- Nama Resmi Lembaga -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Nama Resmi Sekolah / Yayasan</label>
                            <div class="relative">
                                <i class="ph ph-buildings absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                                <input type="text" name="school_name" value="<?= esc($settings['school_name'] ?? '') ?>" required
                                    class="w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-sm font-medium text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all"
                                    placeholder="Contoh: Rumah Semut Kecil">
                            </div>
                        </div>

                        <!-- Nama Aplikasi (Brand Pendek) -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Nama Brand / Singkatan Aplikasi</label>
                            <div class="relative">
                                <i class="ph ph-app-window absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                                <input type="text" name="app_name" value="<?= esc($settings['app_name'] ?? '') ?>" required
                                    class="w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-sm font-medium text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all"
                                    placeholder="Contoh: RSK APPS">
                            </div>
                        </div>

                        <!-- Tagline / Subtitle -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Jenjang / Tagline Aplikasi</label>
                            <div class="relative">
                                <i class="ph ph-tag absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                                <input type="text" name="app_tagline" value="<?= esc($settings['app_tagline'] ?? '') ?>"
                                    class="w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-sm font-medium text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all"
                                    placeholder="Contoh: Preschool & Kindergarten">
                            </div>
                        </div>

                        <!-- NPSN -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">NPSN</label>
                            <div class="relative">
                                <i class="ph ph-identification-card absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                                <input type="text" name="school_npsn" value="<?= esc($settings['school_npsn'] ?? '') ?>"
                                    class="w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-sm font-medium text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all"
                                    placeholder="Contoh: 69912345">
                            </div>
                        </div>

                        <!-- Akreditasi -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Status Akreditasi</label>
                            <div class="relative">
                                <i class="ph ph-certificate absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                                <input type="text" name="school_akreditasi" value="<?= esc($settings['school_akreditasi'] ?? '') ?>"
                                    class="w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-sm font-medium text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all"
                                    placeholder="Contoh: A (Unggul)">
                            </div>
                        </div>

                        <!-- Website -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Alamat Website</label>
                            <div class="relative">
                                <i class="ph ph-globe absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                                <input type="url" name="school_website" value="<?= esc($settings['school_website'] ?? '') ?>"
                                    class="w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-sm font-medium text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all"
                                    placeholder="Contoh: https://rsk.sch.id">
                            </div>
                        </div>

                    </div>
                </div>

                <hr class="border-gray-100 dark:border-slate-800">

                <!-- Section: Kontak & Lokasi -->
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Kontak & Alamat Lembaga</h3>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mb-5">Digunakan pada kop surat, footer, dan informasi layanan bantuan.</p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                        <!-- Telepon -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Telepon Kantor</label>
                            <div class="relative">
                                <i class="ph ph-phone absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                                <input type="text" name="school_phone" value="<?= esc($settings['school_phone'] ?? '') ?>"
                                    class="w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-sm font-medium text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all"
                                    placeholder="Contoh: 022-2501234">
                            </div>
                        </div>

                        <!-- WhatsApp -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">No. WhatsApp Resmi Sekolah</label>
                            <div class="relative">
                                <i class="ph ph-whatsapp-logo absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                                <input type="text" name="school_whatsapp" value="<?= esc($settings['school_whatsapp'] ?? '') ?>"
                                    class="w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-sm font-medium text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all"
                                    placeholder="Contoh: 081234567890">
                            </div>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Email Resmi</label>
                            <div class="relative">
                                <i class="ph ph-envelope-simple absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                                <input type="email" name="school_email" value="<?= esc($settings['school_email'] ?? '') ?>"
                                    class="w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-sm font-medium text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all"
                                    placeholder="Contoh: info@rsk.sch.id">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <!-- Alamat Lengkap (2 Col) -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Alamat Jalan / Gedung</label>
                            <div class="relative">
                                <i class="ph ph-map-pin absolute left-4 top-3.5 text-gray-400 text-lg"></i>
                                <textarea name="school_address" rows="2"
                                    class="w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-sm font-medium text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all"
                                    placeholder="Alamat lengkap sekolah..."><?= esc($settings['school_address'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <!-- Kota & Kode Pos -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Kota / Kabupaten</label>
                                <input type="text" name="school_city" value="<?= esc($settings['school_city'] ?? '') ?>"
                                    class="w-full px-4 py-2 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-sm font-medium text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all"
                                    placeholder="Contoh: Kota Bandung">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Kode Pos</label>
                                <input type="text" name="school_postal_code" value="<?= esc($settings['school_postal_code'] ?? '') ?>"
                                    class="w-full px-4 py-2 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-sm font-medium text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all"
                                    placeholder="Contoh: 40135">
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <!-- ========================================== -->
            <!-- TAB 2: PEJABAT & PENANDATANGAN DOKUMEN     -->
            <!-- ========================================== -->
            <div x-show="activeTab === 'pejabat'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="border-b border-gray-100 dark:border-slate-800 pb-4">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Pejabat Resmi & Penandatangan</h3>
                    <p class="text-xs text-gray-500 dark:text-slate-400">Data pimpinan ini akan otomatis tertera pada raport siswa, surat pengantar, serta kuitansi pembayaran.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Kepala Sekolah Card -->
                    <div class="p-5 rounded-2xl bg-gray-50/70 dark:bg-slate-900/50 border border-gray-100 dark:border-slate-800 space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-600/20 dark:text-blue-400 flex items-center justify-center text-xl shrink-0">
                                <i class="ph ph-user-circle-gear"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Kepala Sekolah</h4>
                                <p class="text-[11px] text-gray-500 dark:text-slate-400">Pimpinan tertinggi institusi</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Nama Lengkap & Gelar</label>
                            <input type="text" name="headmaster_name" value="<?= esc($settings['headmaster_name'] ?? '') ?>"
                                class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border-gray-200 dark:border-slate-700 rounded-xl text-sm font-medium text-gray-900 dark:text-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all"
                                placeholder="Contoh: Hj. Siti Nurhaliza, S.Pd., M.M.">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">NIP / NUPTK</label>
                            <input type="text" name="headmaster_nip" value="<?= esc($settings['headmaster_nip'] ?? '') ?>"
                                class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border-gray-200 dark:border-slate-700 rounded-xl text-sm font-mono text-gray-900 dark:text-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all"
                                placeholder="Contoh: 19850101 201001 2 001">
                        </div>
                    </div>

                    <!-- Kasir / Bendahara Card -->
                    <div class="p-5 rounded-2xl bg-gray-50/70 dark:bg-slate-900/50 border border-gray-100 dark:border-slate-800 space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-600/20 dark:text-emerald-400 flex items-center justify-center text-xl shrink-0">
                                <i class="ph ph-wallet"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Petugas Kasir / Bendahara</h4>
                                <p class="text-[11px] text-gray-500 dark:text-slate-400">Default penandatangan bukti pembayaran</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Nama Default di Kuitansi</label>
                            <input type="text" name="cashier_name" value="<?= esc($settings['cashier_name'] ?? '') ?>"
                                class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border-gray-200 dark:border-slate-700 rounded-xl text-sm font-medium text-gray-900 dark:text-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all"
                                placeholder="Contoh: Admin Keuangan / Bendahara">
                        </div>

                        <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 text-xs text-amber-700 dark:text-amber-300 flex items-start gap-2">
                            <i class="ph ph-info text-base shrink-0 mt-0.5"></i>
                            <span>Nama ini akan tampil di bagian tanda tangan bawah kuitansi kasir pembayaran secara otomatis.</span>
                        </div>
                    </div>

                </div>

            </div>

            <!-- ========================================== -->
            <!-- TAB 3: KEUANGAN & KUITANSI                 -->
            <!-- ========================================== -->
            <div x-show="activeTab === 'keuangan'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="border-b border-gray-100 dark:border-slate-800 pb-4">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Pengaturan Kuitansi & Rekening Bank</h3>
                    <p class="text-xs text-gray-500 dark:text-slate-400">Atur format penomoran nota kuitansi dan rekening resmi sekolah untuk pembayaran transfer.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Format & Catatan Kuitansi -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Prefix / Awalan Nomor Kuitansi</label>
                            <div class="relative">
                                <i class="ph ph-hash absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                                <input type="text" name="receipt_prefix" value="<?= esc($settings['receipt_prefix'] ?? 'KW') ?>"
                                    class="w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-sm font-mono font-bold text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all"
                                    placeholder="Contoh: KW atau INV">
                            </div>
                            <p class="text-[11px] text-gray-500 dark:text-slate-400 mt-1">Nomor kuitansi akan berformat: <code class="font-mono text-blue-600 dark:text-blue-400"><?= esc($settings['receipt_prefix'] ?? 'KW') ?>-YYYYMMDD-XXXX</code></p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Catatan Kaki Kuitansi (Footer)</label>
                            <textarea name="receipt_footer" rows="4"
                                class="w-full p-4 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-sm text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all"
                                placeholder="Pesan atau disclaimer di bawah kuitansi..."><?= esc($settings['receipt_footer'] ?? '') ?></textarea>
                            <p class="text-[11px] text-gray-500 dark:text-slate-400 mt-1">Dicetak di bagian paling bawah lembar kuitansi pembayaran.</p>
                        </div>
                    </div>

                    <!-- Rekening Bank Sekolah -->
                    <div class="p-5 rounded-2xl bg-gray-50/70 dark:bg-slate-900/50 border border-gray-100 dark:border-slate-800 space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 dark:bg-sky-600/20 dark:text-sky-400 flex items-center justify-center text-xl shrink-0">
                                <i class="ph ph-bank"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Rekening Bank Resmi Sekolah</h4>
                                <p class="text-[11px] text-gray-500 dark:text-slate-400">Untuk instruksi transfer wali murid</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Nama Bank</label>
                            <input type="text" name="bank_name" value="<?= esc($settings['bank_name'] ?? '') ?>"
                                class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border-gray-200 dark:border-slate-700 rounded-xl text-sm font-medium text-gray-900 dark:text-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all"
                                placeholder="Contoh: Bank Mandiri / BCA / BSI">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Nomor Rekening</label>
                            <input type="text" name="bank_account_number" value="<?= esc($settings['bank_account_number'] ?? '') ?>"
                                class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border-gray-200 dark:border-slate-700 rounded-xl text-sm font-mono font-bold text-gray-900 dark:text-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all"
                                placeholder="Contoh: 130-00-1234567-8">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Atas Nama Rekening</label>
                            <input type="text" name="bank_account_holder" value="<?= esc($settings['bank_account_holder'] ?? '') ?>"
                                class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border-gray-200 dark:border-slate-700 rounded-xl text-sm font-medium text-gray-900 dark:text-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all"
                                placeholder="Contoh: Yayasan Rumah Semut Kecil">
                        </div>
                    </div>

                </div>

            </div>

            <!-- ========================================== -->
            <!-- TAB 4: INTEGRASI WHATSAPP (WAPIFY)        -->
            <!-- ========================================== -->
            <div x-show="activeTab === 'wapify'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-8">
                
                <!-- Header Card Wapify -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-2xl bg-gradient-to-r from-emerald-50 via-teal-50 to-sky-50 dark:from-emerald-950/30 dark:via-slate-900 dark:to-slate-900 border border-emerald-100 dark:border-emerald-900/30">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-2xl shadow-lg shadow-emerald-600/25 shrink-0">
                            <i class="ph ph-whatsapp-logo"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">Wapify Gateway API</h3>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">Resmi</span>
                            </div>
                            <p class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">Kirim kuitansi kasir, notifikasi tagihan SPP, dan pengumuman sekolah langsung ke WhatsApp wali murid.</p>
                        </div>
                    </div>

                    <a href="https://wapify.biz.id/docs" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3.5 py-2 rounded-xl bg-white dark:bg-slate-800 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-50 transition-colors shadow-xs shrink-0 self-start sm:self-auto">
                        <i class="ph ph-book-open text-base"></i>
                        <span>Buka Dokumentasi API</span>
                        <i class="ph ph-arrow-square-out text-xs"></i>
                    </a>
                </div>

                <!-- Form Konfigurasi Wapify -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    
                    <!-- Left Column: Kredensial API -->
                    <div class="space-y-5">
                        
                        <!-- Toggle Switch Aktif/Nonaktif -->
                        <div class="p-4 rounded-xl bg-gray-50 dark:bg-slate-900/50 border border-gray-100 dark:border-slate-800 flex items-center justify-between">
                            <div>
                                <label class="text-sm font-bold text-gray-900 dark:text-white">Aktifkan Pengiriman WhatsApp</label>
                                <p class="text-xs text-gray-500 dark:text-slate-400">Jika dinonaktifkan, sistem tidak akan mengirim pesan WhatsApp keluar.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="wapify_is_active" value="0">
                                <input type="checkbox" name="wapify_is_active" value="1" <?= ($settings['wapify_is_active'] ?? '0') === '1' ? 'checked' : '' ?> class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>

                        <!-- Wapify API Base URL -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">API Base URL</label>
                            <div class="relative">
                                <i class="ph ph-link absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                                <input type="url" name="wapify_api_url" value="<?= esc($settings['wapify_api_url'] ?? 'https://wapify.biz.id/v1') ?>" required
                                    class="w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-sm font-mono text-gray-900 dark:text-white focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all"
                                    placeholder="https://wapify.biz.id/v1">
                            </div>
                            <p class="text-[11px] text-gray-500 dark:text-slate-400 mt-1">Default resmi: <code class="font-mono text-emerald-600">https://wapify.biz.id/v1</code></p>
                        </div>

                        <!-- Secret API Key -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Secret API Key (wsk_...)</label>
                            <div class="relative">
                                <i class="ph ph-key absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                                <input :type="showApiKey ? 'text' : 'password'" name="wapify_api_key" value="<?= esc($settings['wapify_api_key'] ?? '') ?>"
                                    class="w-full pl-11 pr-12 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-sm font-mono text-gray-900 dark:text-white focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all"
                                    placeholder="wsk_xxxxxxxxxxxxxxxxxxxxxx">
                                <button type="button" @click="showApiKey = !showApiKey" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 p-1">
                                    <i :class="showApiKey ? 'ph ph-eye-slash' : 'ph ph-eye'" class="text-lg"></i>
                                </button>
                            </div>
                            <p class="text-[11px] text-gray-500 dark:text-slate-400 mt-1">Dapatkan di menu <strong>API Keys</strong> pada dashboard Wapify Anda.</p>
                        </div>

                        <!-- Session Name -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Nama Sesi WhatsApp (Session)</label>
                            <div class="relative">
                                <i class="ph ph-device-mobile-camera absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                                <input type="text" name="wapify_session" value="<?= esc($settings['wapify_session'] ?? 'default') ?>" required
                                    class="w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-sm font-mono text-gray-900 dark:text-white focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all"
                                    placeholder="Contoh: default atau rsk_wa">
                            </div>
                            <p class="text-[11px] text-gray-500 dark:text-slate-400 mt-1">Nama sesi WhatsApp yang dibuat di menu <strong>Sessions</strong> pada Wapify.</p>
                        </div>

                    </div>

                    <!-- Right Column: Card Uji Coba & Status Koneksi -->
                    <div class="space-y-6">
                        
                        <!-- Box 1: Cek Status Sesi -->
                        <div class="p-5 rounded-2xl bg-gray-50/80 dark:bg-slate-900/50 border border-gray-100 dark:border-slate-800 space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-600/20 dark:text-emerald-400 flex items-center justify-center text-lg">
                                        <i class="ph ph-plugs-connected"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">Uji Koneksi Server & Sesi</h4>
                                        <p class="text-[11px] text-gray-500 dark:text-slate-400">Verifikasi apakah API Key dan Sesi sudah terhubung</p>
                                    </div>
                                </div>

                                <button type="button" @click="testWapifyConnection()" :disabled="isTestingConn" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white transition-all shadow-sm">
                                    <i class="ph ph-spinner animate-spin" x-show="isTestingConn" style="display: none;"></i>
                                    <i class="ph ph-arrows-clockwise" x-show="!isTestingConn"></i>
                                    <span x-text="isTestingConn ? 'Mengecek...' : 'Cek Status'">Cek Status</span>
                                </button>
                            </div>

                            <!-- Status Badge Box -->
                            <div x-show="connStatus" style="display: none;" class="p-3.5 rounded-xl text-xs transition-all"
                                :class="{
                                    'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800': connStatus === 'connected',
                                    'bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800': connStatus === 'disconnected',
                                    'bg-red-50 text-red-800 dark:bg-red-950/40 dark:text-red-300 border border-red-200 dark:border-red-800': connStatus === 'error'
                                }">
                                <div class="flex items-start gap-2">
                                    <i class="ph ph-check-circle text-base shrink-0 mt-0.5" x-show="connStatus === 'connected'"></i>
                                    <i class="ph ph-warning-circle text-base shrink-0 mt-0.5" x-show="connStatus === 'disconnected'"></i>
                                    <i class="ph ph-x-circle text-base shrink-0 mt-0.5" x-show="connStatus === 'error'"></i>
                                    <span x-text="connMessage" class="font-medium"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Box 2: Kirim Pesan Uji Coba -->
                        <div class="p-5 rounded-2xl bg-gray-50/80 dark:bg-slate-900/50 border border-gray-100 dark:border-slate-800 space-y-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 dark:bg-sky-600/20 dark:text-sky-400 flex items-center justify-center text-lg">
                                    <i class="ph ph-paper-plane-tilt"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white">Kirim Pesan Uji Coba</h4>
                                    <p class="text-[11px] text-gray-500 dark:text-slate-400">Kirim pesan WhatsApp percobaan ke nomor tertentu</p>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 dark:text-slate-300 mb-1">Nomor WhatsApp Tujuan</label>
                                    <input type="text" x-model="testPhone" placeholder="08xxxxxxxxxx / 628xxxxxxxxxx"
                                        class="w-full px-3.5 py-2 bg-white dark:bg-slate-800 border-gray-200 dark:border-slate-700 rounded-xl text-xs font-mono font-medium text-gray-900 dark:text-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all">
                                </div>

                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 dark:text-slate-300 mb-1">Teks Pesan</label>
                                    <textarea x-model="testMessage" rows="2"
                                        class="w-full px-3.5 py-2 bg-white dark:bg-slate-800 border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all"></textarea>
                                </div>

                                <button type="button" @click="sendWapifyTest()" :disabled="isTestingSend" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 text-xs font-semibold rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white transition-all shadow-sm">
                                    <i class="ph ph-spinner animate-spin" x-show="isTestingSend" style="display: none;"></i>
                                    <i class="ph ph-whatsapp-logo text-base" x-show="!isTestingSend"></i>
                                    <span x-text="isTestingSend ? 'Mengirim...' : 'Kirim Pesan WhatsApp'">Kirim Pesan WhatsApp</span>
                                </button>
                            </div>
                        </div>

                    </div>

                </div>

                <hr class="border-gray-100 dark:border-slate-800">

                <!-- Section: Aturan Pengingat Tagihan & Proteksi Anti-Banned -->
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 dark:bg-purple-600/20 dark:text-purple-400 flex items-center justify-center text-xl shrink-0">
                            <i class="ph ph-shield-check"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-900 dark:text-white">Jadwal & Proteksi Pengiriman Tagihan (Anti-Banned)</h4>
                            <p class="text-xs text-gray-500 dark:text-slate-400">Atur tanggal broadcast bulanan dan jeda aman (jitter) agar nomor WhatsApp tidak dianggap bot spam oleh Meta.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <!-- Tanggal Pengingat -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Tanggal Broadcast Rutin Bulanan</label>
                            <div class="relative">
                                <i class="ph ph-calendar-blank absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                                <input type="number" min="1" max="28" name="wa_reminder_day" value="<?= esc($settings['wa_reminder_day'] ?? '10') ?>" required
                                    class="w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-sm font-semibold text-gray-900 dark:text-white focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all"
                                    placeholder="10">
                            </div>
                            <p class="text-[11px] text-gray-500 dark:text-slate-400 mt-1">Setiap tanggal ini, sistem siap mengirim tagihan bulan berjalan (misal: tanggal 10).</p>
                        </div>

                        <!-- Jeda Min -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Jeda Acak Minimal (Detik)</label>
                            <div class="relative">
                                <i class="ph ph-timer absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                                <input type="number" min="3" max="60" name="wa_delay_min" value="<?= esc($settings['wa_delay_min'] ?? '8') ?>" required
                                    class="w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-sm font-semibold text-gray-900 dark:text-white focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all"
                                    placeholder="8">
                            </div>
                            <p class="text-[11px] text-gray-500 dark:text-slate-400 mt-1">Saran aman: minimal 7 - 10 detik per pesan.</p>
                        </div>

                        <!-- Jeda Max -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Jeda Acak Maksimal (Detik)</label>
                            <div class="relative">
                                <i class="ph ph-hourglass-high absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                                <input type="number" min="5" max="120" name="wa_delay_max" value="<?= esc($settings['wa_delay_max'] ?? '15') ?>" required
                                    class="w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-sm font-semibold text-gray-900 dark:text-white focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all"
                                    placeholder="15">
                            </div>
                            <p class="text-[11px] text-gray-500 dark:text-slate-400 mt-1">Memberikan jeda fluktuatif seperti pengetikan manusia asli.</p>
                        </div>
                    </div>

                    <!-- Template Pesan -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300">Format Template Pesan Pengingat Tagihan</label>
                            <span class="text-[11px] text-gray-400 dark:text-slate-500">Mendukung format WhatsApp (*tebal*, _miring_)</span>
                        </div>

                        <textarea name="wa_reminder_template" rows="7"
                            class="w-full p-4 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-xs font-mono text-gray-900 dark:text-white focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all leading-relaxed"><?= esc($settings['wa_reminder_template'] ?? '') ?></textarea>
                        
                        <!-- Tag Helper Chips -->
                        <div class="flex flex-wrap items-center gap-1.5 pt-1">
                            <span class="text-[11px] text-gray-400 dark:text-slate-500 mr-1">Tag Tersedia:</span>
                            <span class="px-2 py-0.5 rounded-md bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 font-mono text-[11px]">{salam}</span>
                            <span class="px-2 py-0.5 rounded-md bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 font-mono text-[11px]">{nama_siswa}</span>
                            <span class="px-2 py-0.5 rounded-md bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 font-mono text-[11px]">{kelas}</span>
                            <span class="px-2 py-0.5 rounded-md bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 font-mono text-[11px]">{nama_sekolah}</span>
                            <span class="px-2 py-0.5 rounded-md bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 font-mono text-[11px]">{periode}</span>
                            <span class="px-2 py-0.5 rounded-md bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 font-mono text-[11px]">{rincian_tagihan}</span>
                            <span class="px-2 py-0.5 rounded-md bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 font-mono text-[11px]">{total_tagihan}</span>
                            <span class="px-2 py-0.5 rounded-md bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 font-mono text-[11px]">{info_bank}</span>
                            <span class="px-2 py-0.5 rounded-md bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 font-mono text-[11px]">{kode_unik}</span>
                        </div>
                    </div>

                    <!-- Notice Card Anti-Banned Tips -->
                    <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/40 text-xs text-amber-800 dark:text-amber-300 flex items-start gap-3">
                        <i class="ph ph-warning-diamond text-lg shrink-0 mt-0.5 text-amber-600 dark:text-amber-400"></i>
                        <div class="space-y-1">
                            <p class="font-bold">Tips Pencegahan Pemblokiran WhatsApp Unofficial:</p>
                            <p>1. <strong>Simpan Kontak:</strong> Imbau seluruh wali murid untuk menyimpan nomor WhatsApp resmi sekolah di buku telepon mereka.</p>
                            <p>2. <strong>Pra-Validasi:</strong> Sistem akan otomatis memvalidasi apakah nomor terdaftar di WhatsApp sebelum pengiriman pesan dimulai.</p>
                            <p>3. <strong>Jam Kerja:</strong> Selalu kirim broadcast di jam kerja resmi (08.30 – 15.30 WIB) untuk menghindari laporan spam.</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ========================================== -->
            <!-- TAB 5: PENGATURAN SISTEM                   -->
            <!-- ========================================== -->
            <div x-show="activeTab === 'sistem'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="border-b border-gray-100 dark:border-slate-800 pb-4">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Preferensi Sistem & Regional</h3>
                    <p class="text-xs text-gray-500 dark:text-slate-400">Pengaturan zona waktu server dan operasional aplikasi.</p>
                </div>

                <div class="max-w-xl space-y-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Zona Waktu (Timezone)</label>
                        <select name="timezone" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-sm font-medium text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all">
                            <option value="Asia/Jakarta" <?= ($settings['timezone'] ?? '') === 'Asia/Jakarta' ? 'selected' : '' ?>>WIB - Asia/Jakarta (UTC+7)</option>
                            <option value="Asia/Makassar" <?= ($settings['timezone'] ?? '') === 'Asia/Makassar' ? 'selected' : '' ?>>WITA - Asia/Makassar (UTC+8)</option>
                            <option value="Asia/Jayapura" <?= ($settings['timezone'] ?? '') === 'Asia/Jayapura' ? 'selected' : '' ?>>WIT - Asia/Jayapura (UTC+9)</option>
                        </select>
                    </div>

                    <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-600/10 border border-blue-200 dark:border-blue-500/20 text-xs text-blue-800 dark:text-blue-300 flex items-start gap-3">
                        <i class="ph ph-shield-check text-xl shrink-0 mt-0.5"></i>
                        <div>
                            <p class="font-semibold mb-0.5">Pengaturan Otomatis Terhubung</p>
                            <p class="text-blue-600/80 dark:text-blue-300/80">Nilai konfigurasi ini dapat langsung diakses di seluruh template dan kode menggunakan helper global <code class="font-mono bg-blue-100 dark:bg-blue-900/50 px-1 py-0.5 rounded">app_setting('key')</code>.</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Footer Action Button -->
            <div class="mt-8 pt-6 border-t border-gray-100 dark:border-slate-800 flex items-center justify-between">
                <span class="text-xs text-gray-400 dark:text-slate-500">Perubahan akan langsung berpengaruh ke seluruh tampilan sistem.</span>
                <button type="submit" :disabled="isSubmitting" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-lg shadow-blue-500/25 active:scale-95">
                    <i class="ph ph-spinner animate-spin" x-show="isSubmitting" style="display: none;"></i>
                    <i class="ph ph-floppy-disk text-lg" x-show="!isSubmitting"></i>
                    <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Konfigurasi'">Simpan Konfigurasi</span>
                </button>
            </div>

        </form>

    </div>

</div>
<?= $this->endSection() ?>
