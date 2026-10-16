<?= $this->extend('layout/main') ?>

<!-- 1. Breadcrumb -->
<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-500 dark:text-slate-400">Keuangan</span></li>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Broadcast Tagihan</span></li>
<?= $this->endSection() ?>

<!-- 2. Judul Utama -->
<?= $this->section('page_title') ?>
Broadcast Pengingat Tagihan WhatsApp
<?= $this->endSection() ?>

<!-- 3. Sub Judul -->
<?= $this->section('page_subtitle') ?>
Kirim pemberitahuan tagihan bulanan secara otomatis dengan proteksi jeda acak (Anti-Banned) ke nomor WhatsApp orang tua.
<?= $this->endSection() ?>

<!-- 4. Tombol Aksi Kanan Atas -->
<?= $this->section('page_actions') ?>
<div class="flex items-center gap-3">
    <!-- Filter Bulan & Tahun Form -->
    <form method="GET" action="<?= base_url('keuangan/broadcast-tagihan') ?>" class="flex items-center gap-2">
        <select name="bulan" onchange="this.form.submit()" class="px-3 py-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl text-xs font-medium text-gray-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-500/20">
            <?php foreach($bulanList as $k => $v): ?>
                <option value="<?= $k ?>" <?= $bulanAktif == $k ? 'selected' : '' ?>><?= $v ?></option>
            <?php endforeach; ?>
        </select>
        <select name="tahun" onchange="this.form.submit()" class="px-3 py-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl text-xs font-medium text-gray-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-500/20">
            <?php for($y = date('Y') - 1; $y <= date('Y') + 1; $y++): ?>
                <option value="<?= $y ?>" <?= $tahunAktif == $y ? 'selected' : '' ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>
    </form>

    <?php if ($waIsActive && $totalNomorValid > 0): ?>
    <button @click="$dispatch('start-broadcast')" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-xs font-semibold transition-all shadow-md shadow-emerald-600/20 active:scale-95">
        <i class="ph ph-paper-plane-tilt text-base"></i>
        <span>Kirim Broadcast Massal</span>
    </button>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

<!-- 5. Konten Utama -->
<?= $this->section('content') ?>
<div x-data="{
    // Data Siswa
    siswaList: <?= htmlspecialchars(json_encode($siswaList), ENT_QUOTES, 'UTF-8') ?>,
    bulanAktif: '<?= $bulanAktif ?>',
    tahunAktif: '<?= $tahunAktif ?>',
    
    // Broadcast State
    runnerOpen: false,
    isRunning: false,
    isPaused: false,
    currentIndex: 0,
    totalTargets: 0,
    successCount: 0,
    failedCount: 0,
    skippedCount: 0,
    countdown: 0,
    countdownTimer: null,
    currentStudentName: '',
    currentStatusText: '',
    logs: [],
    
    // Delay Config
    delayMin: <?= $waDelayMin ?>,
    delayMax: <?= $waDelayMax ?>,

    startBroadcast() {
        const targets = this.siswaList.filter(s => s.no_hp_ortu && s.no_hp_ortu.trim() !== '');
        if (targets.length === 0) {
            Swal.fire('Perhatian', 'Tidak ada siswa dengan nomor WhatsApp valid untuk dibroadcast.', 'warning');
            return;
        }

        this.totalTargets = targets.length;
        this.currentIndex = 0;
        this.successCount = 0;
        this.failedCount = 0;
        this.skippedCount = 0;
        this.logs = [];
        this.runnerOpen = true;
        this.isRunning = true;
        this.isPaused = false;

        this.processNext(targets);
    },

    async processNext(targets) {
        if (!this.isRunning) return;

        if (this.isPaused) {
            this.currentStatusText = 'Broadcast dijeda oleh pengguna.';
            return;
        }

        if (this.currentIndex >= targets.length) {
            this.isRunning = false;
            this.currentStatusText = '🎉 Broadcast selesai! Seluruh antrean telah diproses.';
            Swal.fire({
                icon: 'success',
                title: 'Broadcast Selesai',
                text: `Berhasil terkirim: ${this.successCount}, Gagal: ${this.failedCount}`,
            });
            return;
        }

        const student = targets[this.currentIndex];
        this.currentStudentName = student.nama_lengkap + ' (' + student.nama_kelas + ')';
        this.currentStatusText = 'Sedang mengirim pesan WhatsApp...';

        try {
            const formData = new FormData();
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
            formData.append('siswa_id', student.siswa_id);
            formData.append('bulan', this.bulanAktif);
            formData.append('tahun', this.tahunAktif);
            formData.append('phone', student.no_hp_ortu);

            const res = await fetch('<?= base_url('keuangan/broadcast-tagihan/send-reminder') ?>', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();

            if (data.status === 'success') {
                this.successCount++;
                this.addLog('success', student.nama_lengkap, student.no_hp_ortu, 'Terkirim');
            } else {
                this.failedCount++;
                this.addLog('error', student.nama_lengkap, student.no_hp_ortu, data.message || 'Gagal');
            }
        } catch (err) {
            this.failedCount++;
            this.addLog('error', student.nama_lengkap, student.no_hp_ortu, 'Kesalahan Jaringan');
        }

        this.currentIndex++;

        // Jika masih ada sisa antrean, lakukan jeda acak (Anti-Banned Jitter)
        if (this.currentIndex < targets.length && this.isRunning && !this.isPaused) {
            const randomDelay = Math.floor(Math.random() * (this.delayMax - this.delayMin + 1)) + this.delayMin;
            this.countdown = randomDelay;

            this.countdownTimer = setInterval(() => {
                if (this.isPaused || !this.isRunning) {
                    clearInterval(this.countdownTimer);
                    return;
                }
                this.countdown--;
                this.currentStatusText = `🛡️ Proteksi Anti-Banned: Jeda acak ${this.countdown} detik sebelum pesan berikutnya...`;

                if (this.countdown <= 0) {
                    clearInterval(this.countdownTimer);
                    this.processNext(targets);
                }
            }, 1000);
        } else {
            this.processNext(targets);
        }
    },

    togglePause(targets) {
        this.isPaused = !this.isPaused;
        if (!this.isPaused) {
            this.processNext(targets);
        }
    },

    stopBroadcast() {
        this.isRunning = false;
        clearInterval(this.countdownTimer);
        this.currentStatusText = 'Broadcast dihentikan.';
    },

    addLog(type, name, phone, msg) {
        this.logs.unshift({
            time: new Date().toLocaleTimeString(),
            type: type,
            name: name,
            phone: phone,
            msg: msg
        });
    },

    async checkSingleContact(phone, sId) {
        if (!phone) return;
        const btn = document.getElementById('btn-check-' + sId);
        if (btn) btn.innerHTML = '<i class=\'ph ph-spinner animate-spin\'></i>';

        try {
            const formData = new FormData();
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
            formData.append('phone', phone);

            const res = await fetch('<?= base_url('keuangan/broadcast-tagihan/check-contact') ?>', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();

            if (data.status === 'success' && data.data && data.data.numberExists) {
                Swal.fire('Nomor WhatsApp Aktif', `Nomor ${phone} terdaftar dan aktif di WhatsApp.`, 'success');
            } else {
                Swal.fire('Nomor Tidak Ditemukan', `Nomor ${phone} tidak terdaftar di WhatsApp atau tidak aktif.`, 'warning');
            }
        } catch(e) {
            Swal.fire('Error', 'Gagal memvalidasi nomor: ' + e.message, 'error');
        } finally {
            if (btn) btn.innerHTML = '<i class=\'ph ph-check-circle\'></i> Cek WA';
        }
    },

    async sendSingleReminder(sId, phone, name) {
        if (!phone) {
            Swal.fire('Perhatian', 'Nomor WhatsApp orang tua belum terdaftar.', 'warning');
            return;
        }

        const confirm = await Swal.fire({
            title: 'Kirim Pengingat?',
            text: `Kirim rincian tagihan via WhatsApp ke orang tua dari ${name} (${phone})?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kirim',
            cancelButtonText: 'Batal'
        });

        if (!confirm.isConfirmed) return;

        Swal.fire({
            title: 'Sedang Mengirim...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        try {
            const formData = new FormData();
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
            formData.append('siswa_id', sId);
            formData.append('bulan', this.bulanAktif);
            formData.append('tahun', this.tahunAktif);
            formData.append('phone', phone);

            const res = await fetch('<?= base_url('keuangan/broadcast-tagihan/send-reminder') ?>', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();

            if (data.status === 'success') {
                Swal.fire('Berhasil!', data.message, 'success');
            } else {
                Swal.fire('Gagal', data.message || 'Terjadi kesalahan.', 'error');
            }
        } catch(e) {
            Swal.fire('Error', 'Kesalahan koneksi: ' + e.message, 'error');
        }
    },

    async promptEditPhone(sId, sName, currentPhone) {
        const { value: newPhone } = await Swal.fire({
            title: 'No. WhatsApp Wali Murid',
            text: `Masukkan nomor WhatsApp orang tua/wali dari ${sName}:`,
            input: 'text',
            inputValue: currentPhone || '',
            inputPlaceholder: 'Contoh: 081234567890',
            showCancelButton: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
            inputValidator: (value) => {
                if (!value || !value.trim()) {
                    return 'Nomor WhatsApp tidak boleh kosong!';
                }
            }
        });

        if (newPhone) {
            Swal.fire({
                title: 'Menyimpan...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            try {
                const formData = new FormData();
                formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                formData.append('siswa_id', sId);
                formData.append('phone', newPhone.trim());

                const res = await fetch('<?= base_url('keuangan/broadcast-tagihan/update-phone') ?>', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.status === 'success') {
                    Swal.fire('Tersimpan!', data.message, 'success').then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Gagal', data.message || 'Terjadi kesalahan.', 'error');
                }
            } catch(e) {
                Swal.fire('Error', 'Kesalahan koneksi: ' + e.message, 'error');
            }
        }
    }
}" 
@start-broadcast.window="startBroadcast()"
class="space-y-6">

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Siswa Menunggak -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-gray-100 dark:border-slate-800 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 dark:bg-rose-600/10 dark:text-rose-400 flex items-center justify-center text-2xl shrink-0">
                <i class="ph ph-users-three"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-slate-400 font-medium">Siswa Belum Lunas</p>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5"><?= $totalSiswa ?></h3>
            </div>
        </div>

        <!-- Total Tunggakan -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-gray-100 dark:border-slate-800 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-600/10 dark:text-amber-400 flex items-center justify-center text-2xl shrink-0">
                <i class="ph ph-currency-circle-dollar"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-slate-400 font-medium">Total Nominal Tertunggak</p>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mt-0.5"><?= rupiah($totalNominalTertunggak) ?></h3>
            </div>
        </div>

        <!-- Nomor Valid WA -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-gray-100 dark:border-slate-800 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-600/10 dark:text-emerald-400 flex items-center justify-center text-2xl shrink-0">
                <i class="ph ph-whatsapp-logo"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-slate-400 font-medium">No. WhatsApp Siap Kirim</p>
                <h3 class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-0.5"><?= $totalNomorValid ?></h3>
            </div>
        </div>

        <!-- Nomor Kosong -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-gray-100 dark:border-slate-800 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gray-100 text-gray-500 dark:bg-slate-700 dark:text-slate-400 flex items-center justify-center text-2xl shrink-0">
                <i class="ph ph-phone-x"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-slate-400 font-medium">Nomor Belum Terisi</p>
                <h3 class="text-2xl font-bold text-gray-700 dark:text-slate-300 mt-0.5"><?= $totalNomorKosong ?></h3>
            </div>
        </div>
    </div>

    <!-- Status & Info Banner Anti-Banned -->
    <div class="p-4 sm:p-5 rounded-2xl border <?= $waIsActive ? 'bg-emerald-50/60 dark:bg-emerald-950/20 border-emerald-100 dark:border-emerald-900/30' : 'bg-amber-50/60 dark:bg-amber-950/20 border-amber-100 dark:border-amber-900/30' ?> flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl <?= $waIsActive ? 'bg-emerald-600 text-white' : 'bg-amber-500 text-white' ?> flex items-center justify-center text-xl shrink-0 shadow-sm">
                <i class="ph <?= $waIsActive ? 'ph-shield-check' : 'ph-warning' ?>"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                        <?= $waIsActive ? 'Gateway WhatsApp Aktif & Terlindungi' : 'Gateway WhatsApp Belum Diaktifkan' ?>
                    </h4>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase <?= $waIsActive ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300' ?>">
                        <?= $waIsActive ? 'Siap Kirim' : 'Nonaktif' ?>
                    </span>
                </div>
                <p class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">
                    Jadwal Bulanan: <strong>Setiap Tanggal <?= $reminderDay ?></strong> · Proteksi Jeda Acak: <strong><?= $waDelayMin ?> - <?= $waDelayMax ?> Detik</strong> per pesan untuk mencegah blokir bot.
                </p>
            </div>
        </div>

        <a href="<?= base_url('konfigurasi') ?>" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-200 border border-gray-200 dark:border-slate-700 hover:bg-gray-50 transition-colors shrink-0 self-start md:self-auto shadow-xs">
            <i class="ph ph-gear text-base"></i>
            <span>Ubah Aturan Pengingat</span>
        </a>
    </div>

    <!-- Tabel Daftar Siswa yang Menunggak -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
        <div class="p-5 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Daftar Tagihan Periode <?= $namaBulan ?> <?= $tahunAktif ?></h3>
                <p class="text-xs text-gray-500 dark:text-slate-400">Rincian siswa dengan kewajiban administrasi yang belum terselesaikan.</p>
            </div>
        </div>

        <?php if (empty($siswaList)): ?>
        <div class="p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-600 dark:bg-emerald-600/20 dark:text-emerald-400 flex items-center justify-center text-3xl mx-auto mb-3">
                <i class="ph ph-check-circle"></i>
            </div>
            <h4 class="text-base font-bold text-gray-900 dark:text-white">Semua Tagihan Lunas!</h4>
            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">Tidak ada tagihan tertunggak pada bulan <?= $namaBulan ?> <?= $tahunAktif ?>. Seluruh siswa telah menyelesaikan pembayaran.</p>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-slate-900/40 text-[11px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider border-b border-gray-100 dark:border-slate-800">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Siswa & Kelas</th>
                        <th class="py-3 px-4">Rincian Pos Tagihan</th>
                        <th class="py-3 px-4 text-right">Total Tunggakan</th>
                        <th class="py-3 px-4">No. WhatsApp Ortu</th>
                        <th class="py-3 px-4 text-center">Status Nomor</th>
                        <th class="py-3 px-4 text-center">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800 text-xs">
                    <?php $no = 1; foreach($siswaList as $s): ?>
                    <tr class="hover:bg-gray-50/60 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="py-3.5 px-4 text-center font-mono text-gray-400 dark:text-slate-500"><?= $no++ ?></td>
                        <td class="py-3.5 px-4">
                            <p class="font-bold text-gray-900 dark:text-white text-sm"><?= esc($s['nama_lengkap']) ?></p>
                            <p class="text-[11px] text-gray-500 dark:text-slate-400 font-mono">NIS: <?= esc($s['nis'] ?: '-') ?> · Kelas: <span class="font-semibold text-blue-600 dark:text-blue-400"><?= esc($s['nama_kelas']) ?></span></p>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="space-y-1">
                                <?php foreach($s['items'] as $item): ?>
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>
                                    <span class="text-gray-700 dark:text-slate-300"><?= esc($item['judul_tagihan']) ?></span>
                                    <span class="text-gray-400 font-mono">(<?= rupiah($item['sisa']) ?>)</span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-right font-bold text-rose-600 dark:text-rose-400 font-mono text-sm whitespace-nowrap">
                            <?= rupiah($s['total_tunggakan']) ?>
                        </td>
                        <td class="py-3.5 px-4 font-mono font-medium">
                            <div class="flex items-center gap-1.5">
                                <?php if (!empty($s['no_hp_ortu'])): ?>
                                    <span class="text-gray-800 dark:text-slate-200"><?= esc($s['no_hp_ortu']) ?></span>
                                    <button type="button" @click="promptEditPhone(<?= $s['siswa_id'] ?>, '<?= esc(addslashes($s['nama_lengkap'])) ?>', '<?= esc($s['no_hp_ortu']) ?>')" class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors p-0.5" title="Ubah Nomor">
                                        <i class="ph ph-pencil-simple text-xs"></i>
                                    </button>
                                <?php else: ?>
                                    <span class="text-gray-400 italic">Belum diisi</span>
                                    <button type="button" @click="promptEditPhone(<?= $s['siswa_id'] ?>, '<?= esc(addslashes($s['nama_lengkap'])) ?>', '')" class="text-blue-600 dark:text-blue-400 hover:underline text-[11px] font-sans font-medium flex items-center gap-0.5 ml-1">
                                        <i class="ph ph-plus-circle"></i> Isi
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <?php if (!empty($s['no_hp_ortu'])): ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                    <i class="ph ph-check"></i> Siap Kirim
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                    <i class="ph ph-x"></i> Tidak Ada No
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                <?php if (!empty($s['no_hp_ortu'])): ?>
                                <button type="button" id="btn-check-<?= $s['siswa_id'] ?>" @click="checkSingleContact('<?= esc($s['no_hp_ortu']) ?>', <?= $s['siswa_id'] ?>)" title="Cek apakah nomor terdaftar di WhatsApp"
                                    class="px-2.5 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-slate-200 text-xs font-semibold transition-colors flex items-center gap-1">
                                    <i class="ph ph-check-circle"></i>
                                    <span>Cek WA</span>
                                </button>
                                <button type="button" @click="sendSingleReminder(<?= $s['siswa_id'] ?>, '<?= esc($s['no_hp_ortu']) ?>', '<?= esc(addslashes($s['nama_lengkap'])) ?>')" title="Kirim pengingat ke siswa ini"
                                    class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition-colors flex items-center gap-1 shadow-xs">
                                    <i class="ph ph-whatsapp-logo text-sm"></i>
                                    <span>Kirim</span>
                                </button>
                                <?php else: ?>
                                <button type="button" @click="promptEditPhone(<?= $s['siswa_id'] ?>, '<?= esc(addslashes($s['nama_lengkap'])) ?>', '')" class="px-2.5 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-300 text-xs font-semibold transition-colors flex items-center gap-1">
                                    <i class="ph ph-plus-circle"></i>
                                    <span>Isi Nomor</span>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- MODAL LIVE BROADCAST RUNNER (ANTI-BANNED REAL-TIME) -->
    <div x-show="runnerOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" x-transition.opacity>
        <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm"></div>
        <div class="min-h-full flex items-center justify-center p-4">
            <div class="relative bg-white dark:bg-slate-800 rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl border border-gray-100 dark:border-slate-700 space-y-6" @click.stop>
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-slate-700">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-2xl shadow-lg shadow-emerald-600/25">
                            <i class="ph ph-paper-plane-tilt"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">Eksekusi Broadcast WhatsApp</h3>
                            <p class="text-xs text-gray-500 dark:text-slate-400">Proteksi Jeda Acak (Human-like Jitter)</p>
                        </div>
                    </div>

                    <button type="button" @click="stopBroadcast(); runnerOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 p-1">
                        <i class="ph ph-x text-xl"></i>
                    </button>
                </div>

                <!-- Progress Bar & Metrics -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-gray-700 dark:text-slate-300">Kemajuan Pengiriman:</span>
                        <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400">
                            <span x-text="currentIndex"></span> / <span x-text="totalTargets"></span> Pesan
                            (<span x-text="totalTargets > 0 ? Math.round((currentIndex / totalTargets) * 100) : 0"></span>%)
                        </span>
                    </div>

                    <!-- Bar -->
                    <div class="w-full h-3 rounded-full bg-gray-100 dark:bg-slate-700 overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-400 transition-all duration-300 rounded-full"
                            :style="'width: ' + (totalTargets > 0 ? Math.round((currentIndex / totalTargets) * 100) : 0) + '%'"></div>
                    </div>

                    <!-- 3 Pill Stats -->
                    <div class="grid grid-cols-3 gap-2 pt-1 text-center">
                        <div class="p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-800">
                            <p class="text-[10px] text-emerald-600 font-semibold uppercase">Berhasil</p>
                            <p class="text-lg font-bold text-emerald-700 dark:text-emerald-400" x-text="successCount"></p>
                        </div>
                        <div class="p-2.5 rounded-xl bg-rose-50 dark:bg-rose-950/30 border border-rose-100 dark:border-rose-800">
                            <p class="text-[10px] text-rose-600 font-semibold uppercase">Gagal</p>
                            <p class="text-lg font-bold text-rose-700 dark:text-rose-400" x-text="failedCount"></p>
                        </div>
                        <div class="p-2.5 rounded-xl bg-gray-50 dark:bg-slate-700/40 border border-gray-100 dark:border-slate-700">
                            <p class="text-[10px] text-gray-500 font-semibold uppercase">Sisa Antrean</p>
                            <p class="text-lg font-bold text-gray-700 dark:text-slate-300" x-text="totalTargets - currentIndex"></p>
                        </div>
                    </div>
                </div>

                <!-- Status Banner Real-time -->
                <div class="p-4 rounded-2xl bg-gray-50 dark:bg-slate-900/50 border border-gray-100 dark:border-slate-700/60 text-xs space-y-1">
                    <p class="font-bold text-gray-800 dark:text-white" x-text="currentStudentName || 'Menunggu...'"></p>
                    <p class="text-gray-500 dark:text-slate-400 flex items-center gap-1.5" x-text="currentStatusText"></p>
                </div>

                <!-- Activity Log List (Scrollable) -->
                <div>
                    <h5 class="text-xs font-bold text-gray-700 dark:text-slate-300 mb-2">Riwayat Pengiriman:</h5>
                    <div class="max-h-48 overflow-y-auto rounded-xl border border-gray-100 dark:border-slate-700/80 p-3 space-y-2 bg-gray-50/50 dark:bg-slate-900/30 custom-scrollbar text-[11px]">
                        <template x-for="(log, idx) in logs" :key="idx">
                            <div class="flex items-center justify-between gap-2 p-1.5 rounded-lg bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700">
                                <div class="flex items-center gap-2 truncate">
                                    <span class="w-2 h-2 rounded-full shrink-0" :class="log.type === 'success' ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                                    <span class="font-semibold text-gray-800 dark:text-white truncate" x-text="log.name"></span>
                                    <span class="font-mono text-gray-400" x-text="'(' + log.phone + ')'"></span>
                                </div>
                                <span class="font-mono text-[10px] font-semibold shrink-0" :class="log.type === 'success' ? 'text-emerald-600' : 'text-rose-600'" x-text="log.msg"></span>
                            </div>
                        </template>
                        <div x-show="logs.length === 0" class="text-center text-gray-400 py-4 italic">Belum ada aktivitas.</div>
                    </div>
                </div>

                <!-- Runner Control Buttons -->
                <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-slate-700">
                    <button type="button" @click="stopBroadcast()" x-show="isRunning" class="px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 rounded-xl transition-colors">
                        Batalkan Sisa Antrean
                    </button>
                    <span x-show="!isRunning" class="text-xs text-gray-400">Proses telah selesai.</span>

                    <div class="flex items-center gap-2">
                        <button type="button" x-show="isRunning" @click="togglePause(siswaList.filter(s => s.no_hp_ortu && s.no_hp_ortu.trim() !== ''))"
                            class="px-4 py-2 text-xs font-semibold rounded-xl border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-200 hover:bg-gray-100 transition-colors">
                            <span x-text="isPaused ? 'Lanjutkan' : 'Jeda (Pause)'"></span>
                        </button>
                        <button type="button" @click="stopBroadcast(); runnerOpen = false"
                            class="px-5 py-2 text-xs font-semibold rounded-xl bg-gray-900 text-white dark:bg-white dark:text-gray-900 hover:opacity-90 transition-opacity">
                            Tutup
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
