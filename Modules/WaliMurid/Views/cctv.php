<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><a href="<?= base_url('wali-murid/dashboard') ?>" class="text-gray-500 hover:text-gray-900 dark:text-slate-400">Portal Orang Tua</a></li>
<li><i class="ph ph-caret-right text-xs text-gray-400"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">CCTV Online (Fullday)</span></li>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Monitoring CCTV Online Kelas Fullday
<?= $this->endSection() ?>

<?= $this->section('page_subtitle') ?>
Layanan pemantauan aktivitas belajar dan istirahat ananda secara langsung selama berada di lingkungan sekolah.
<?= $this->endSection() ?>

<?= $this->section('page_actions') ?>
<div class="flex items-center gap-2">
    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400 border border-rose-200 dark:border-rose-900 shadow-sm">
        <span class="w-2 h-2 rounded-full bg-rose-600 animate-pulse"></span>
        <span>Sistem CCTV Aktif</span>
    </span>
    <a href="<?= base_url('wali-murid/dashboard') ?>" class="bg-gray-100 hover:bg-gray-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-gray-700 dark:text-slate-200 px-4 py-2 rounded-xl text-xs md:text-sm font-semibold transition-all flex items-center gap-2">
        <i class="ph ph-arrow-left"></i>
        <span>Kembali</span>
    </a>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6" x-data="{ 
    fullscreenModal: false, 
    activeCam: null, 
    streamingCams: {},
    currentTime: '',
    toggleStream(camId) {
        this.streamingCams[camId] = !this.streamingCams[camId];
    },
    isStreaming(camId) {
        return !!this.streamingCams[camId];
    },
    init() {
        this.updateClock();
        setInterval(() => this.updateClock(), 1000);
    },
    updateClock() {
        const now = new Date();
        this.currentTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';
    }
}">

    <!-- CHILD SWITCHER (Jika Orang Tua memiliki lebih dari 1 anak) -->
    <?php if(count($anakList) > 1): ?>
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                    <i class="ph ph-users-three text-blue-600 dark:text-blue-400 text-base"></i>
                    <span>Pilih Data Ananda:</span>
                </div>
                <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
                    <?php foreach($anakList as $a): ?>
                        <?php $isActive = ((int)$a['siswa_id'] === (int)$activeAnakId); ?>
                        <a 
                            href="<?= base_url('wali-murid/cctv?anak_id=' . $a['siswa_id']) ?>" 
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs md:text-sm font-semibold transition-all whitespace-nowrap <?= $isActive ? 'bg-rose-600 text-white shadow-md shadow-rose-500/25 ring-2 ring-rose-500/30' : 'bg-gray-50 dark:bg-slate-700/60 text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700' ?>"
                        >
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold <?= $isActive ? 'bg-white text-rose-600' : 'bg-rose-100 text-rose-700 dark:bg-slate-600 dark:text-slate-200' ?>">
                                <?= strtoupper(substr($a['nama_lengkap'], 0, 1)) ?>
                            </span>
                            <span><?= esc($a['nama_panggilan'] ?: $a['nama_lengkap']) ?></span>
                            <?php if(($a['program'] ?? 'Reguler') === 'Fullday'): ?>
                                <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-amber-400 text-amber-950">Fullday</span>
                            <?php else: ?>
                                <span class="text-[9px] font-normal opacity-70">Reguler</span>
                            <?php endif; ?>
                            <?php if($isActive): ?>
                                <i class="ph ph-check-circle text-xs"></i>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if(!$isFullday): ?>
        <!-- Peringatan Akses: Siswa Bukan Program Fullday -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-8 sm:p-12 text-center max-w-2xl mx-auto">
            <div class="w-20 h-20 rounded-3xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center text-4xl mx-auto mb-4 border border-amber-200 dark:border-amber-900/40">
                <i class="ph ph-lock-key"></i>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 mb-3">
                <i class="ph ph-warning-circle"></i> Khusus Program Fullday
            </span>
            <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">
                Akses CCTV Tidak Tersedia untuk Program Reguler
            </h3>
            <p class="text-sm text-gray-600 dark:text-slate-300 leading-relaxed mb-6">
                Fasilitas pemantauan CCTV secara daring diperuntukkan khusus bagi ananda yang terdaftar pada <strong>Program Fullday</strong>. 
                Saat ini ananda <strong><?= esc($activeAnak['nama_lengkap']) ?></strong> tercatat di <strong>Program Reguler</strong>.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="<?= base_url('wali-murid/dashboard') ?>" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-800 dark:text-slate-200 text-xs font-semibold transition-all">
                    Kembali ke Beranda
                </a>
                <?php 
                    $schoolWa = app_setting('school_phone', '08123456789');
                    $waClean = preg_replace('/[^0-9]/', '', $schoolWa);
                    if(strpos($waClean, '0') === 0) $waClean = '62' . substr($waClean, 1);
                ?>
                <a href="https://wa.me/<?= $waClean ?>?text=Halo%20Admin%20Sekolah,%20saya%20orang%20tua%20dari%20<?= urlencode($activeAnak['nama_lengkap']) ?>%20ingin%20berkonsultasi%20mengenai%20Program%20Fullday" target="_blank" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-md shadow-emerald-500/20 transition-all flex items-center justify-center gap-2">
                    <i class="ph ph-whatsapp-logo text-base"></i>
                    <span>Tanya Info Program Fullday</span>
                </a>
            </div>
        </div>

    <?php else: ?>

        <!-- Banner Info Ruang CCTV Aktif -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-2xl p-6 shadow-lg border border-slate-800 relative overflow-hidden">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 relative z-10">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-rose-600/20 border border-rose-500/40 text-rose-400 flex items-center justify-center text-3xl shrink-0">
                        <i class="ph ph-video-camera"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                SIARAN CCTV TERSEDIA
                            </span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                <i class="ph ph-pause-circle"></i>
                                Autoplay Nonaktif (Hemat Kuota)
                            </span>
                            <span class="text-xs text-slate-400 hidden sm:inline">|</span>
                            <span class="text-xs font-semibold text-slate-300" x-text="currentTime"></span>
                        </div>
                        <h2 class="text-xl md:text-2xl font-black text-white">
                            Monitoring Kelas <?= esc($activeAnak['nama_panggilan'] ?: $activeAnak['nama_lengkap']) ?>
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Kelas: <strong><?= esc($activeAnak['kelas']['nama_kelas'] ?? 'Kinder') ?></strong> &bull; Jadwal Siaran: <strong>Senin - Jumat, 07.30 - 16.30 WIB</strong>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 self-end md:self-auto">
                    <span class="px-3.5 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs font-semibold border border-slate-700 flex items-center gap-2">
                        <i class="ph ph-broadcast text-emerald-400 text-base"></i>
                        <span><?= count($cameras) ?> Kamera Terdaftar</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Grid Kamera CCTV -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach($cameras as $cam): ?>
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden flex flex-col group hover:shadow-md transition-all">
                    <!-- CCTV Header Bar -->
                    <div class="p-3.5 bg-gray-50 dark:bg-slate-900 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2 truncate">
                            <span class="font-mono font-bold px-2 py-0.5 rounded bg-gray-200 dark:bg-slate-800 text-gray-800 dark:text-slate-200 text-[11px] shrink-0">
                                <?= esc($cam['code']) ?>
                            </span>
                            <span class="font-bold text-gray-900 dark:text-white truncate">
                                <?= esc($cam['title']) ?>
                            </span>
                        </div>
                        <span 
                            class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold shrink-0 transition-colors"
                            :class="isStreaming('<?= $cam['id'] ?>') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700'"
                        >
                            <span class="w-1.5 h-1.5 rounded-full" :class="isStreaming('<?= $cam['id'] ?>') ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400'"></span>
                            <span x-text="isStreaming('<?= $cam['id'] ?>') ? 'Sedang Memutar' : 'Standby (Klik Putar)'"></span>
                        </span>
                    </div>

                    <!-- Video Screen (TIDAK AUTOPLAY: Standby vs Playing State) -->
                    <div class="relative bg-slate-950 aspect-video flex flex-col items-center justify-center text-white overflow-hidden select-none">
                        
                        <!-- 1. MODE STANDBY (NO AUTOPLAY) -->
                        <div x-show="!isStreaming('<?= $cam['id'] ?>')" class="w-full h-full flex flex-col items-center justify-center p-6 text-center z-10">
                            <!-- Camera Standby Icon -->
                            <div class="w-14 h-14 rounded-2xl bg-slate-800/80 border border-slate-700 flex items-center justify-center text-slate-400 text-2xl mb-2.5 shadow-inner">
                                <i class="ph ph-video-camera"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-200 tracking-wide">
                                <?= esc($cam['title']) ?>
                            </p>
                            <div class="flex items-center gap-2 mt-1 font-mono text-[11px] text-slate-400">
                                <i class="ph ph-globe text-blue-400"></i>
                                <span>IP: <?= esc($cam['ip_public']) ?><?= $cam['port'] ? ':' . esc($cam['port']) : '' ?></span>
                            </div>

                            <!-- Tombol Manual Play (No Autoplay) -->
                            <button 
                                type="button" 
                                @click="toggleStream('<?= $cam['id'] ?>')" 
                                class="mt-3.5 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-lg shadow-blue-600/30 active:scale-95 transition-all"
                            >
                                <i class="ph ph-play-circle text-lg"></i>
                                <span>Putar Siaran Kamera</span>
                            </button>
                            <span class="text-[10px] text-slate-500 mt-2 flex items-center gap-1">
                                <i class="ph ph-leaf text-emerald-400"></i>
                                <span>Autoplay dinonaktifkan demi menghemat kuota internet Anda</span>
                            </span>
                        </div>

                        <!-- 2. MODE PLAYING (Setelah tombol Putar ditekan) -->
                        <div x-show="isStreaming('<?= $cam['id'] ?>')" class="w-full h-full flex flex-col items-center justify-center relative z-10" x-cloak>
                            <!-- Top Stream Overlays -->
                            <div class="absolute top-3 left-3 flex items-center gap-2 z-10">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-black/60 backdrop-blur text-[10px] font-mono text-rose-400 font-bold border border-white/10">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                    LIVE FEED
                                </span>
                                <span class="px-2 py-0.5 rounded bg-black/60 backdrop-blur text-[10px] font-mono text-slate-300 border border-white/10">
                                    <?= esc($cam['resolution']) ?>
                                </span>
                            </div>

                            <div class="absolute top-3 right-3 z-10">
                                <span class="px-2 py-0.5 rounded bg-black/60 backdrop-blur text-[10px] font-mono text-emerald-400 font-bold border border-white/10" x-text="currentTime"></span>
                            </div>

                            <?php if (!empty($cam['stream_url']) && $cam['tipe_stream'] === 'embed'): ?>
                                <template x-if="isStreaming('<?= $cam['id'] ?>')">
                                    <iframe src="<?= esc($cam['stream_url']) ?>" class="w-full h-full border-0" allowfullscreen></iframe>
                                </template>
                            <?php else: ?>
                                <!-- Central Physical CCTV Stream Placeholder -->
                                <div class="text-center p-6 z-10 flex flex-col items-center">
                                    <div class="relative mb-3">
                                        <div class="w-14 h-14 rounded-2xl bg-slate-800/80 border border-slate-700 flex items-center justify-center text-emerald-400 text-2xl shadow-inner">
                                            <i class="ph ph-broadcast"></i>
                                        </div>
                                        <div class="absolute inset-0 rounded-2xl border border-emerald-500/30 animate-ping pointer-events-none"></div>
                                    </div>
                                    <p class="text-sm font-bold text-slate-200 tracking-wide">
                                        <?= esc($cam['title']) ?>
                                    </p>
                                    <p class="text-[11px] text-slate-400 max-w-xs mt-1">
                                        Aliran siaran CCTV terhubung ke IP Public <strong><?= esc($cam['ip_public']) ?><?= $cam['port'] ? ':' . esc($cam['port']) : '' ?></strong>. Feed aktif dari <?= esc($cam['area']) ?>.
                                    </p>
                                    <span class="mt-2 text-[10px] font-mono uppercase tracking-wider text-emerald-400 bg-emerald-950/60 px-2.5 py-0.5 rounded border border-emerald-800/50">
                                        [ Live Stream Aktif ]
                                    </span>
                                </div>
                            <?php endif; ?>

                            <!-- Bottom Stream Controls Bar -->
                            <div class="absolute bottom-0 inset-x-0 p-2.5 bg-gradient-to-t from-black/90 via-black/50 to-transparent flex items-center justify-between text-xs z-10">
                                <div class="flex items-center gap-2">
                                    <button 
                                        type="button" 
                                        @click="toggleStream('<?= $cam['id'] ?>')" 
                                        class="px-2.5 py-1 rounded bg-rose-600/80 hover:bg-rose-600 text-white text-[11px] font-semibold transition-all flex items-center gap-1 shadow-sm"
                                        title="Hentikan siaran"
                                    >
                                        <i class="ph ph-stop text-sm"></i>
                                        <span>Hentikan</span>
                                    </button>
                                    <span class="text-[10px] font-mono text-slate-400"><?= esc($cam['area']) ?></span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button 
                                        type="button" 
                                        @click="fullscreenModal = true; activeCam = <?= htmlspecialchars(json_encode($cam), ENT_QUOTES, 'UTF-8') ?>;" 
                                        class="p-1.5 rounded bg-white/10 hover:bg-white/20 text-white text-xs transition-colors flex items-center gap-1 shadow-sm"
                                        title="Tampilan Penuh"
                                    >
                                        <i class="ph ph-corners-out text-sm"></i>
                                        <span class="text-[10px] font-semibold hidden sm:inline">Perbesar</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Scanline Overlay Effect -->
                        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-white/[0.02] to-transparent pointer-events-none"></div>
                    </div>

                    <!-- CCTV Footer Info -->
                    <div class="p-4 flex-1 flex flex-col justify-between text-xs bg-white dark:bg-slate-800">
                        <p class="text-gray-600 dark:text-slate-300 mb-3 leading-relaxed">
                            <?= esc($cam['description']) ?>
                        </p>
                        <div class="pt-3 border-t border-gray-100 dark:border-slate-700/60 flex items-center justify-between text-gray-400 dark:text-slate-400 text-[11px]">
                            <span class="flex items-center gap-1">
                                <i class="ph ph-clock"></i> Jam Operasional: <?= esc($cam['schedule']) ?>
                            </span>
                            <span class="font-medium text-slate-500">Resolusi: <?= esc($cam['resolution']) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Panduan & Tata Tertib Pemantauan CCTV -->
        <div class="bg-blue-50/70 dark:bg-slate-800/80 rounded-2xl p-5 border border-blue-100 dark:border-slate-700">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center text-xl shrink-0 mt-0.5">
                    <i class="ph ph-info"></i>
                </div>
                <div class="text-xs space-y-1">
                    <h4 class="font-bold text-gray-900 dark:text-white text-sm">Tata Tertib & Privasi Pemantauan CCTV</h4>
                    <p class="text-gray-600 dark:text-slate-300 leading-relaxed">
                        Fasilitas live CCTV disediakan semata-mata untuk ketenangan bunda & ayah dalam memantau kenyamanan dan keamanan ananda selama di sekolah. 
                        Dilarang merekam, menyebarluaskan, atau mempublikasikan tangkapan layar video CCTV ke media sosial demi menjaga privasi ananda dan peserta didik lainnya.
                    </p>
                </div>
            </div>
        </div>

        <!-- Modal Tampilan Layar Penuh CCTV -->
        <div 
            x-show="fullscreenModal" 
            x-cloak 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/90 backdrop-blur-md"
            @keydown.escape.window="fullscreenModal = false"
        >
            <div 
                class="relative w-full max-w-5xl bg-slate-950 rounded-3xl overflow-hidden shadow-2xl border border-slate-800 flex flex-col"
                @click.away="fullscreenModal = false"
            >
                <div class="p-4 bg-slate-900 border-b border-slate-800 flex items-center justify-between text-white">
                    <div class="flex items-center gap-3">
                        <span class="font-mono font-bold px-2.5 py-1 rounded bg-rose-600 text-white text-xs" x-text="activeCam?.code"></span>
                        <h4 class="text-sm font-bold text-white truncate" x-text="activeCam?.title"></h4>
                        <span class="inline-flex items-center gap-1 text-[11px] text-emerald-400 font-semibold ml-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Live 1080p
                        </span>
                    </div>
                    <button @click="fullscreenModal = false" class="text-slate-400 hover:text-white p-1 rounded-lg transition-colors">
                        <i class="ph ph-x text-2xl"></i>
                    </button>
                </div>

                <div class="relative bg-slate-950 aspect-video flex flex-col items-center justify-center text-white p-8">
                    <div class="w-20 h-20 rounded-3xl bg-slate-800 border border-slate-700 flex items-center justify-center text-4xl text-rose-500 mb-4 shadow-xl">
                        <i class="ph ph-video-camera"></i>
                    </div>
                    <p class="text-lg font-bold text-white" x-text="activeCam?.title"></p>
                    <div class="flex items-center gap-2 mt-2 font-mono text-xs text-slate-400">
                        <span>IP Public:</span>
                        <span class="text-blue-300 font-bold" x-text="(activeCam?.ip_public || '103.148.12.34') + ':' + (activeCam?.port || '554')"></span>
                    </div>
                    <p class="text-xs text-slate-400 max-w-md text-center mt-2">
                        Siaran langsung terpantau jernih. Area kegiatan belajar dan pendampingan ananda program Fullday.
                    </p>
                    <span class="mt-4 text-xs font-mono text-emerald-400 bg-black/50 px-3 py-1 rounded-full border border-emerald-500/30" x-text="'Waktu Server: ' + currentTime"></span>
                </div>

                <div class="p-3 bg-slate-900/80 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
                    <span>RSK Apps CCTV System &bull; High Definition Stream</span>
                    <button @click="fullscreenModal = false" class="px-4 py-1.5 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-semibold transition-all">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

    <?php endif; ?>

</div>
<?= $this->endSection() ?>

