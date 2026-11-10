<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><a href="<?= base_url('wali-kelas/dashboard') ?>" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Portal Pendidik</a></li>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><a href="<?= base_url('wali-kelas/murid') ?>" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Murid Binaan</a></li>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium"><?= esc($siswa['nama_lengkap']) ?></span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Profil Murid: <?= esc($siswa['nama_lengkap']) ?>
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Kelola biodata, pantau riwayat kelas, dan catat jurnal perkembangan ananda <?= esc($siswa['nama_panggilan'] ?: $siswa['nama_lengkap']) ?>.
<?= $this->endSection() ?>

<?= $this->section('page_actions') ?>
<div class="flex items-center gap-2">
    <a href="<?= base_url('wali-kelas/murid') ?>" class="bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 px-3.5 py-2 rounded-xl text-xs font-semibold hover:bg-gray-200 dark:hover:bg-slate-700 transition-colors flex items-center gap-1.5">
        <i class="ph ph-arrow-left"></i> Kembali ke Daftar Murid
    </a>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6" x-data="{
    activeTab: 'jurnal',
    jurnalModalOpen: false,
    editKontakOpen: false,
    jurnalData: {
        id: '',
        siswa_id: '<?= $siswa['id'] ?>',
        kelas_id: '<?= !empty($rombelSiswa[0]['rombel_id']) ? $rombelSiswa[0]['rombel_id'] : '' ?>',
        tanggal: '<?= date('Y-m-d') ?>',
        kategori: 'Afektif (Sosial Emosional)',
        judul: '',
        catatan: '',
        status: 'published'
    },
    openJurnalModal(item = null) {
        if (item) {
            this.jurnalData = { ...item };
        } else {
            this.jurnalData = {
                id: '',
                siswa_id: '<?= $siswa['id'] ?>',
                kelas_id: '<?= !empty($rombelSiswa[0]['rombel_id']) ? $rombelSiswa[0]['rombel_id'] : '' ?>',
                tanggal: '<?= date('Y-m-d') ?>',
                kategori: 'Afektif (Sosial Emosional)',
                judul: '',
                catatan: '',
                status: 'published'
            };
        }
        this.jurnalModalOpen = true;
    }
}">

    <!-- Banner Profil Header Siswa -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <?php if(!empty($siswa['foto'])): ?>
                <img src="<?= base_url('uploads/siswa/' . $siswa['foto']) ?>" alt="<?= esc($siswa['nama_lengkap']) ?>" class="w-20 h-20 rounded-2xl object-cover border-2 border-white dark:border-slate-700 shadow-lg">
            <?php else: ?>
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br <?= $siswa['jenis_kelamin'] === 'P' ? 'from-pink-500 to-rose-600' : 'from-blue-500 to-indigo-600' ?> text-white font-black text-2xl flex items-center justify-center shadow-lg">
                    <?= strtoupper(substr($siswa['nama_panggilan'] ?: $siswa['nama_lengkap'], 0, 2)) ?>
                </div>
            <?php endif; ?>

            <div>
                <div class="flex items-center gap-2 flex-wrap mb-1.5">
                    <?php if(!empty($rombelSiswa[0])): ?>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                            <?= esc($rombelSiswa[0]['nama_rombel']) ?> (<?= esc($rombelSiswa[0]['tingkat']) ?>)
                        </span>
                    <?php endif; ?>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold <?= $siswa['jenis_kelamin'] === 'P' ? 'bg-pink-100 text-pink-700 dark:bg-pink-900/40 dark:text-pink-300' : 'bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300' ?>">
                        <?= $siswa['jenis_kelamin'] === 'P' ? 'Perempuan' : 'Laki-laki' ?>
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                        ● <?= esc($siswa['status_siswa'] ?? 'Aktif') ?>
                    </span>
                </div>

                <h3 class="text-2xl font-black text-gray-900 dark:text-white">
                    <?= esc($siswa['nama_lengkap']) ?>
                </h3>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5 flex items-center gap-3">
                    <span>Nama Panggilan: <strong class="text-gray-700 dark:text-slate-200"><?= esc($siswa['nama_panggilan'] ?: '-') ?></strong></span>
                    <span>•</span>
                    <span class="font-mono">NIS: <?= esc($siswa['nis'] ?: '-') ?></span>
                    <span>•</span>
                    <span class="font-mono">NISN: <?= esc($siswa['nisn'] ?: '-') ?></span>
                </p>
            </div>
        </div>

        <!-- Tombol Aksi Kanan -->
        <div class="flex items-center gap-2 flex-wrap self-stretch sm:self-auto justify-end">
            <button 
                type="button" 
                @click="openJurnalModal()" 
                class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-md shadow-emerald-500/25 transition-all"
            >
                <i class="ph ph-plus-circle text-base"></i>
                <span>Catat Perkembangan</span>
            </button>
            <button 
                type="button" 
                @click="editKontakOpen = true" 
                class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl text-xs font-bold bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-slate-200 transition-all"
            >
                <i class="ph ph-address-book text-base"></i>
                <span>Perbarui Kontak</span>
            </button>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-gray-200 dark:border-slate-800">
        <button 
            type="button" 
            @click="activeTab = 'jurnal'" 
            :class="activeTab === 'jurnal' ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-500 dark:text-slate-400 hover:text-gray-700'"
            class="px-4 py-3 text-xs flex items-center gap-2 transition-all"
        >
            <i class="ph ph-notebook text-base"></i>
            <span>Jurnal Perkembangan (<?= count($catatanList) ?>)</span>
        </button>

        <button 
            type="button" 
            @click="activeTab = 'biodata'" 
            :class="activeTab === 'biodata' ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-500 dark:text-slate-400 hover:text-gray-700'"
            class="px-4 py-3 text-xs flex items-center gap-2 transition-all"
        >
            <i class="ph ph-user-list text-base"></i>
            <span>Biodata & Orang Tua</span>
        </button>
    </div>

    <!-- TAB 1: TIMELINE JURNAL PERKEMBANGAN -->
    <div x-show="activeTab === 'jurnal'" class="space-y-4">
        <?php if(empty($catatanList)): ?>
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-12 text-center border border-gray-100 dark:border-slate-800 shadow-sm">
                <i class="ph ph-notebook text-4xl text-gray-300 dark:text-slate-600 mx-auto mb-2 block"></i>
                <h4 class="text-sm font-bold text-gray-800 dark:text-slate-200">Belum Ada Catatan Perkembangan</h4>
                <p class="text-xs text-gray-400 max-w-sm mx-auto mt-1 mb-4">
                    Mulai dokumentasikan perkembangan sosial, motorik, kognitif, atau ibadah ananda <?= esc($siswa['nama_panggilan'] ?: $siswa['nama_lengkap']) ?>.
                </p>
                <button 
                    type="button" 
                    @click="openJurnalModal()" 
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-500/25 transition-all"
                >
                    <i class="ph ph-plus"></i>
                    <span>Tulis Catatan Pertama</span>
                </button>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach($catatanList as $cl): ?>
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-gray-100 dark:border-slate-800 shadow-sm flex flex-col md:flex-row gap-5 items-start">
                        <?php if(!empty($cl['foto'])): ?>
                            <a href="<?= base_url('uploads/perkembangan/' . $cl['foto']) ?>" target="_blank" class="w-full md:w-44 h-36 rounded-xl overflow-hidden shrink-0 border border-gray-100 dark:border-slate-700 group relative block">
                                <img src="<?= base_url('uploads/perkembangan/' . $cl['foto']) ?>" alt="Dokumentasi" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs font-bold transition-opacity">
                                    <i class="ph ph-magnifying-glass-plus mr-1"></i> Perbesar
                                </div>
                            </a>
                        <?php endif; ?>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2 mb-2 flex-wrap">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                        <?= esc($cl['kategori']) ?>
                                    </span>
                                    <span class="text-xs text-gray-400 font-medium">
                                        <i class="ph ph-calendar"></i> <?= tanggal_indo($cl['tanggal']) ?>
                                    </span>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $cl['status'] === 'published' ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-slate-400' ?>">
                                    <?= $cl['status'] === 'published' ? '✓ Dilihat Wali Murid' : 'Draft Guru' ?>
                                </span>
                            </div>

                            <h4 class="text-base font-bold text-gray-900 dark:text-white mb-2">
                                <?= esc($cl['judul']) ?>
                            </h4>

                            <div class="text-xs text-gray-600 dark:text-slate-300 leading-relaxed space-y-2 whitespace-pre-line bg-gray-50 dark:bg-slate-700/40 p-3.5 rounded-xl">
                                <?= esc($cl['catatan']) ?>
                            </div>

                            <div class="flex items-center justify-between mt-3 text-[11px] text-gray-400">
                                <span>Pendidik: <strong class="text-gray-700 dark:text-slate-300"><?= esc($cl['sapaan'] ?? '') ?> <?= esc($cl['nama_guru'] ?? 'Pendidik') ?></strong></span>

                                <!-- Tombol Edit & Hapus (Jika catatan milik guru ini) -->
                                <?php if($guru && $cl['guru_id'] == $guru['id']): ?>
                                    <div class="flex items-center gap-2">
                                        <button 
                                            type="button" 
                                            @click="openJurnalModal(<?= htmlspecialchars(json_encode($cl), ENT_QUOTES, 'UTF-8') ?>)" 
                                            class="text-blue-600 dark:text-blue-400 font-bold hover:underline flex items-center gap-0.5"
                                        >
                                            <i class="ph ph-pencil-simple"></i> Edit
                                        </button>
                                        <button 
                                            type="button" 
                                            onclick="confirmAction('Hapus Catatan?', 'Catatan perkembangan ini akan dihapus permanen.', 'Ya, Hapus', () => { window.location.href='<?= base_url('wali-kelas/delete-perkembangan/' . $cl['id']) ?>' })"
                                            class="text-rose-500 hover:underline flex items-center gap-0.5"
                                        >
                                            <i class="ph ph-trash"></i> Hapus
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- TAB 2: BIODATA & ORANG TUA -->
    <div x-show="activeTab === 'biodata'" style="display: none;" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Biodata Pribadi Murid -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm space-y-4">
                <h4 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2 pb-3 border-b border-gray-100 dark:border-slate-700/60">
                    <i class="ph ph-identification-card text-blue-600 dark:text-blue-400 text-lg"></i>
                    Identitas Diri Murid
                </h4>
                <div class="space-y-3 text-xs">
                    <div class="flex justify-between py-1 border-b border-gray-50 dark:border-slate-700/40">
                        <span class="text-gray-400">Nama Lengkap</span>
                        <strong class="text-gray-900 dark:text-white"><?= esc($siswa['nama_lengkap']) ?></strong>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50 dark:border-slate-700/40">
                        <span class="text-gray-400">Nama Panggilan</span>
                        <strong class="text-gray-900 dark:text-white"><?= esc($siswa['nama_panggilan'] ?: '-') ?></strong>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50 dark:border-slate-700/40">
                        <span class="text-gray-400">Tempat, Tanggal Lahir</span>
                        <strong class="text-gray-900 dark:text-white"><?= esc($siswa['tempat_lahir'] ?: '-') ?>, <?= !empty($siswa['tanggal_lahir']) ? tanggal_indo($siswa['tanggal_lahir']) : '-' ?></strong>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50 dark:border-slate-700/40">
                        <span class="text-gray-400">Agama</span>
                        <strong class="text-gray-900 dark:text-white"><?= esc($siswa['agama'] ?: 'Islam') ?></strong>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50 dark:border-slate-700/40">
                        <span class="text-gray-400">Alamat Tempat Tinggal</span>
                        <span class="text-gray-900 dark:text-white text-right max-w-xs"><?= esc($siswa['alamat'] ?: 'Belum diisi') ?></span>
                    </div>
                </div>
            </div>

            <!-- Data Orang Tua & Kontak -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm space-y-4">
                <h4 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2 pb-3 border-b border-gray-100 dark:border-slate-700/60">
                    <i class="ph ph-users text-emerald-600 dark:text-emerald-400 text-lg"></i>
                    Data Orang Tua & Wali
                </h4>
                <div class="space-y-3 text-xs">
                    <div class="flex justify-between py-1 border-b border-gray-50 dark:border-slate-700/40">
                        <span class="text-gray-400">Nama Ayah</span>
                        <strong class="text-gray-900 dark:text-white"><?= esc($siswa['nama_ayah'] ?: '-') ?></strong>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50 dark:border-slate-700/40">
                        <span class="text-gray-400">Nama Ibu</span>
                        <strong class="text-gray-900 dark:text-white"><?= esc($siswa['nama_ibu'] ?: '-') ?></strong>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50 dark:border-slate-700/40 items-center">
                        <span class="text-gray-400">No. WhatsApp Orang Tua</span>
                        <?php if(!empty($siswa['no_hp_ortu'])): ?>
                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $siswa['no_hp_ortu']) ?>" target="_blank" class="font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1 font-mono">
                                <i class="ph ph-whatsapp-logo text-base"></i>
                                <span><?= esc($siswa['no_hp_ortu']) ?></span>
                            </a>
                        <?php else: ?>
                            <span class="text-gray-400 italic">Belum diisi</span>
                        <?php endif; ?>
                    </div>
                    <div class="pt-2">
                        <span class="text-gray-400 block mb-1.5 font-semibold">Akun Portal Orang Tua Terhubung:</span>
                        <?php if(empty($orangTuaList)): ?>
                            <span class="text-amber-600 dark:text-amber-400 text-xs italic block">Belum ada akun wali murid yang ditautkan ke ananda ini.</span>
                        <?php else: ?>
                            <div class="space-y-1.5">
                                <?php foreach($orangTuaList as $ot): ?>
                                    <div class="bg-gray-50 dark:bg-slate-700/40 p-2.5 rounded-xl flex items-center justify-between">
                                        <div>
                                            <strong class="text-gray-900 dark:text-white font-mono"><?= esc($ot['username']) ?></strong>
                                            <span class="text-gray-400 block text-[11px]"><?= esc($ot['email']) ?> (<?= ucfirst($ot['hubungan']) ?>)</span>
                                        </div>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                            Aktif
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Catat Jurnal Perkembangan (Alpine.js) -->
    <div 
        x-show="jurnalModalOpen" 
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
            @click.away="jurnalModalOpen = false"
        >
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100 dark:border-slate-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl">
                        <i class="ph ph-notebook"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white" x-text="jurnalData.id ? 'Edit Catatan Perkembangan' : 'Catat Jurnal Perkembangan'"></h3>
                        <p class="text-xs text-gray-400">Ananda: <?= esc($siswa['nama_lengkap']) ?></p>
                    </div>
                </div>
                <button type="button" @click="jurnalModalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200">
                    <i class="ph ph-x text-lg"></i>
                </button>
            </div>

            <form action="<?= base_url('wali-kelas/save-perkembangan') ?>" method="post" enctype="multipart/form-data" class="space-y-4">
                <?= csrf_field() ?>
                <input type="hidden" name="id" :value="jurnalData.id">
                <input type="hidden" name="siswa_id" value="<?= esc($siswa['id']) ?>">

                <!-- Pilih Kelas -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Rombongan Belajar *</label>
                    <select name="kelas_id" x-model="jurnalData.kelas_id" required class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:bg-white focus:border-emerald-500">
                        <?php foreach($kelasBinaan as $kb): ?>
                            <option value="<?= $kb['id'] ?>"><?= esc($kb['nama_rombel']) ?> (<?= esc($kb['tingkat']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Tanggal Catatan -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Tanggal Observasi *</label>
                        <input type="date" name="tanggal" x-model="jurnalData.tanggal" required class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:bg-white focus:border-emerald-500">
                    </div>

                    <!-- Kategori Perkembangan -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Aspek Perkembangan *</label>
                        <select name="kategori" x-model="jurnalData.kategori" required class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:bg-white focus:border-emerald-500">
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

                <!-- Judul Catatan -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Judul Observasi / Topik *</label>
                    <input type="text" name="judul" x-model="jurnalData.judul" placeholder="Contoh: Mampu berbagi mainan dengan teman..." required class="w-full px-3 py-2.5 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:bg-white focus:border-emerald-500">
                </div>

                <!-- Isi Catatan -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Catatan Naratif / Deskripsi Perkembangan *</label>
                    <textarea name="catatan" x-model="jurnalData.catatan" rows="4" placeholder="Jelaskan secara naratif perilaku, pencapaian, atau hal-hal yang perlu distimulasi lebih lanjut..." required class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:bg-white focus:border-emerald-500 leading-relaxed"></textarea>
                </div>

                <!-- Unggah Foto Kegiatan -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Foto Kegiatan / Dokumentasi (Opsional)</label>
                    <input type="file" name="foto" accept="image/*" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-slate-700 dark:file:text-slate-300">
                </div>

                <!-- Status Publikasi -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Status Publikasi</label>
                    <select name="status" x-model="jurnalData.status" class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:bg-white focus:border-emerald-500">
                        <option value="published">Publikasi (Dapat Dilihat Wali Murid)</option>
                        <option value="draft">Simpan sebagai Draft (Hanya Pendidik)</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100 dark:border-slate-700">
                    <button type="button" @click="jurnalModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-medium text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-md shadow-emerald-500/25 transition-all flex items-center gap-1.5">
                        <i class="ph ph-check"></i>
                        <span>Simpan Catatan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Kontak & Alamat Murid (Alpine.js) -->
    <div 
        x-show="editKontakOpen" 
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
            @click.away="editKontakOpen = false"
        >
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl">
                        <i class="ph ph-address-book"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Perbarui Kontak Murid</h3>
                        <p class="text-xs text-gray-400"><?= esc($siswa['nama_lengkap']) ?></p>
                    </div>
                </div>
                <button type="button" @click="editKontakOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200">
                    <i class="ph ph-x text-lg"></i>
                </button>
            </div>

            <form action="<?= base_url('wali-kelas/murid/update-kontak') ?>" method="post" class="space-y-4">
                <?= csrf_field() ?>
                <input type="hidden" name="siswa_id" value="<?= esc($siswa['id']) ?>">

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">No. WhatsApp Orang Tua</label>
                    <div class="relative">
                        <i class="ph ph-whatsapp-logo absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <input 
                            type="text" 
                            name="no_hp_ortu" 
                            value="<?= esc($siswa['no_hp_ortu'] ?? '') ?>" 
                            placeholder="Contoh: 081234567890" 
                            class="w-full pl-10 pr-3 py-2.5 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 font-mono"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Alamat Tempat Tinggal</label>
                    <textarea 
                        name="alamat" 
                        rows="3" 
                        placeholder="Alamat domisili ananda saat ini..." 
                        class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:bg-white focus:border-blue-500"
                    ><?= esc($siswa['alamat'] ?? '') ?></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="editKontakOpen = false" class="px-4 py-2 rounded-xl text-xs font-medium text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-500/25 transition-all flex items-center gap-1.5">
                        <i class="ph ph-check"></i>
                        <span>Simpan Kontak</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
<?= $this->endSection() ?>

