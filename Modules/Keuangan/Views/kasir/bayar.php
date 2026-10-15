<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><a href="<?= base_url('keuangan/kasir') ?>" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Kasir Pembayaran</a></li>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Bayar Tagihan</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Pembayaran: <?= esc($siswa['nama_lengkap']) ?>
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Daftar tagihan yang belum lunas. Pilih tagihan yang ingin dibayar. Anda dapat mengubah nominal jika ingin membayar sebagian (mencicil).
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<form action="<?= base_url('keuangan/kasir/process') ?>" method="POST" x-data="kasirApp()">
    <?= csrf_field() ?>
    <input type="hidden" name="siswa_id" value="<?= $siswa['id'] ?>">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Kolom Kiri: Daftar Tagihan -->
        <div class="lg:col-span-2 space-y-4">
            <?php if (empty($tagihan)): ?>
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-8 text-center">
                    <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ph ph-check-circle text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Semua Tagihan Lunas!</h3>
                    <p class="text-gray-500 dark:text-slate-400">Tidak ada tagihan tertunggak untuk siswa ini.</p>
                </div>
            <?php else: ?>
                <?php foreach($tagihan as $idx => $t): ?>
                    <?php 
                        $sisa = $t['nominal_tagihan'] - $t['nominal_dibayar'];
                    ?>
                    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-5 flex flex-col sm:flex-row gap-4 items-start sm:items-center cursor-pointer hover:border-blue-300 dark:hover:border-blue-600 transition-colors"
                         @click="toggleTagihan(<?= $t['id'] ?>, <?= $sisa ?>)">
                         
                        <div class="flex-shrink-0 pt-1">
                            <input type="checkbox" id="chk_<?= $t['id'] ?>" :checked="tagihan[<?= $t['id'] ?>] !== undefined" class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 pointer-events-none">
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <h4 class="font-bold text-gray-900 dark:text-white truncate"><?= esc($t['judul_tagihan']) ?></h4>
                                <?php if($t['status'] == 'Mencicil'): ?>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400">Dicicil</span>
                                <?php endif; ?>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-slate-400 flex flex-wrap gap-x-4 gap-y-1">
                                <span>Tagihan: <?= rupiah($t['nominal_tagihan']) ?></span>
                                <?php if($t['nominal_dibayar'] > 0): ?>
                                    <span class="text-emerald-600 dark:text-emerald-400">Telah dibayar: <?= rupiah($t['nominal_dibayar']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="flex flex-col items-end gap-2 w-full sm:w-48 shrink-0" @click.stop>
                            <span class="text-xs font-semibold text-gray-500 dark:text-slate-400">Akan dibayar:</span>
                            
                            <!-- Input Nominal Bayar -->
                            <div class="relative w-full" x-show="tagihan[<?= $t['id'] ?>] !== undefined">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                                <input type="text" name="bayar[<?= $t['id'] ?>]" x-model="tagihan[<?= $t['id'] ?>]" @input="formatRupiah($event, <?= $t['id'] ?>, <?= $sisa ?>)" class="block w-full pl-9 pr-3 py-2 bg-gray-50 dark:bg-slate-700/50 border-gray-200 dark:border-slate-600 rounded-lg text-sm font-bold text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-right">
                            </div>
                            
                            <div x-show="tagihan[<?= $t['id'] ?>] === undefined" class="text-lg font-bold text-red-600 dark:text-red-400 py-1">
                                <?= rupiah($sisa) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Kolom Kanan: Summary & Checkout -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 sticky top-6">
                <div class="p-6 border-b border-gray-100 dark:border-slate-700">
                    <h3 class="font-bold text-gray-900 dark:text-white">Ringkasan Pembayaran</h3>
                </div>
                
                <div class="p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Metode Pembayaran</label>
                        <select name="metode_pembayaran" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-transparent rounded-xl text-sm focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                            <option value="Tunai">Tunai / Cash</option>
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="QRIS">QRIS / E-Wallet</option>
                        </select>
                    </div>
                    
                    <div class="pt-4 border-t border-gray-100 dark:border-slate-700">
                        <div class="flex justify-between items-end mb-4">
                            <span class="text-sm text-gray-500 dark:text-slate-400">Total Tagihan (Dipilih)</span>
                            <span class="text-2xl font-bold text-blue-600 dark:text-blue-400" x-text="formatCurrency(totalBayar)">Rp 0</span>
                        </div>
                        
                        <!-- Saldo Deposit Siswa -->
                        <?php if(isset($siswa['saldo_deposit']) && $siswa['saldo_deposit'] > 0): ?>
                        <div class="mb-4 p-3 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl border border-emerald-100 dark:border-emerald-500/20 flex flex-col gap-2">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-emerald-700 dark:text-emerald-400">Saldo Deposit Tersedia</span>
                                <span class="text-sm font-bold text-emerald-700 dark:text-emerald-400"><?= rupiah($siswa['saldo_deposit']) ?></span>
                            </div>
                            <label class="flex items-center gap-2 cursor-pointer mt-1">
                                <input type="checkbox" name="gunakan_deposit" value="1" x-model="gunakanDeposit" class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                <span class="text-xs text-gray-600 dark:text-slate-400">Potong tagihan dengan saldo deposit</span>
                            </label>
                        </div>
                        <?php endif; ?>

                        <!-- Uang Diterima & Kembalian -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Uang Diterima / Transfer (Rp)</label>
                                <input type="text" name="uang_diterima" x-model="uangDiterima" @input="formatUangDiterima" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border-gray-200 dark:border-slate-600 rounded-xl text-lg font-bold text-gray-900 dark:text-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-right" placeholder="0">
                            </div>
                            
                            <div class="flex justify-between items-center" x-show="uangDiterimaVal > 0">
                                <span class="text-sm font-medium text-gray-500 dark:text-slate-400">Kembalian / Sisa</span>
                                <span class="text-lg font-bold text-amber-600 dark:text-amber-400" x-text="formatCurrency(kembalian)">Rp 0</span>
                            </div>
                            
                            <label class="flex items-center gap-2 cursor-pointer bg-blue-50 dark:bg-blue-500/10 p-2 rounded-lg border border-blue-100 dark:border-blue-500/20" x-show="kembalian > 0">
                                <input type="checkbox" name="simpan_deposit" value="1" checked class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-xs font-medium text-blue-800 dark:text-blue-300">Simpan kembalian ke Saldo Tabungan Siswa</span>
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" :disabled="totalBayar <= 0 || (sisaTagihanSetelahDeposit > 0 && uangDiterimaVal < sisaTagihanSetelahDeposit)" class="w-full flex justify-center items-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-500/30 hover:bg-blue-700 transition-all disabled:opacity-50 disabled:shadow-none">
                        <i class="ph ph-check-circle text-lg"></i> Proses Pembayaran
                    </button>
                </div>
            </div>
        </div>
        
    </div>
</form>

<script>
function kasirApp() {
    return {
        tagihan: {}, // Menyimpan tagihan_id -> nominal
        uangDiterima: '',
        gunakanDeposit: false,
        saldoDeposit: <?= isset($siswa['saldo_deposit']) ? $siswa['saldo_deposit'] : 0 ?>,
        
        get uangDiterimaVal() {
            let val = String(this.uangDiterima).replace(/[^0-9]/g, '');
            return val ? parseInt(val) : 0;
        },
        
        get sisaTagihanSetelahDeposit() {
            if(this.gunakanDeposit) {
                return Math.max(0, this.totalBayar - this.saldoDeposit);
            }
            return this.totalBayar;
        },
        
        get kembalian() {
            let sisa = this.sisaTagihanSetelahDeposit;
            if(this.uangDiterimaVal > sisa) {
                return this.uangDiterimaVal - sisa;
            }
            return 0;
        },
        
        formatUangDiterima(event) {
            let val = event.target.value.replace(/[^0-9]/g, '');
            if (!val) {
                this.uangDiterima = '';
            } else {
                this.uangDiterima = this.formatNumber(parseInt(val));
            }
        },
        
        get totalBayar() {
            let total = 0;
            for (let key in this.tagihan) {
                let val = String(this.tagihan[key]).replace(/[^0-9]/g, '');
                if (val) total += parseInt(val);
            }
            return total;
        },
        
        toggleTagihan(id, maxNominal) {
            if (this.tagihan[id] !== undefined) {
                delete this.tagihan[id];
            } else {
                this.tagihan[id] = this.formatNumber(maxNominal);
            }
        },
        
        formatRupiah(event, id, maxNominal) {
            let val = event.target.value.replace(/[^0-9]/g, '');
            if (!val) {
                this.tagihan[id] = '';
                return;
            }
            let intVal = parseInt(val);
            if (intVal > maxNominal) intVal = maxNominal; // Cegah bayar lebih dari sisa
            
            this.tagihan[id] = this.formatNumber(intVal);
        },
        
        formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        },
        
        formatCurrency(num) {
            return "Rp " + this.formatNumber(num);
        }
    }
}
</script>
<?= $this->endSection() ?>
