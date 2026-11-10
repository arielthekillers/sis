<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><a href="<?= base_url('wali-kelas/dashboard') ?>" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Portal Pendidik</a></li>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Jurnal Perkembangan</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Kelola Jurnal Perkembangan Murid
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Dokumentasikan catatan observasi, aspek perkembangan, dan foto kegiatan ananda yang dapat dipantau langsung oleh wali murid.
<?= $this->endSection() ?>

<?= $this->section('page_actions') ?>
<button 
    type="button" 
    @click="$dispatch('open-add-jurnal')" 
    class="bg-blue-600 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-blue-700 transition-all shadow-md shadow-blue-500/25 flex items-center gap-1.5"
>
    <i class="ph ph-plus-circle text-base"></i>
    <span>Catat Jurnal Baru</span>
</button>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6" x-data="{
    searchQuery: '',
    modalOpen: false,
    formData: {
        id: '',
        siswa_id: '',
        kelas_id: '<?= !empty($kelasBinaan[0]['id']) ? $kelasBinaan[0]['id'] : '' ?>',
        tanggal: '<?= date('Y-m-d') ?>',
        kategori: 'Afektif (Sosial Emosional)',
        judul: '',
        catatan: '',
        status: 'published'
    },
    openModal(data = null) {
        if (data) {
            this.formData = { ...data };
        } else {
            this.formData = {
                id: '',
                siswa_id: '<?= !empty($muridList[0]['id']) ? $muridList[0]['id'] : '' ?>',
                kelas_id: '<?= !empty($kelasBinaan[0]['id']) ? $kelasBinaan[0]['id'] : '' ?>',
                tanggal: '<?= date('Y-m-d') ?>',
                kategori: 'Afektif (Sosial Emosional)',
                judul: '',
                catatan: '',
                status: 'published'
            };
        }
        this.modalOpen = true;
    }
}" @open-add-jurnal.window="openModal()">

    <!-- Filter & Toolbar -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-4 sm:p-5 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
        
        <!-- Search Input -->
        <div class="w-full md:w-80 relative">
            <i class="ph ph-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
            <input 
                type="text" 
                x-model="searchQuery" 
                placeholder="Cari judul catatan, topik, atau nama murid..." 
                class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white placeholder-gray-400 focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-medium"
            >
        </div>

        <div class="flex items-center gap-3 flex-wrap justify-between md:justify-end text-xs">
            <!-- Filter Rombel -->
            <?php if(count($kelasBinaan) > 1): ?>
                <div class="flex items-center gap-1.5">
                    <span class="text-gray-400 font-medium">Kelas:</span>
                    <select 
                        onchange="window.location.href='<?= base_url('wali-kelas/perkembangan') ?>?rombel_id=' + this.value + '&kategori=<?= urlencode($selectedKategori ?? '') ?>'"
                        class="px-3 py-1.5 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-gray-700 dark:text-slate-200 focus:outline-none focus:border-blue-500"
                    >
                        <option value="">Semua Kelas</option>
                        <?php foreach($kelasBinaan as $kb): ?>
                            <option value="<?= $kb['id'] ?>" <?= $selectedRombelId == $kb['id'] ? 'selected' : '' ?>>
                                <?= esc($kb['nama_rombel']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <!-- Filter Kategori -->
            <div class="flex items-center gap-1.5">
                <span class="text-gray-400 font-medium">Aspek:</span>
                <select 
                    onchange="window.location.href='<?= base_url('wali-kelas/perkembangan') ?>?rombel_id=<?= $selectedRombelId ?? '' ?>&kategori=' + encodeURIComponent(this.value)"
                    class="px-3 py-1.5 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-gray-700 dark:text-slate-200 focus:outline-none focus:border-blue-500"
                >
                    <option value="">Semua Aspek</option>
                    <option value="Afektif (Sosial Emosional)" <?= $selectedKategori === 'Afektif (Sosial Emosional)' ? 'selected' : '' ?>>Afektif (Sosial Emosional)</option>
                    <option value="Kognitif & Logika" <?= $selectedKategori === 'Kognitif & Logika' ? 'selected' : '' ?>>Kognitif & Logika</option>
                    <option value="Motorik Kasar & Halus" <?= $selectedKategori === 'Motorik Kasar & Halus' ? 'selected' : '' ?>>Motorik Kasar & Halus</option>
                    <option value="Agama & Moralitas" <?= $selectedKategori === 'Agama & Moralitas' ? 'selected' : '' ?>>Agama & Moralitas</option>
                    <option value="Bahasa & Komunikasi" <?= $selectedKategori === 'Bahasa & Komunikasi' ? 'selected' : '' ?>>Bahasa & Komunikasi</option>
                    <option value="Seni & Kreativitas" <?= $selectedKategori === 'Seni & Kreativitas' ? 'selected' : '' ?>>Seni & Kreativitas</option>
                    <option value="Kemandirian & Kedisiplinan" <?= $selectedKategori === 'Kemandirian & Kedisiplinan' ? 'selected' : '' ?>>Kemandirian & Kedisiplinan</option>
                </select>
            </div>

            <!-- Total Catatan Badge -->
            <span class="font-bold px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                <?= count($perkembanganList) ?> Catatan
            </span>
        </div>
    </div>

    <!-- Feed / Daftar Jurnal Perkembangan -->
    <?php if(empty($perkembanganList)): ?>
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-12 text-center border border-gray-100 dark:border-slate-800 shadow-sm">
            <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-500 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-3">
                <i class="ph ph-notebook"></i>
            </div>
            <h4 class="text-base font-bold text-gray-900 dark:text-white mb-1">Belum Ada Catatan Perkembangan</h4>
            <p class="text-xs text-gray-400 max-w-sm mx-auto mb-4">
                Tidak ada catatan perkembangan yang sesuai dengan filter. Klik tombol di bawah untuk mencatat jurnal pertama.
            </p>
            <button 
                type="button" 
                @click="openModal()" 
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-500/25 transition-all"
            >
                <i class="ph ph-plus"></i>
                <span>Tulis Jurnal Perkembangan</span>
            </button>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <?php foreach($perkembanganList as $pl): ?>
                <div 
                    x-show="!searchQuery || '<?= strtolower(esc($pl['nama_lengkap'])) ?>'.includes(searchQuery.toLowerCase()) || '<?= strtolower(esc($pl['judul'])) ?>'.includes(searchQuery.toLowerCase()) || '<?= strtolower(esc($pl['kategori'])) ?>'.includes(searchQuery.toLowerCase())"
                    class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-all flex flex-col justify-between"
                >
                    <div>
                        <!-- Header Jurnal: Foto Murid, Nama, Tanggal, Status -->
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="flex items-center gap-3">
                                <?php if(!empty($pl['foto_siswa'])): ?>
                                    <img src="<?= base_url('uploads/siswa/' . $pl['foto_siswa']) ?>" alt="<?= esc($pl['nama_lengkap']) ?>" class="w-10 h-10 rounded-xl object-cover border border-gray-200 dark:border-slate-700 shrink-0">
                                <?php else: ?>
                                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 font-bold text-xs flex items-center justify-center shrink-0">
                                        <?= strtoupper(substr($pl['nama_panggilan'] ?: $pl['nama_lengkap'], 0, 2)) ?>
                                    </div>
                                <?php endif; ?>

                                <div>
                                    <a href="<?= base_url('wali-kelas/murid/profil/' . $pl['siswa_id']) ?>" class="font-bold text-xs text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        <?= esc($pl['nama_lengkap']) ?>
                                    </a>
                                    <span class="block text-[11px] text-gray-400">Kelas <?= esc($pl['nama_rombel'] ?? '-') ?></span>
                                </div>
                            </div>

                            <div class="text-right shrink-0">
                                <span class="text-[10px] text-gray-400 block mb-1"><?= tanggal_indo($pl['tanggal']) ?></span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $pl['status'] === 'published' ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-slate-400' ?>">
                                    <?= $pl['status'] === 'published' ? '✓ Publik' : 'Draft' ?>
                                </span>
                            </div>
                        </div>

                        <!-- Aspek Kategori Badge -->
                        <div class="mb-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                                <?= esc($pl['kategori']) ?>
                            </span>
                        </div>

                        <!-- Judul & Catatan Naratif -->
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-1.5">
                            <?= esc($pl['judul']) ?>
                        </h4>
                        <div class="text-xs text-gray-600 dark:text-slate-300 leading-relaxed whitespace-pre-line bg-gray-50 dark:bg-slate-700/40 p-3 rounded-xl mb-3">
                            <?= esc($pl['catatan']) ?>
                        </div>

                        <!-- Dokumentasi Foto Jika Ada -->
                        <?php if(!empty($pl['foto'])): ?>
                            <div class="mb-3">
                                <a href="<?= base_url('uploads/perkembangan/' . $pl['foto']) ?>" target="_blank" class="block h-36 rounded-xl overflow-hidden border border-gray-100 dark:border-slate-700 group relative">
                                    <img src="<?= base_url('uploads/perkembangan/' . $pl['foto']) ?>" alt="Dokumentasi" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs font-bold transition-opacity">
                                        <i class="ph ph-magnifying-glass-plus mr-1"></i> Lihat Foto Penuh
                                    </div>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Footer Kartu: Aksi Edit & Hapus -->
                    <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-slate-700/60 text-xs">
                        <a href="<?= base_url('wali-kelas/murid/profil/' . $pl['siswa_id']) ?>" class="text-[11px] font-bold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                            <span>Profil Murid</span>
                            <i class="ph ph-arrow-right text-[10px]"></i>
                        </a>

                        <div class="flex items-center gap-2">
                            <button 
                                type="button" 
                                @click="openModal(<?= htmlspecialchars(json_encode($pl), ENT_QUOTES, 'UTF-8') ?>)" 
                                class="p-1.5 rounded-lg text-gray-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-slate-700 transition-colors"
                                title="Edit Catatan"
                            >
                                <i class="ph ph-pencil-simple text-base"></i>
                            </button>
                            <button 
                                type="button" 
                                onclick="confirmAction('Hapus Catatan?', 'Catatan perkembangan ini akan dihapus permanen.', 'Ya, Hapus', () => { window.location.href='<?= base_url('wali-kelas/delete-perkembangan/' . $pl['id']) ?>' })"
                                class="p-1.5 rounded-lg text-gray-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-slate-700 transition-colors"
                                title="Hapus Catatan"
                            >
                                <i class="ph ph-trash text-base"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Modal Catat / Edit Jurnal Perkembangan (Alpine.js) -->
    <div 
        x-show="modalOpen" 
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
            class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-lg w-full p-6 text-left border border-gray-100 dark:border-slate-700 relative max-h-[90vh] overflow-y-auto"
            @click.away="modalOpen = false"
        >
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100 dark:border-slate-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl">
                        <i class="ph ph-notebook"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white" x-text="formData.id ? 'Edit Jurnal Perkembangan' : 'Catat Jurnal Perkembangan Baru'"></h3>
                        <p class="text-xs text-gray-400">Pilih murid dan catat observasi ananda.</p>
                    </div>
                </div>
                <button type="button" @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200">
                    <i class="ph ph-x text-lg"></i>
                </button>
            </div>

            <form action="<?= base_url('wali-kelas/save-perkembangan') ?>" method="post" enctype="multipart/form-data" class="space-y-4">
                <?= csrf_field() ?>
                <input type="hidden" name="id" :value="formData.id">

                <!-- Pilih Murid -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Pilih Murid *</label>
                    <select name="siswa_id" x-model="formData.siswa_id" required class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:bg-white focus:border-blue-500">
                        <option value="">-- Pilih Murid Binaan --</option>
                        <?php foreach($muridList as $ml): ?>
                            <option value="<?= $ml['id'] ?>">
                                <?= esc($ml['nama_lengkap']) ?> (Kelas <?= esc($ml['nama_rombel']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Pilih Kelas -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Rombongan Belajar *</label>
                    <select name="kelas_id" x-model="formData.kelas_id" required class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:bg-white focus:border-blue-500">
                        <?php foreach($kelasBinaan as $kb): ?>
                            <option value="<?= $kb['id'] ?>"><?= esc($kb['nama_rombel']) ?> (<?= esc($kb['tingkat']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Tanggal -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Tanggal Observasi *</label>
                        <input type="date" name="tanggal" x-model="formData.tanggal" required class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:bg-white focus:border-blue-500">
                    </div>

                    <!-- Kategori -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Aspek Perkembangan *</label>
                        <select name="kategori" x-model="formData.kategori" required class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:bg-white focus:border-blue-500">
                            <option value="Afektif (Sosial Emosional)">Afektif (Sosial Emosional)</option>
                            <option value="Kognitif & Logika">Kognitif & Logika</option>
                            <option value="Motorik Kasar & Halus">Motorik Kasar & Halus</option>
                            <option value="Agama & Moralitas">Agama & Moralitas</option>
                            <option value="Bahasa & Komunikasi">Bahasa & Komunikasi</option>
                            <option value="Seni & Kreativitas">Seni & Kreativitas</option>
                            <option value="Kemandirian & Kedisiplinan">Kemandirian & Kedisiplinan</option>
                        </select>
                    </div>
                </div>

                <!-- Judul -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Judul Observasi / Topik *</label>
                    <input type="text" name="judul" x-model="formData.judul" placeholder="Contoh: Sangat antusias saat menyusun balok..." required class="w-full px-3 py-2.5 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:bg-white focus:border-blue-500">
                </div>

                <!-- Isi Catatan -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Deskripsi Naratif *</label>
                    <textarea name="catatan" x-model="formData.catatan" rows="4" placeholder="Jelaskan secara naratif perilaku, pencapaian, atau hal-hal yang perlu distimulasi lebih lanjut..." required class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 leading-relaxed"></textarea>
                </div>

                <!-- Unggah Foto Kegiatan -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Unggah Foto Kegiatan (Opsional)</label>
                    <input type="file" name="foto" accept="image/*" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-slate-700 dark:file:text-slate-300">
                </div>

                <!-- Status Publikasi -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Status Publikasi</label>
                    <select name="status" x-model="formData.status" class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:bg-white focus:border-blue-500">
                        <option value="published">Publikasi (Dapat Dilihat Wali Murid)</option>
                        <option value="draft">Simpan sebagai Draft (Hanya Pendidik)</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100 dark:border-slate-700">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 rounded-xl text-xs font-medium text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-500/25 transition-all flex items-center gap-1.5">
                        <i class="ph ph-check"></i>
                        <span>Simpan Catatan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
<?= $this->endSection() ?>

