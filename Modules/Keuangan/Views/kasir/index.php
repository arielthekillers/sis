<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Kasir Pembayaran</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Kasir Pembayaran
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Cari nama siswa untuk langsung membuka tagihan mereka.
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="mb-6 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-8"
     x-data="kasirSearch()">
    
    <div class="max-w-2xl mx-auto relative">
        <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-3 text-center">Mulai Pembayaran Baru</label>
        
        <div class="relative">
            <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xl z-10"></i>
            <input type="text" 
                   x-model="searchQuery" 
                   @input.debounce.300ms="fetchStudents" 
                   @focus="isOpen = true"
                   @click.away="isOpen = false"
                   placeholder="Ketik Nama Siswa atau NIS/NISN..." 
                   class="block w-full pl-12 pr-4 py-4 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-2xl text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-lg shadow-sm">
                   
            <i class="ph ph-spinner animate-spin absolute right-4 top-1/2 -translate-y-1/2 text-blue-500 text-xl z-10" x-show="isLoading" style="display: none;"></i>
        </div>

        <!-- Dropdown Results -->
        <div x-show="isOpen && (searchQuery.length > 1 || results.length > 0)" 
             x-transition.opacity
             class="absolute w-full mt-2 bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden z-50 max-h-96 overflow-y-auto"
             style="display: none;">
             
            <template x-if="results.length > 0">
                <ul class="divide-y divide-gray-100 dark:divide-slate-700">
                    <template x-for="s in results" :key="s.id">
                        <li>
                            <a :href="'<?= base_url('keuangan/kasir/bayar') ?>/' + s.id" class="flex items-center gap-4 p-4 hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors group">
                                <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                                    <i class="ph ph-user text-xl"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold text-gray-900 dark:text-white truncate" x-text="s.nama_lengkap"></h4>
                                    <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-slate-400 mt-0.5">
                                        <span class="flex items-center gap-1"><i class="ph ph-identification-card"></i> <span x-text="s.nis || '-'"></span></span>
                                        <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-slate-600"></span>
                                        <span class="flex items-center gap-1"><i class="ph ph-chalkboard-teacher"></i> <span x-text="s.kelas_sekarang"></span></span>
                                    </div>
                                </div>
                                <div class="shrink-0 text-blue-500 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="ph ph-arrow-right text-xl"></i>
                                </div>
                            </a>
                        </li>
                    </template>
                </ul>
            </template>
            
            <template x-if="!isLoading && searchQuery.length > 1 && results.length === 0">
                <div class="p-6 text-center text-gray-500 dark:text-slate-400">
                    Siswa tidak ditemukan.
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function kasirSearch() {
    return {
        searchQuery: '',
        results: [],
        isLoading: false,
        isOpen: false,
        
        async fetchStudents() {
            if (this.searchQuery.length < 2) {
                this.results = [];
                return;
            }
            
            this.isLoading = true;
            try {
                const response = await fetch(`<?= base_url('keuangan/kasir/searchAjax') ?>?q=${encodeURIComponent(this.searchQuery)}`);
                const data = await response.json();
                this.results = data;
                this.isOpen = true;
            } catch (error) {
                console.error("Gagal mengambil data", error);
            } finally {
                this.isLoading = false;
            }
        }
    }
}
</script>
<?= $this->endSection() ?>
