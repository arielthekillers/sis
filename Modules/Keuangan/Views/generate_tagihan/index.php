<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Terbitkan Tagihan</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Terbitkan Tagihan Bulanan
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Gunakan halaman ini setiap awal bulan untuk memompa tagihan-tagihan rutin (seperti SPP, Katering) ke dalam daftar buku utang siswa.
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 relative overflow-hidden group">
        <div class="absolute -right-8 -top-8 bg-emerald-50 dark:bg-emerald-500/10 w-32 h-32 rounded-full flex items-center justify-center opacity-50 group-hover:scale-110 transition-transform">
            <i class="ph ph-paper-plane-tilt text-5xl text-emerald-500/30"></i>
        </div>
        
        <form action="<?= base_url('keuangan/generate-tagihan/process') ?>" method="POST" x-data="{ isSubmitting: false, showConfirm: false }" @submit.prevent="if(!showConfirm) { showConfirm = true; } else { isSubmitting = true; $el.submit(); }">
            <?= csrf_field() ?>
            
            <div class="space-y-5 relative z-10">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Bulan Tagihan <span class="text-red-500">*</span></label>
                    <select name="bulan" required class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all sm:text-sm">
                        <?php $current_m = date('m'); foreach($bulan_list as $key => $val): ?>
                            <option value="<?= $key ?>" <?= $key == $current_m ? 'selected' : '' ?>><?= $val ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Tahun <span class="text-red-500">*</span></label>
                    <input type="number" name="tahun" value="<?= date('Y') ?>" required min="2020" max="2100" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-gray-900 dark:text-white focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all sm:text-sm">
                </div>
                
                <div x-show="showConfirm" x-collapse class="p-4 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 rounded-xl mt-4">
                    <div class="flex gap-3">
                        <i class="ph ph-info text-emerald-500 text-xl flex-shrink-0"></i>
                        <div>
                            <h4 class="text-sm font-semibold text-emerald-800 dark:text-emerald-400">Konfirmasi Penerbitan</h4>
                            <p class="text-xs text-emerald-700 dark:text-emerald-300 mt-1">Sistem akan membuatkan tagihan untuk siswa sesuai dengan bulan yang Anda pilih. Tagihan untuk siswa yang sudah ada akan secara otomatis dilewati.</p>
                            
                            <div class="mt-3 flex gap-2">
                                <button type="submit" :disabled="isSubmitting" class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors disabled:opacity-50 shadow-sm shadow-emerald-500/30">
                                    <i class="ph ph-spinner animate-spin" x-show="isSubmitting" style="display: none;"></i>
                                    <span x-text="isSubmitting ? 'Memompa data...' : 'Ya, Terbitkan Sekarang'"></span>
                                </button>
                                <button type="button" @click="showConfirm = false" class="px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-slate-300 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-600 transition-colors">
                                    Batal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" x-show="!showConfirm" class="w-full inline-flex justify-center items-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-sm font-medium text-white shadow-lg shadow-emerald-500/30 hover:bg-emerald-700 transition-all">
                    <i class="ph ph-magic-wand text-lg"></i> Terbitkan Tagihan Masal
                </button>
            </div>
        </form>
    </div>
    
    <div class="bg-gray-50 dark:bg-slate-800/50 rounded-xl p-6 border border-gray-100 dark:border-slate-800">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-500/20 text-blue-600 flex items-center justify-center">
                <i class="ph ph-info text-xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Informasi Cara Kerja</h3>
        </div>
        <ul class="space-y-3 text-sm text-gray-600 dark:text-slate-400">
            <li class="flex gap-2">
                <i class="ph ph-check-circle text-emerald-500 mt-0.5"></i>
                <span>Hanya tagihan yang memiliki siklus <b>Bulanan</b> di Master Data yang akan diterbitkan.</span>
            </li>
            <li class="flex gap-2">
                <i class="ph ph-check-circle text-emerald-500 mt-0.5"></i>
                <span>Nominal tagihan akan <b>dikunci</b> mengikuti nominal saat ini yang ada di Master Data, sehingga kebal dari inflasi/perubahan harga di masa depan.</span>
            </li>
            <li class="flex gap-2">
                <i class="ph ph-check-circle text-emerald-500 mt-0.5"></i>
                <span>Aman ditekan berkali-kali. Sistem secara cerdas akan melewati (skip) siswa yang sudah memiliki tagihan dengan judul yang sama di bulan tersebut.</span>
            </li>
        </ul>
        
        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-slate-700">
            <h4 class="text-sm font-bold text-red-600 dark:text-red-400 mb-2"><i class="ph ph-trash"></i> Batalkan Penerbitan (Rollback)</h4>
            <p class="text-xs text-gray-500 dark:text-slate-400 mb-4">Jika Anda salah menerbitkan tagihan untuk bulan tertentu, gunakan fitur ini. Sistem hanya akan menghapus tagihan yang berstatus <b>Belum Bayar</b>.</p>
            
            <form action="<?= base_url('keuangan/generate-tagihan/rollback') ?>" method="POST" x-data="{ isSubmitting: false, showConfirm: false }" @submit.prevent="if(!showConfirm) { showConfirm = true; } else { isSubmitting = true; $el.submit(); }">
                <?= csrf_field() ?>
                <div class="flex gap-2">
                    <select name="bulan_rollback" required class="block w-full px-3 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-lg text-xs focus:ring-red-500 focus:border-red-500">
                        <?php $current_m = date('m'); foreach($bulan_list as $key => $val): ?>
                            <option value="<?= $key ?>" <?= $key == $current_m ? 'selected' : '' ?>><?= $val ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" name="tahun_rollback" value="<?= date('Y') ?>" required min="2020" max="2100" class="block w-24 px-3 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-lg text-xs focus:ring-red-500 focus:border-red-500">
                    <button type="submit" x-show="!showConfirm" class="px-4 py-2 bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400 text-xs font-semibold rounded-lg hover:bg-red-100 dark:hover:bg-red-500/20 border border-red-200 dark:border-red-800/30 whitespace-nowrap">
                        Rollback
                    </button>
                    
                    <button type="submit" x-show="showConfirm" class="px-4 py-2 bg-red-600 text-white text-xs font-semibold rounded-lg hover:bg-red-700 whitespace-nowrap shadow-md shadow-red-500/30" style="display:none;">
                        <i class="ph ph-spinner animate-spin" x-show="isSubmitting"></i>
                        <span x-text="isSubmitting ? 'Menghapus...' : 'Ya, Hapus'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
