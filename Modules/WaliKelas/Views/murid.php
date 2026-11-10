<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><a href="<?= base_url('wali-kelas/dashboard') ?>" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Portal Pendidik</a></li>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Murid Binaan</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Daftar Murid Binaan
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Daftar seluruh ananda yang berada di rombel binaan Anda. Anda dapat melihat profil, kontak orang tua, dan mengelola catatan perkembangan.
<?= $this->endSection() ?>

<?= $this->section('page_actions') ?>
<div class="flex items-center gap-2">
    <a href="<?= base_url('wali-kelas/dashboard') ?>" class="bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 px-3.5 py-2 rounded-xl text-xs font-semibold hover:bg-gray-200 dark:hover:bg-slate-700 transition-colors flex items-center gap-1.5">
        <i class="ph ph-arrow-left"></i> Kembali ke Beranda
    </a>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6" x-data="{
    searchQuery: '',
    viewMode: 'grid',
    editModalOpen: false,
    editData: {
        siswa_id: null,
        nama_lengkap: '',
        no_hp_ortu: '',
        alamat: ''
    },
    openEditModal(id, nama, hp, alamat) {
        this.editData.siswa_id = id;
        this.editData.nama_lengkap = nama;
        this.editData.no_hp_ortu = hp || '';
        this.editData.alamat = alamat || '';
        this.editModalOpen = true;
    }
}">

    <!-- Filter & Toolbar Header -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-4 sm:p-5 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
        
        <!-- Search Input -->
        <div class="w-full md:w-80 relative">
            <i class="ph ph-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
            <input 
                type="text" 
                x-model="searchQuery" 
                placeholder="Cari nama murid, NIS, atau NISN..." 
                class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white placeholder-gray-400 focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-medium"
            >
        </div>

        <div class="flex items-center gap-3 flex-wrap justify-between md:justify-end">
            <!-- Filter Rombel (Dropdown jika mengampu lebih dari 1 kelas) -->
            <?php if(count($kelasBinaan) > 1): ?>
                <div class="flex items-center gap-1.5 text-xs">
                    <span class="text-gray-400 font-medium">Filter Kelas:</span>
                    <select 
                        onchange="window.location.href='<?= base_url('wali-kelas/murid') ?>' + (this.value ? '?rombel_id=' + this.value : '')"
                        class="px-3 py-1.5 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-gray-700 dark:text-slate-200 focus:outline-none focus:border-blue-500"
                    >
                        <option value="">Semua Kelas (<?= count($kelasBinaan) ?>)</option>
                        <?php foreach($kelasBinaan as $kb): ?>
                            <option value="<?= $kb['id'] ?>" <?= $selectedRombelId == $kb['id'] ? 'selected' : '' ?>>
                                <?= esc($kb['nama_rombel']) ?> (<?= $kb['total_siswa'] ?> Siswa)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <!-- Switcher Grid / Table -->
            <div class="inline-flex p-1 bg-gray-100 dark:bg-slate-700/60 rounded-xl text-xs font-semibold">
                <button 
                    type="button" 
                    @click="viewMode = 'grid'" 
                    :class="viewMode === 'grid' ? 'bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-gray-500 dark:text-slate-400 hover:text-gray-700'"
                    class="px-2.5 py-1.5 rounded-lg transition-all flex items-center gap-1"
                    title="Tampilan Kartu Grid"
                >
                    <i class="ph ph-squares-four text-base"></i>
                    <span class="hidden sm:inline">Grid</span>
                </button>
                <button 
                    type="button" 
                    @click="viewMode = 'table'" 
                    :class="viewMode === 'table' ? 'bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-gray-500 dark:text-slate-400 hover:text-gray-700'"
                    class="px-2.5 py-1.5 rounded-lg transition-all flex items-center gap-1"
                    title="Tampilan Tabel"
                >
                    <i class="ph ph-table text-base"></i>
                    <span class="hidden sm:inline">Tabel</span>
                </button>
            </div>

            <!-- Total Murid Pill -->
            <span class="text-xs font-bold px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                Total: <?= count($muridList) ?> Murid
            </span>
        </div>
    </div>

    <!-- Kondisi 1: Tidak Ada Murid -->
    <?php if(empty($muridList)): ?>
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-12 text-center border border-gray-100 dark:border-slate-800 shadow-sm">
            <div class="w-16 h-16 bg-blue-50 dark:bg-blue-900/30 text-blue-500 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-3">
                <i class="ph ph-student"></i>
            </div>
            <h4 class="text-base font-bold text-gray-900 dark:text-white mb-1">Belum Ada Murid Binaan</h4>
            <p class="text-xs text-gray-400 max-w-sm mx-auto">
                Rombongan belajar yang Anda ampu saat ini belum memiliki anggota siswa. Silakan hubungi bagian administrasi akademik.
            </p>
        </div>
    <?php else: ?>

        <!-- MODE 1: GRID VIEW (DEFAULT) -->
        <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php foreach($muridList as $m): ?>
                <?php 
                    $waClean = preg_replace('/[^0-9]/', '', $m['no_hp_ortu'] ?? '');
                    $waUrl = !empty($waClean) ? "https://wa.me/" . $waClean . "?text=" . urlencode("Halo Bapak/Ibu wali dari " . $m['nama_lengkap'] . ", salam dari wali kelas...") : null;
                ?>
                <div 
                    x-show="!searchQuery || '<?= strtolower(esc($m['nama_lengkap'])) ?>'.includes(searchQuery.toLowerCase()) || '<?= strtolower(esc($m['nama_panggilan'] ?? '')) ?>'.includes(searchQuery.toLowerCase()) || '<?= esc($m['nis'] ?? '') ?>'.includes(searchQuery) || '<?= esc($m['nisn'] ?? '') ?>'.includes(searchQuery)"
                    class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-md hover:border-blue-200 dark:hover:border-blue-800 transition-all flex flex-col justify-between"
                >
                    <div>
                        <!-- Header Kartu: Foto, Nama, Badge Rombel -->
                        <div class="flex items-start gap-3.5 mb-4">
                            <?php if(!empty($m['foto'])): ?>
                                <img src="<?= base_url('uploads/siswa/' . $m['foto']) ?>" alt="<?= esc($m['nama_lengkap']) ?>" class="w-14 h-14 rounded-2xl object-cover border border-gray-200 dark:border-slate-700 shrink-0">
                            <?php else: ?>
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br <?= $m['jenis_kelamin'] === 'P' ? 'from-pink-500 to-rose-600' : 'from-blue-500 to-indigo-600' ?> text-white font-black text-lg flex items-center justify-center shrink-0 shadow-md">
                                    <?= strtoupper(substr($m['nama_panggilan'] ?: $m['nama_lengkap'], 0, 2)) ?>
                                </div>
                            <?php endif; ?>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap mb-1">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                        <?= esc($m['nama_rombel']) ?>
                                    </span>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold <?= $m['jenis_kelamin'] === 'P' ? 'bg-pink-100 text-pink-700 dark:bg-pink-900/40 dark:text-pink-300' : 'bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300' ?>">
                                        <?= $m['jenis_kelamin'] === 'P' ? 'Perempuan' : 'Laki-laki' ?>
                                    </span>
                                </div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white truncate">
                                    <?= esc($m['nama_lengkap']) ?>
                                </h4>
                                <p class="text-xs text-gray-400">
                                    Panggilan: <strong class="text-gray-700 dark:text-slate-300"><?= esc($m['nama_panggilan'] ?: '-') ?></strong>
                                </p>
                            </div>
                        </div>

                        <!-- Data Identitas & Kontak -->
                        <div class="bg-gray-50 dark:bg-slate-700/40 rounded-xl p-3 space-y-1.5 text-xs mb-4">
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="text-gray-400">NIS / NISN:</span>
                                <strong class="font-mono text-gray-700 dark:text-slate-300"><?= esc($m['nis'] ?: '-') ?> / <?= esc($m['nisn'] ?: '-') ?></strong>
                            </div>
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="text-gray-400">Orang Tua / Wali:</span>
                                <span class="font-medium text-gray-700 dark:text-slate-300 truncate max-w-[150px]">
                                    <?= esc($m['nama_ayah'] ?: ($m['nama_ibu'] ?: 'Wali Siswa')) ?>
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="text-gray-400">No. WhatsApp Ortu:</span>
                                <?php if(!empty($m['no_hp_ortu'])): ?>
                                    <a href="<?= $waUrl ?>" target="_blank" class="font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1 font-mono">
                                        <i class="ph ph-whatsapp-logo"></i>
                                        <span><?= esc($m['no_hp_ortu']) ?></span>
                                    </a>
                                <?php else: ?>
                                    <span class="text-amber-500 italic text-[10px]">Belum ada</span>
                                <?php endif; ?>
                            </div>
                            <div class="flex items-center justify-between text-[11px] pt-1 border-t border-gray-200/60 dark:border-slate-600/60">
                                <span class="text-gray-400">Catatan Jurnal:</span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                    <i class="ph ph-notebook"></i> <?= $m['total_catatan'] ?> Catatan
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-center gap-2 pt-2 border-t border-gray-100 dark:border-slate-700/60">
                        <a 
                            href="<?= base_url('wali-kelas/murid/profil/' . $m['siswa_id']) ?>" 
                            class="flex-1 py-2 px-3 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white text-center shadow-sm transition-all flex items-center justify-center gap-1.5"
                        >
                            <i class="ph ph-user-circle text-base"></i>
                            <span>Profil & Jurnal</span>
                        </a>

                        <!-- Tombol Cepat Edit Kontak Murid -->
                        <button 
                            type="button" 
                            @click="openEditModal(<?= $m['siswa_id'] ?>, '<?= addslashes(esc($m['nama_lengkap'])) ?>', '<?= addslashes(esc($m['no_hp_ortu'] ?? '')) ?>', '<?= addslashes(esc($m['alamat'] ?? '')) ?>')"
                            class="p-2 rounded-xl text-xs font-semibold bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 text-gray-700 dark:text-slate-300 transition-colors"
                            title="Edit Kontak & Alamat Murid"
                        >
                            <i class="ph ph-pencil-simple text-base"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- MODE 2: TABLE VIEW -->
        <div x-show="viewMode === 'table'" style="display: none;" class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 dark:bg-slate-900/50 text-gray-500 dark:text-slate-400 border-b border-gray-100 dark:border-slate-700">
                        <tr>
                            <th class="py-3.5 px-4 font-semibold">Nama Murid</th>
                            <th class="py-3.5 px-4 font-semibold">Rombel</th>
                            <th class="py-3.5 px-4 font-semibold">NIS / NISN</th>
                            <th class="py-3.5 px-4 font-semibold">Orang Tua / Kontak</th>
                            <th class="py-3.5 px-4 text-center font-semibold">Total Jurnal</th>
                            <th class="py-3.5 px-4 text-center font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-800 font-medium">
                        <?php foreach($muridList as $m): ?>
                            <?php 
                                $waClean = preg_replace('/[^0-9]/', '', $m['no_hp_ortu'] ?? '');
                                $waUrl = !empty($waClean) ? "https://wa.me/" . $waClean . "?text=" . urlencode("Halo Bapak/Ibu wali dari " . $m['nama_lengkap'] . "...") : null;
                            ?>
                            <tr 
                                x-show="!searchQuery || '<?= strtolower(esc($m['nama_lengkap'])) ?>'.includes(searchQuery.toLowerCase()) || '<?= strtolower(esc($m['nama_panggilan'] ?? '')) ?>'.includes(searchQuery.toLowerCase()) || '<?= esc($m['nis'] ?? '') ?>'.includes(searchQuery) || '<?= esc($m['nisn'] ?? '') ?>'.includes(searchQuery)"
                                class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors"
                            >
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br <?= $m['jenis_kelamin'] === 'P' ? 'from-pink-500 to-rose-600' : 'from-blue-500 to-indigo-600' ?> text-white font-bold text-xs flex items-center justify-center shrink-0">
                                            <?= strtoupper(substr($m['nama_panggilan'] ?: $m['nama_lengkap'], 0, 2)) ?>
                                        </div>
                                        <div>
                                            <a href="<?= base_url('wali-kelas/murid/profil/' . $m['siswa_id']) ?>" class="font-bold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                                <?= esc($m['nama_lengkap']) ?>
                                            </a>
                                            <span class="block text-[11px] text-gray-400">Panggilan: <?= esc($m['nama_panggilan'] ?: '-') ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                        <?= esc($m['nama_rombel']) ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-mono text-[11px] text-gray-600 dark:text-slate-300">
                                    <?= esc($m['nis'] ?: '-') ?> / <?= esc($m['nisn'] ?: '-') ?>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-xs font-semibold text-gray-800 dark:text-slate-200">
                                        <?= esc($m['nama_ayah'] ?: ($m['nama_ibu'] ?: 'Wali Siswa')) ?>
                                    </div>
                                    <?php if(!empty($m['no_hp_ortu'])): ?>
                                        <a href="<?= $waUrl ?>" target="_blank" class="text-[11px] text-emerald-600 dark:text-emerald-400 hover:underline font-mono flex items-center gap-1">
                                            <i class="ph ph-whatsapp-logo"></i> <?= esc($m['no_hp_ortu']) ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                                        <?= $m['total_catatan'] ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a 
                                            href="<?= base_url('wali-kelas/murid/profil/' . $m['siswa_id']) ?>" 
                                            class="px-2.5 py-1.5 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-300 transition-colors"
                                            title="Buka Profil & Jurnal"
                                        >
                                            Profil & Jurnal
                                        </a>
                                        <button 
                                            type="button" 
                                            @click="openEditModal(<?= $m['siswa_id'] ?>, '<?= addslashes(esc($m['nama_lengkap'])) ?>', '<?= addslashes(esc($m['no_hp_ortu'] ?? '')) ?>', '<?= addslashes(esc($m['alamat'] ?? '')) ?>')"
                                            class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors"
                                            title="Edit Kontak"
                                        >
                                            <i class="ph ph-pencil-simple text-base"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php endif; ?>

    <!-- Modal Edit Kontak & Alamat Murid (Alpine.js) -->
    <div 
        x-show="editModalOpen" 
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
            @click.away="editModalOpen = false"
        >
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl">
                        <i class="ph ph-address-book"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Perbarui Kontak Murid</h3>
                        <p class="text-xs text-gray-400" x-text="editData.nama_lengkap"></p>
                    </div>
                </div>
                <button type="button" @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200">
                    <i class="ph ph-x text-lg"></i>
                </button>
            </div>

            <form action="<?= base_url('wali-kelas/murid/update-kontak') ?>" method="post" class="space-y-4">
                <?= csrf_field() ?>
                <input type="hidden" name="siswa_id" :value="editData.siswa_id">

                <!-- No HP WhatsApp Orang Tua -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">No. WhatsApp Orang Tua</label>
                    <div class="relative">
                        <i class="ph ph-whatsapp-logo absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <input 
                            type="text" 
                            name="no_hp_ortu" 
                            x-model="editData.no_hp_ortu" 
                            placeholder="Contoh: 081234567890" 
                            class="w-full pl-10 pr-3 py-2.5 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 font-mono"
                        >
                    </div>
                </div>

                <!-- Alamat Tempat Tinggal -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">Alamat Tempat Tinggal</label>
                    <textarea 
                        name="alamat" 
                        x-model="editData.alamat" 
                        rows="3" 
                        placeholder="Alamat domisili ananda saat ini..." 
                        class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl text-xs text-gray-900 dark:text-white focus:bg-white focus:border-blue-500"
                    ></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button 
                        type="button" 
                        @click="editModalOpen = false" 
                        class="px-4 py-2 rounded-xl text-xs font-medium text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        class="px-5 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-500/25 transition-all flex items-center gap-1.5"
                    >
                        <i class="ph ph-check"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
<?= $this->endSection() ?>

