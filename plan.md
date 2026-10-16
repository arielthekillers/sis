# Rencana Pengembangan Sistem Informasi Sekolah (SIS)

Dokumen ini berisi peta jalan (roadmap) dan urutan pengerjaan modul-modul dalam Sistem Informasi Sekolah (SIS). Pengembangan dilakukan secara bertahap, dimulai dari data dasar (master) hingga data transaksional yang kompleks.

## Langkah 1: Modul Master Data (Referensi Utama)
Ini adalah data-data dasar penyangga seluruh aplikasi yang harus dikerjakan paling awal, karena seluruh modul lain akan bergantung padanya.
> **CATATAN PENTING**: Untuk fleksibilitas sistem, semua data (siswa, kelas, presensi, nilai, keuangan) **harus berbasis Tahun Ajaran**. Filter tahun ajaran aktif akan digunakan di seluruh modul.

- [x] **Manajemen Tahun Ajaran & Semester**: Menentukan tahun ajaran dan semester yang sedang aktif (misal: 2026/2027 Ganjil). Semua aktivitas siswa dan guru akan merujuk ke tabel ini.
- [x] **Manajemen Data Kelas**: Menetapkan tingkatan kelas (contoh: 10, 11, 12) dan rombongan belajar / nama kelas (contoh: X-IPA-1, X-IPS-2).
- [x] **Manajemen Mata Pelajaran**: Data mata pelajaran dan pengelompokannya.

## Langkah 2: Modul Civitas Akademika (Pengguna)
Setelah tempat (kelas) dan waktu (tahun ajaran) tersedia, kita mengelola data pengguna sistem.

- [x] **Manajemen Guru & Tenaga Kependidikan**: Mengelola biodata guru, nip, penempatan jabatan, dll.
- [x] **Manajemen Siswa**: Mengelola data siswa, mutasi, dan pembagian/penempatan siswa ke dalam kelas-kelas di tahun ajaran tertentu. Termasuk profil komprehensif.

## Langkah 3: Modul Transaksional (Aktivitas Sehari-hari)
Setelah master dan pengguna selesai, kita masuk ke fungsionalitas harian sekolah.

- [ ] **Kehadiran / Presensi**: Presensi siswa harian dan presensi guru.
- [ ] **Penilaian & Rapor (Akademik)**: Manajemen nilai tugas, ujian, dan pencetakan rapor siswa.

## Langkah 4: Modul Keuangan (Prioritas Saat Ini)
Modul untuk mengatur tarif, tagihan massal, kasir, hingga riwayat.

- [x] **Master Jenis Pembayaran & Setup Tarif Siswa**
- [x] **Generate (Terbitkan) Tagihan Massal**
- [x] **Laporan Daftar Tagihan & Auto-Adjustment Nominal**
- [x] **Kasir Pembayaran & Sistem Deposit (Tabungan Siswa)**
- [x] **Menu Riwayat Transaksi (Arsip Kuitansi)**: Daftar riwayat kuitansi untuk keperluan cetak ulang dan batal (void).
- [x] **Pengeluaran Kas Operasional**: Pencatatan beban biaya, ATK, pemeliharaan, kategori beban, dan unggah bukti nota fisik.
- [x] **Buku Kas Umum (Laporan Arus Kas Komprehensif)**: Integrasi kas masuk (kasir) dan kas keluar (pengeluaran), saldo kas berjalan, rekapitulasi harian, dan format jurnal akuntansi siap cetak resmi.
- [x] **Broadcast Tagihan WhatsApp (Wapify API + Manual WA Web)**: Pengiriman tagihan massal otomatis dengan mesin anti-banned (jitter delay, spintax) serta opsi kirim manual 1-klik tanpa API.
- [x] **Portal SPP Publik Mandiri (`/spp`)**: Akses orang tua berbasis NIS dengan tabel 12 bulan (Januari–Desember), filter 3 tahun, status lunas/belum lunas, rekening transfer 1-klik salin, dan konfirmasi WhatsApp.
- [x] **Pengecekan Status SPP Guru & Staf (`/keuangan/status-spp`)**: Pengecekan instan keamanan pembayaran anak ("Aman vs Belum Aman"), integrasi tombol bayar kasir dan kirim status WA ke ortu.

## Langkah 5: Dashboard & Pengaturan Sistem Global
- [x] **Dashboard Home (Beranda)**: Menampilkan metrik utama sekolah (grafik, statistik pendapatan, dll) yang elegan.
- [x] **Konfigurasi Sistem Dinamis (`/konfigurasi`)**: Profil sekolah, rekening bank, gateway WhatsApp Wapify, template pengingat, dan runtime query caching.

## Langkah 6: Rencana Pengembangan Masa Depan (Backlog / Future Enhancements)
Daftar rencana fitur tambahan untuk dikerjakan pada tahap berikutnya:

### 1. Penerimaan Peserta Didik Baru (PPDB Online / Digital)
- [ ] **Formulir Pendaftaran Calon Siswa Mandiri**: Form publik di website sekolah untuk orang tua mendaftarkan ananda secara online.
- [ ] **Unggah Berkas Persyaratan**: Fitur upload dokumen (Akta Kelahiran, Kartu Keluarga, KTP Ortu, Foto).
- [ ] **Panel Verifikasi Berkas & Seleksi Admin**: Tata Usaha dapat memverifikasi berkas, menjadwalkan observasi, dan menentukan status kelulusan (Diterima / Menunggu / Ditolak).
- [ ] **Konversi Otomatis ke Siswa Aktif**: Saat calon siswa dinyatakan diterima dan menyelesaikan administrasi, data otomatis ditransfer ke tabel `siswa`.

### 2. Pencadangan Basis Data & Keamanan Otomatis (Backup & Maintenance)
- [ ] **Pencadangan Database Otomatis (Backup Scheduler)**: Fitur backup basis data berkala (harian/mingguan) via cron job / spark command.
- [ ] **Download / Restore Backup**: Tombol unduh cadangan SQL langsung dari menu Pengaturan Sistem.
- [ ] **Audit Trail / Activity Log**: Pencatatan riwayat aktivitas pengguna penting (siapa mengubah tagihan, siapa menghapus data).

### 3. Modul Akademik Lanjutan (Presensi & Rapor)
- [ ] **Kehadiran / Presensi Harian**: Pencatatan absensi siswa harian per rombel serta presensi kehadiran guru/staf.
- [ ] **Notifikasi Absensi ke Wali Murid**: Pesan WhatsApp otomatis jika siswa tidak hadir / terlambat.
- [ ] **Penilaian & E-Rapor**: Manajemen nilai capaian perkembangan anak usia dini / Kurikulum Merdeka dan cetak buku rapor PDF.
