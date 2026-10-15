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
<div x-data="{ activeTab: 'profil' }">
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
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-slate-900/50 border-b border-gray-200 dark:border-slate-700 text-sm font-medium text-gray-500 dark:text-slate-400">
                            <th class="py-3 px-4">No. Transaksi</th>
                            <th class="py-3 px-4">Tanggal Bayar</th>
                            <th class="py-3 px-4 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-800 text-sm">
                        <?php if (empty($historyPembayaran)): ?>
                        <tr>
                            <td colspan="3" class="py-6 text-center text-gray-500 dark:text-slate-400">
                                <i class="ph ph-wallet text-3xl mb-2"></i>
                                <p>Belum ada riwayat pembayaran.</p>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach($historyPembayaran as $p): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                                <td class="py-3 px-4 font-medium text-blue-600 dark:text-blue-400"><?= esc($p['no_transaksi'] ?? '-') ?></td>
                                <td class="py-3 px-4"><?= !empty($p['tanggal_bayar']) ? tanggal_indo($p['tanggal_bayar']) : '-' ?></td>
                                <td class="py-3 px-4 text-right font-semibold text-gray-900 dark:text-slate-200"><?= rupiah($p['nominal_bayar'] ?? 0) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
