<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><a href="<?= base_url('wali-kelas/dashboard') ?>" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Portal Wali Kelas</a></li>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><a href="<?= base_url('wali-kelas/kelas/' . $rombel['id']) ?>" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors"><?= esc($rombel['nama_kelas']) ?></a></li>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Perkembangan: <?= esc($siswa['nama_lengkap']) ?></span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Jurnal Perkembangan: <?= esc($siswa['nama_lengkap']) ?>
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Kelas: <?= esc($rombel['nama_kelas']) ?> (<?= esc($rombel['tingkat']) ?>) • Catat dan pantau tumbuh kembang ananda secara berkala.
<?= $this->endSection() ?>

<?= $this->section('page_actions') ?>
<div class="flex items-center gap-2">
    <a href="<?= base_url('wali-kelas/kelas/' . $rombel['id']) ?>" class="bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 px-3.5 py-2 rounded-xl text-sm font-medium hover:bg-gray-200 dark:hover:bg-slate-700 transition-colors flex items-center gap-2">
        <i class="ph ph-arrow-left"></i> Kembali
    </a>
    <button @click="$dispatch('open-perkembangan-modal', { id: null, kategori: 'Karakter & Kemandirian', status: 'published', tanggal: '<?= date('Y-m-d') ?>', judul: '', catatan: '' })" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition-all shadow-md shadow-blue-500/25 flex items-center gap-2">
        <i class="ph ph-plus-circle text-base"></i>
        <span>Tambah Catatan</span>
    </button>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div x-data="{
    modalOpen: false,
    editData: {
        id: null,
        kategori: 'Karakter & Kemandirian',
        tanggal: '<?= date('Y-m-d') ?>',
        judul: '',
        catatan: '',
        status: 'published'
    },
    isSubmitting: false
}"
@open-perkembangan-modal.window="editData = $event.detail; modalOpen = true"
class="space-y-6">

    <!-- Card Profil Ringkas Murid -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 flex flex-col sm:flex-row items-center sm:items-start gap-6">
        <div class="relative flex-shrink-0">
            <?php if(!empty($siswa['foto'])): ?>
                <img src="<?= base_url('uploads/profiles/' . $siswa['foto']) ?>" alt="<?= esc($siswa['nama_lengkap']) ?>" class="w-24 h-24 object-cover rounded-2xl border-4 border-gray-50 dark:border-slate-700 shadow-sm">
            <?php else: ?>
                <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-blue-100 to-indigo-100 dark:from-slate-700 dark:to-slate-800 flex items-center justify-center text-blue-600 dark:text-blue-400 font-extrabold text-3xl border-4 border-gray-50 dark:border-slate-700 shadow-sm">
                    <?= strtoupper(substr($siswa['nama_lengkap'], 0, 1)) ?>
                </div>
            <?php endif; ?>
            <span class="absolute -bottom-1 -right-1 px-2 py-0.5 rounded-full text-[10px] font-bold <?= $siswa['jenis_kelamin'] === 'L' ? 'bg-sky-500 text-white' : 'bg-pink-500 text-white' ?>">
                <?= $siswa['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?>
            </span>
        </div>

        <div class="flex-1 text-center sm:text-left">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        <?= esc($siswa['nama_lengkap']) ?>
                        <?php if(!empty($siswa['nama_panggilan'])): ?>
                            <span class="text-sm font-medium text-gray-500 dark:text-slate-400">(<?= esc($siswa['nama_panggilan']) ?>)</span>
                        <?php endif; ?>
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-slate-400">
                        NIS: <?= esc($siswa['nis'] ?: '-') ?> • NISN: <?= esc($siswa['nisn'] ?: '-') ?>
                    </p>
                </div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 self-center sm:self-auto">
                    <i class="ph ph-check-circle"></i> Status: <?= esc($siswa['status_siswa'] ?? 'Aktif') ?>
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-3 border-t border-gray-100 dark:border-slate-700/60 text-xs">
                <div>
                    <span class="text-gray-400 block mb-0.5">Orang Tua / Wali:</span>
                    <span class="font-medium text-gray-800 dark:text-slate-200">
                        <?= esc($siswa['nama_ayah'] ?: ($siswa['nama_ibu'] ?: '-')) ?>
                    </span>
                </div>
                <div>
                    <span class="text-gray-400 block mb-0.5">Kontak WhatsApp:</span>
                    <?php if(!empty($siswa['no_hp_ortu'])): ?>
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $siswa['no_hp_ortu']) ?>" target="_blank" class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-semibold hover:underline">
                            <i class="ph ph-whatsapp-logo text-sm"></i> <?= esc($siswa['no_hp_ortu']) ?>
                        </a>
                    <?php else: ?>
                        <span class="text-gray-500">-</span>
                    <?php endif; ?>
                </div>
                <div>
                    <span class="text-gray-400 block mb-0.5">Total Jurnal:</span>
                    <span class="font-bold text-blue-600 dark:text-blue-400"><?= count($catatanList) ?> Catatan Terekam</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Catatan Jurnal Perkembangan -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="ph ph-notebook text-blue-600 dark:text-blue-400 text-xl"></i>
                Timeline Catatan Perkembangan
            </h3>
            <span class="text-xs text-gray-500 dark:text-slate-400 font-medium">
                <?= count($catatanList) ?> Catatan
            </span>
        </div>

        <?php if(empty($catatanList)): ?>
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-12 text-center border border-gray-100 dark:border-slate-800">
                <div class="w-16 h-16 bg-blue-50 dark:bg-slate-700 rounded-2xl flex items-center justify-center text-blue-600 dark:text-blue-400 mx-auto mb-3 text-3xl">
                    <i class="ph ph-pencil-line"></i>
                </div>
                <h4 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Belum Ada Catatan Perkembangan</h4>
                <p class="text-sm text-gray-500 dark:text-slate-400 max-w-md mx-auto mb-5">
                    Mulai dokumentasikan milestone, capaian karakter, motorik, atau catatan penting ananda hari ini.
                </p>
                <button @click="$dispatch('open-perkembangan-modal', { id: null, kategori: 'Karakter & Kemandirian', status: 'published', tanggal: '<?= date('Y-m-d') ?>', judul: '', catatan: '' })" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-xl text-sm transition-all shadow-md shadow-blue-500/20">
                    <i class="ph ph-plus"></i> Tambah Catatan Pertama
                </button>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach($catatanList as $c): ?>
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-slate-800 transition-all hover:border-gray-200 dark:hover:border-slate-700">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 mb-3">
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <?php
                                    $kategoriColors = [
                                        'Akademik'                => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 border-blue-200 dark:border-blue-800',
                                        'Karakter & Kemandirian'  => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                                        'Motorik & Fisik'         => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border-amber-200 dark:border-amber-800',
                                        'Sosial Emosional'        => 'bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 border-purple-200 dark:border-purple-800',
                                        'Kesehatan'               => 'bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400 border-rose-200 dark:border-rose-800',
                                        'Catatan Khusus'          => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-600',
                                    ];
                                    $badgeClass = $kategoriColors[$c['kategori']] ?? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 border-blue-200';
                                ?>
                                <span class="px-2.5 py-1 rounded-lg text-xs font-semibold border <?= $badgeClass ?>">
                                    <?= esc($c['kategori']) ?>
                                </span>
                                <span class="text-xs text-gray-500 dark:text-slate-400 flex items-center gap-1">
                                    <i class="ph ph-calendar text-sm"></i> <?= tanggal_indo($c['tanggal'], true) ?>
                                </span>
                                <?php if($c['status'] === 'published'): ?>
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-600 dark:text-emerald-400">
                                        <i class="ph ph-eye"></i> Tampil ke Orang Tua
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-amber-600 dark:text-amber-400">
                                        <i class="ph ph-eye-slash"></i> Draft (Hanya Guru)
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Tombol Aksi -->
                            <div class="flex items-center gap-2 self-end sm:self-auto">
                                <button 
                                    @click="$dispatch('open-perkembangan-modal', {
                                        id: <?= $c['id'] ?>,
                                        kategori: '<?= esc($c['kategori']) ?>',
                                        tanggal: '<?= esc($c['tanggal']) ?>',
                                        judul: '<?= addslashes(esc($c['judul'])) ?>',
                                        catatan: `<?= addslashes(esc($c['catatan'])) ?>`,
                                        status: '<?= esc($c['status']) ?>'
                                    })" 
                                    class="p-1.5 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-slate-700 transition-colors"
                                    title="Edit Catatan"
                                >
                                    <i class="ph ph-pencil text-base"></i>
                                </button>
                                <button 
                                    onclick="confirmAction('Hapus Catatan?', 'Catatan perkembangan ini akan dihapus permanen!', 'Ya, Hapus', () => { window.location.href='<?= base_url('wali-kelas/delete-perkembangan/' . $c['id']) ?>' })"
                                    class="p-1.5 rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-slate-700 transition-colors"
                                    title="Hapus Catatan"
                                >
                                    <i class="ph ph-trash text-base"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Judul & Isi Catatan -->
                        <h4 class="text-base font-bold text-gray-900 dark:text-white mb-2">
                            <?= esc($c['judul']) ?>
                        </h4>
                        <div class="text-sm text-gray-600 dark:text-slate-300 leading-relaxed whitespace-pre-line mb-4 font-normal">
                            <?= esc($c['catatan']) ?>
                        </div>

                        <!-- Foto Dokumentasi (Jika Ada) -->
                        <?php if(!empty($c['foto'])): ?>
                            <div class="mb-4">
                                <a href="<?= base_url('uploads/perkembangan/' . $c['foto']) ?>" target="_blank" class="inline-block group relative rounded-xl overflow-hidden border border-gray-100 dark:border-slate-700 max-w-sm">
                                    <img src="<?= base_url('uploads/perkembangan/' . $c['foto']) ?>" alt="Dokumentasi <?= esc($c['judul']) ?>" class="max-h-56 w-auto object-cover rounded-xl group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-semibold gap-1.5">
                                        <i class="ph ph-magnifying-glass-plus text-base"></i> Lihat Ukuran Penuh
                                    </div>
                                </a>
                            </div>
                        <?php endif; ?>

                        <!-- Info Guru Pencatat -->
                        <div class="pt-3 border-t border-gray-100 dark:border-slate-700/60 flex items-center justify-between text-xs text-gray-400 dark:text-slate-500">
                            <span class="flex items-center gap-1.5">
                                <i class="ph ph-chalkboard-teacher text-blue-500"></i>
                                Dicatat oleh: <strong class="text-gray-700 dark:text-slate-300"><?= esc($c['sapaan_guru'] ?? '') ?> <?= esc($c['nama_guru'] ?? 'Pendidik') ?></strong>
                            </span>
                            <span><?= date('H:i', strtotime($c['created_at'])) ?> WIB</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal Alpine.js: Input & Edit Catatan Perkembangan (Standar Premium) -->
    <div 
        x-show="modalOpen" 
        x-cloak 
        class="fixed inset-0 z-50 overflow-y-auto"
        role="dialog" 
        aria-modal="true"
    >
        <!-- Backdrop -->
        <div 
            x-show="modalOpen" 
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="modalOpen = false" 
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
        ></div>

        <!-- Dialog Box -->
        <div class="flex min-h-screen items-center justify-center p-4">
            <div 
                x-show="modalOpen" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative w-full max-w-xl bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-gray-100 dark:border-slate-700 p-6 md:p-8 overflow-hidden"
            >
                <!-- Form -->
                <form 
                    action="<?= base_url('wali-kelas/save-perkembangan') ?>" 
                    method="post" 
                    enctype="multipart/form-data" 
                    @submit="isSubmitting = true"
                >
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" :value="editData.id">
                    <input type="hidden" name="siswa_id" value="<?= esc($siswa['id']) ?>">
                    <input type="hidden" name="kelas_id" value="<?= esc($rombel['id']) ?>">

                    <!-- Seamless Header -->
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">
                                Jurnal Perkembangan Siswa
                            </span>
                            <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white mt-1" x-text="editData.id ? 'Ubah Catatan Jurnal' : 'Tambah Catatan Baru'"></h3>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">
                                Ananda: <strong><?= esc($siswa['nama_lengkap']) ?></strong> (<?= esc($rombel['nama_kelas']) ?>)
                            </p>
                        </div>
                        <button 
                            type="button" 
                            @click="modalOpen = false" 
                            class="p-2 rounded-xl text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors"
                        >
                            <i class="ph ph-x text-lg"></i>
                        </button>
                    </div>

                    <!-- Input Fields -->
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Tanggal -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Tanggal Pengamatan *</label>
                                <div class="relative">
                                    <i class="ph ph-calendar absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                                    <input 
                                        type="date" 
                                        name="tanggal" 
                                        x-model="editData.tanggal" 
                                        required 
                                        class="w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border border-transparent rounded-xl text-sm text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all"
                                    >
                                </div>
                            </div>

                            <!-- Kategori Aspek -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Aspek / Kategori *</label>
                                <div class="relative">
                                    <i class="ph ph-tag absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                                    <select 
                                        name="kategori" 
                                        x-model="editData.kategori" 
                                        required 
                                        class="w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border border-transparent rounded-xl text-sm text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all"
                                    >
                                        <option value="Karakter & Kemandirian">Karakter & Kemandirian</option>
                                        <option value="Akademik">Akademik & Kognitif</option>
                                        <option value="Motorik & Fisik">Motorik & Fisik</option>
                                        <option value="Sosial Emosional">Sosial Emosional</option>
                                        <option value="Kesehatan">Kesehatan & Kebiasaan</option>
                                        <option value="Catatan Khusus">Catatan Khusus</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Judul Catatan -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Judul Catatan / Indikator *</label>
                            <div class="relative">
                                <i class="ph ph-text-t absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                                <input 
                                    type="text" 
                                    name="judul" 
                                    x-model="editData.judul" 
                                    placeholder="Contoh: Mulai mandiri merapikan mainan, mengenal angka 1-10..." 
                                    required 
                                    class="w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border border-transparent rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all"
                                >
                            </div>
                        </div>

                        <!-- Catatan Deskripsi Detail -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Deskripsi Perkembangan Ananda *</label>
                            <textarea 
                                name="catatan" 
                                x-model="editData.catatan" 
                                rows="4" 
                                required 
                                placeholder="Jelaskan observasi perkembangan ananda secara detail dan bernada positif..." 
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-slate-700/50 border border-transparent rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all leading-relaxed"
                            ></textarea>
                        </div>

                        <!-- Upload Foto Dokumentasi (Opsional) -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Foto Kegiatan (Opsional)</label>
                            <div class="relative">
                                <i class="ph ph-camera absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                                <input 
                                    type="file" 
                                    name="foto" 
                                    accept="image/*" 
                                    class="w-full pl-11 pr-4 py-2 bg-gray-50 dark:bg-slate-700/50 border border-transparent rounded-xl text-xs text-gray-600 dark:text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-100 file:text-blue-700 dark:file:bg-blue-900/40 dark:file:text-blue-300 hover:file:bg-blue-200 transition-all"
                                >
                            </div>
                            <span class="text-[11px] text-gray-400 dark:text-slate-500 mt-1 block">Mendukung format JPG, PNG. Otomatis dikompresi ke WebP.</span>
                        </div>

                        <!-- Status Publikasi -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Status Publikasi</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="flex items-center gap-2 p-3 rounded-xl border border-gray-200 dark:border-slate-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors" :class="editData.status === 'published' ? 'bg-blue-50/70 border-blue-500 dark:bg-blue-900/20 dark:border-blue-500' : ''">
                                    <input type="radio" name="status" value="published" x-model="editData.status" class="text-blue-600 focus:ring-blue-500">
                                    <div>
                                        <p class="text-xs font-bold text-gray-900 dark:text-white">Publish</p>
                                        <p class="text-[10px] text-gray-500 dark:text-slate-400">Dapat dilihat orang tua</p>
                                    </div>
                                </label>
                                <label class="flex items-center gap-2 p-3 rounded-xl border border-gray-200 dark:border-slate-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors" :class="editData.status === 'draft' ? 'bg-amber-50/70 border-amber-500 dark:bg-amber-900/20 dark:border-amber-500' : ''">
                                    <input type="radio" name="status" value="draft" x-model="editData.status" class="text-amber-600 focus:ring-amber-500">
                                    <div>
                                        <p class="text-xs font-bold text-gray-900 dark:text-white">Draft</p>
                                        <p class="text-[10px] text-gray-500 dark:text-slate-400">Hanya internal guru</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Seamless & Glowing Button -->
                    <div class="mt-8 flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-slate-700/60">
                        <button 
                            type="button" 
                            @click="modalOpen = false" 
                            class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            :disabled="isSubmitting" 
                            class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 transition-all flex items-center gap-2 disabled:opacity-50"
                        >
                            <i x-show="isSubmitting" class="ph ph-spinner animate-spin text-base"></i>
                            <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Catatan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

