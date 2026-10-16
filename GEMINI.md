# Panduan Pengembangan Sistem Informasi Sekolah (Antigravity Rules)

Ini adalah panduan utama (rules) yang **WAJIB** Anda (AI) baca dan ikuti di setiap sesi percakapan sebelum menulis kode apa pun untuk proyek Sistem Informasi Sekolah (SIS).

## 1. Perintah Wajib Saat Sesi Baru
Setiap kali sesi percakapan baru dimulai, Anda harus memastikan bahwa server dan *compiler* CSS berjalan di latar belakang (gunakan fitur `run_command` dengan `IsDaemon=true`):
- **Server CI4:** `php spark serve`
- **Tailwind Watcher:** `npm run dev:css` (Penting: harus selalu berjalan agar perubahan kelas CSS otomatis ter-build).

## 2. Struktur HMVC (Code Modules)
Sistem informasi ini menggunakan arsitektur **HMVC (Hierarchical Model-View-Controller)** yang diimplementasikan melalui *Code Modules* bawaan CodeIgniter 4.
Setiap fitur besar (seperti *Akademik*, *Keuangan*, *Kesiswaan*) WAJIB dipisahkan ke dalam foldernya masing-masing di dalam direktori root `Modules/`.

**Aturan Pembuatan Modul Baru:**
1. Buat folder di `Modules/NamaModul/` yang berisi susunan standar CI4 (Controllers, Models, Views, Config).
2. Modul baru **WAJIB didaftarkan (namespace)** ke dalam `app/Config/Autoload.php` pada *array* `$psr4`.
   Contoh: `'Modules\NamaModul' => ROOTPATH . 'Modules/NamaModul',`
3. Gunakan *namespace* secara disiplin di setiap *Controller* dan *Model* modul. (Misal: `namespace Modules\UserManagement\Controllers;`).
4. Gunakan namespace saat merender View dari dalam modul. (Contoh: `return view('Modules\UserManagement\Views\index');`).

## 3. Struktur View & Template (Wajib Diikuti)
Semua halaman/View baru harus di-*extend* dari `layout/main` dan harus mendefinisikan *section* secara spesifik (TIDAK BOLEH *hardcode* header di dalam content).

**Template View Standar:**
```php
<?= $this->extend('layout/main') ?>

<!-- 1. Breadcrumb -->
<?= $this->section('breadcrumb') ?>
<li><i class="ph ph-caret-right text-xs"></i></li>
<li><span class="text-gray-900 dark:text-slate-200 font-medium">Nama Modul</span></li>
<?= $this->endSection() ?>

<!-- 2. Judul Utama -->
<?= $this->section('page_title') ?>
Kelola Data X
<?= $this->endSection() ?>

<!-- 3. Sub Judul (Opsional) -->
<?= $this->section('page_subtitle') ?>
Penjelasan singkat mengenai fungsi halaman ini.
<?= $this->endSection() ?>

<!-- 4. Tombol Aksi Kanan Atas -->
<?= $this->section('page_actions') ?>
<button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors shadow-sm">
    Tambah Data X
</button>
<?= $this->endSection() ?>

<!-- 5. Konten Utama (Card/Table/Form) -->
<?= $this->section('content') ?>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
    <!-- Isi form atau tabel -->
</div>
<?= $this->endSection() ?>
```

## 3. Fitur UI/UX Global
Panggil fitur-fitur ini alih-alih membuat fungsi manual:

### Flash Messages (Notifikasi Melayang / Toast)
Sistem menggunakan `SweetAlert2` sebagai Toast otomatis di pojok kanan atas.
Di Controller, cukup panggil:
`return redirect()->to('/url')->with('success', 'Data berhasil disimpan!');`
(Mendukung key: `success`, `error`, `info`, `warning`).

### Konfirmasi Hapus / Aksi Berbahaya
JANGAN gunakan `alert()` atau `confirm()` standar peramban. Gunakan helper global JS `confirmAction()`:
```html
<button onclick="confirmAction('Yakin hapus?', 'Data X akan dihapus permanen!', 'Ya, Hapus', () => { window.location.href='/x/delete/1' })">
    Hapus
</button>
```

### CRUD Pop-Up (Modal) & Form UI
Operasi CRUD form diusahakan menggunakan Pop-up/Modal. 
Gunakan kerangka **Alpine.js** (`x-data="{ open: false }"`) untuk membuat modal yang reaktif langsung di dalam View. JANGAN arahkan ke halaman (*page*) form terpisah bila bisa diselesaikan di dalam satu halaman manajemen utama.

**Standar Desain Modal Premium (Wajib Diikuti):**
1. **Header Seamless:** Judul dan subjudul form dibuat menyatu dengan body form (latar belakang disamakan, contoh: `bg-white dark:bg-slate-800`), teks judul besar (`text-2xl font-bold`), tanpa border bawah.
2. **Input Modern:** Gunakan `bg-gray-50 dark:bg-slate-700/50` dengan `border-transparent` dan ujung membulat (`rounded-xl`). Berikan batas menyala saat *focus* (contoh: `focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10`).
3. **Ikon Input:** Tambahkan ikon *Phosphor* abu-abu secara *absolute* di dalam *input field* (`left-4`, dengan *padding left* pada input `pl-11`).
4. **Footer Seamless & Tombol:** Area tombol dibuat tanpa latar belakang yang kontras (hilangkan garis batas/latar belakang abu-abu pada modal standar). Tombol utama diberikan bayangan berpendar (`shadow-lg shadow-blue-500/30`).
5. **Animasi Loading:**
   - **Form Simpan/Ubah:** Gunakan Alpine.js `<form ... x-data="{ isSubmitting: false }" @submit="isSubmitting = true">`. Tambahkan `ph-spinner` berputar dan ubah teks tombol menjadi "Menyimpan..." saat diklik.
   - **Hapus Data:** Tombol *SweetAlert* otomatis menangani *loading spinner* ketika helper `confirmAction()` digunakan.

### Paginasi (Pagination)
CodeIgniter 4 Pager sudah diatur *default* menggunakan `App\Views\Pagers\tailwind`.
Cukup panggil `$pager->links()` di View, desainnya sudah akan mengikuti *Tailwind* secara otomatis.

## 4. Helper Kustom
Sudah ter-*autoload* secara global via `BaseController`. Langsung panggil di *View* atau *Controller*.
- **`rupiah($angka, bool $with_symbol = true)`** -> Menghasilkan `Rp 5.000.000`
- **`tanggal_indo($date, bool $print_day = false)`** -> Menghasilkan `17 Agustus 2026` atau `Senin, 17 Agustus 2026`. Juga otomatis menampilkan jam jika *string* waktu dikirimkan.
- **`app_setting($key, $default = null)`** (alias `konfigurasi($key, $default)`) -> Mengambil nilai dinamis dari tabel `pengaturan` (dengan runtime caching satu query per request). Contoh: `app_setting('school_name')`, `app_setting('school_logo')`.

### Gateway WhatsApp (Wapify Service)
Sistem memiliki pustaka `App\Libraries\WapifyService` untuk mengirim notifikasi WhatsApp otomatis melalui penyedia [Wapify.biz.id](https://wapify.biz.id/docs).
- Mengirim pesan teks, gambar + caption, dokumen PDF / slip kuitansi.
- Mendukung pengecekan status sesi dan validasi nomor aktif WhatsApp.

**Cara Pakai (Controller / Service):**
```php
$wapify = new \App\Libraries\WapifyService();
// Cek status sesi
$status = $wapify->checkSessions();

// Kirim pesan teks
$result = $wapify->sendMessage('08123456789', 'Halo! Ini bukti pembayaran sekolah.');
```

### Manajemen Unggahan Gambar (Image Uploader)
Sistem memiliki pustaka khusus untuk unggah gambar: `App\Libraries\ImageUploader`.
- Mengubah format gambar ke WebP (hemat ruang).
- Otomatis mengubah ukuran (*resize*) ke berbagai varian (misal: `thumb`, `md`, `lg`).
- Memiliki fitur pemotongan menjadi persegi panjang (*square crop*) secara otomatis di *server*.

**Cara Pakai (Controller):**
```php
$uploader = new \App\Libraries\ImageUploader('uploads/profiles/');
// Meminta gambar di-crop jadi kotak, dan dikompres ke 2 ukuran
$result = $uploader->process($this->request->getFile('image'), true, ['thumb' => 150, 'md' => 500]);
if ($result['status'] === 'success') {
    $namaWebp = $result['files']['original'];
}
```

**Cara Pakai (Frontend / AJAX):**
Telah disediakan helper JS global `uploadFileAjax(file, url, csrfToken, fieldName, onProgress, onSuccess, onError)` untuk mengunggah berkas menggunakan bilah kemajuan (*progress bar*) tanpa memuat ulang halaman (*reload*).

### Manajemen Unggahan Dokumen (Document Uploader)
Sistem juga memiliki pustaka khusus untuk unggah dokumen: `App\Libraries\DocumentUploader`.
- Mengunci filter hanya untuk format aman (PDF, Word, Excel).
- Otomatis menggunakan `getRandomName()` untuk mencegah bentrok dan eksploitasi.

**Cara Pakai (Controller):**
```php
$docUploader = new \App\Libraries\DocumentUploader('uploads/documents/');
$result = $docUploader->process($this->request->getFile('dokumen'));
if ($result['status'] === 'success') {
    $namaFile = $result['filename']; // Nama unik yang disimpan
    $namaAsli = $result['original'];
}
```

### Cetak Dokumen PDF (PDF Generator)
Sistem memiliki pustaka `App\Libraries\PdfGenerator` yang membungkus *Dompdf*. Sangat berguna untuk mencetak Slip Gaji, Invoice, atau Laporan.

**Cara Pakai (Controller):**
```php
$pdf = new \App\Libraries\PdfGenerator();
$data = ['nama' => 'Budi', 'gaji' => 5000000];
// Render HTML dari view (pastikan view berisi sintaks HTML/CSS untuk kertas)
$html = view('modul/slip_gaji_pdf', $data);

// Parameter: HTML, Nama File, Ukuran Kertas, Orientasi, Stream (true = langsung tampil di browser)
$pdf->generate($html, 'Slip_Gaji_Budi', 'A4', 'portrait', true);
```

## 5. Website Profil Publik vs SIS Portal (Arsitektur Pemisahan)
Aplikasi ini memisahkan antara Website Profil Publik (marketing & pendaftaran) dan Portal SIS (sistem operasional sekolah):
- **Website Profil Publik (`Modules/Web`):**
  - Route: `/` (dikelola oleh `Modules\Web\Controllers\Web::index`).
  - Layout: `app/Views/layout/web.php`.
  - Aset Statis: `public/web/` (`css/style.css`, `js/app.js`, `img/logo.png`).
  - UI Helper: `Fitur` (tersedia secara global via `app/Helpers/fitur_helper.php` untuk merender button, carousel, badge, accordion, dialog, dll).
  - Route `/` dikecualikan dari filter otentikasi session pada `app/Config/Filters.php`.
- **Portal SIS (Sistem Informasi Sekolah):**
  - Route: `/sis` atau `/dashboard`.
  - Layout: `app/Views/layout/main.php` (Tailwind `public/css/app.css`, Alpine.js, Dark Mode, Sidebar HMVC).
  - Terproteksi oleh filter otentikasi Shield session.
  - Navbar Admin memiliki tombol pintas "Website Sekolah" menuju ke `/`.

## 6. Estetika (Wajib)
Selalu gunakan *Dark Mode Support* dengan prefix `dark:`. Gunakan warna-warna premium (*slate*, *emerald*, *blue*) dan perhatikan kerapian *padding*, *margin*, dan sudut `rounded-lg` atau `rounded-xl`. Gunakan ikon *Phosphor* (`ph ph-nama-ikon`) bukan *FontAwesome*.

## 7. Evolusi Aturan (Self-Updating)
Jika dalam proses pengerjaan pembuatan modul atau *coding* kita menemukan pola, konvensi (*ruleset*), alat, atau *helper* baru yang terbukti bekerja dengan baik namun **belum** terdaftar di panduan ini, Anda (AI) WAJIB mendaftarkannya secara otomatis ke dalam dokumen `GEMINI.md` ini agar panduan tetap *up-to-date*.

## 8. Aturan Git Commit
- Lakukan Git Commit sesuai dengan *Best Practice* (menggunakan pesan konvensi yang jelas seperti `fitur:`, `perbaikan:`, `refaktor:`).
- Gunakan **Bahasa Indonesia** untuk seluruh pesan *commit*.
- **PENTING:** JANGAN pernah melakukan proses *commit* secara otomatis sebelum ada instruksi/perintah eksplisit dari User. Kita menghindari sejarah *commit* yang terlalu kotor/banyak. Kumpulkan saja kodenya, dan hanya lakukan *commit* saat User bilang *"Silakan commit"*.
