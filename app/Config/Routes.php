<?php

use CodeIgniter\Router\RouteCollection;

// --------------------------------------------------------------------
// Website Profil Publik (Landing Page)
// --------------------------------------------------------------------
$routes->get('/', 'Web::index', ['namespace' => 'Modules\Web\Controllers']);
$routes->match(['GET', 'POST'], 'spp', 'Spp::index', ['namespace' => 'Modules\Web\Controllers']);
$routes->match(['GET', 'POST'], 'cek-tagihan', static function () {
    $q = service('request')->getUri()->getQuery();
    return redirect()->to(base_url('spp') . ($q ? '?' . $q : ''));
});

// --------------------------------------------------------------------
// SIS / Dashboard Internal
// --------------------------------------------------------------------
$routes->get('sis', 'Home::index', ['filter' => 'session']);
$routes->get('dashboard', 'Home::index', ['filter' => 'session']);

// --------------------------------------------------------------------
// User Management Module
// --------------------------------------------------------------------
$routes->group('users', ['namespace' => 'Modules\UserManagement\Controllers'], static function ($routes) {
    $routes->get('/', 'UserController::index');
    $routes->post('store', 'UserController::store');
    $routes->post('update', 'UserController::update');
    $routes->get('delete/(:num)', 'UserController::delete/$1');
});

// --------------------------------------------------------------------
// Master Data Module
// --------------------------------------------------------------------
$routes->group('master-data', ['namespace' => 'Modules\MasterData\Controllers', 'filter' => 'session'], static function ($routes) {
    // Tahun Ajaran
    $routes->get('tahun-ajaran', 'TahunAjaran::index');
    $routes->post('tahun-ajaran/save', 'TahunAjaran::save');
    $routes->get('tahun-ajaran/delete/(:num)', 'TahunAjaran::delete/$1');
    
    // Mata Pelajaran
    $routes->get('mata-pelajaran', 'MataPelajaran::index');
    $routes->post('mata-pelajaran/save', 'MataPelajaran::save');
    $routes->get('mata-pelajaran/delete/(:num)', 'MataPelajaran::delete/$1');
});

// --------------------------------------------------------------------
// Civitas Akademika Module
// --------------------------------------------------------------------
$routes->group('civitas-akademika', ['namespace' => 'Modules\CivitasAkademika\Controllers', 'filter' => 'session'], static function ($routes) {
    // Guru & Staf
    $routes->get('guru-staf', 'GuruStaf::index');
    $routes->post('guru-staf/save', 'GuruStaf::save');
    $routes->get('guru-staf/delete/(:num)', 'GuruStaf::delete/$1');
    $routes->get('guru-staf/profil/(:num)', 'GuruStaf::profil/$1');
    $routes->post('guru-staf/buat-tautkan-user', 'GuruStaf::buatDanTautkanUser');
    $routes->post('guru-staf/tautkan-user', 'GuruStaf::tautkanUser');
    $routes->post('guru-staf/reset-password', 'GuruStaf::resetPassword');
    $routes->get('guru-staf/hapus-tautan-user/(:num)', 'GuruStaf::hapusTautanUser/$1');
    
    // Siswa
    $routes->get('siswa', 'Siswa::index');
    $routes->post('siswa/save', 'Siswa::save');
    $routes->get('siswa/delete/(:num)', 'Siswa::delete/$1');
    $routes->get('siswa/profil/(:num)', 'Siswa::profil/$1');
    $routes->post('siswa/buat-tautkan-orang-tua', 'Siswa::buatDanTautkanOrangTua');
    $routes->post('siswa/tautkan-orang-tua', 'Siswa::tautkanOrangTua');
    $routes->post('siswa/reset-password-ortu', 'Siswa::resetPasswordOrtu');
    $routes->get('siswa/hapus-tautan-orang-tua/(:num)', 'Siswa::hapusTautanOrangTua/$1');
});

// --------------------------------------------------------------------
// Akademik Module
// --------------------------------------------------------------------
$routes->group('akademik', ['namespace' => 'Modules\Akademik\Controllers', 'filter' => 'session'], static function ($routes) {
    // Rombongan Belajar
    $routes->get('rombel', 'Rombel::index');
    $routes->post('rombel/save', 'Rombel::save');
    $routes->get('rombel/delete/(:num)', 'Rombel::delete/$1');
    
    // Anggota Rombel (Penempatan Kelas)
    $routes->get('anggota-rombel', 'AnggotaRombel::index');
    $routes->get('anggota-rombel/detail/(:num)', 'AnggotaRombel::detail/$1');
    $routes->post('anggota-rombel/add-siswa', 'AnggotaRombel::addSiswa');
    $routes->get('anggota-rombel/remove/(:num)', 'AnggotaRombel::removeSiswa/$1');
});

$routes->group('keuangan', ['namespace' => 'Modules\Keuangan\Controllers', 'filter' => 'session'], function($routes) {
    // Master Jenis Pembayaran
    $routes->get('jenis-pembayaran', 'JenisPembayaran::index');
    $routes->post('jenis-pembayaran/save', 'JenisPembayaran::save');
    $routes->get('jenis-pembayaran/delete/(:num)', 'JenisPembayaran::delete/$1');

    // Setup Tarif Siswa
    $routes->get('setup-tarif', 'SetupTarif::index');
    $routes->get('setup-tarif/kelas/(:num)', 'SetupTarif::kelas/$1');
    $routes->get('setup-tarif/detail/(:num)', 'SetupTarif::detail/$1');
    $routes->post('setup-tarif/save', 'SetupTarif::save');
    $routes->post('setup-tarif/bulk-save', 'SetupTarif::bulkSave');
    $routes->post('setup-tarif/update/(:num)', 'SetupTarif::update/$1');
    $routes->get('setup-tarif/delete/(:num)', 'SetupTarif::delete/$1');
    
    // Generate Tagihan Bulanan
    $routes->get('generate-tagihan', 'GenerateTagihan::index');
    $routes->post('generate-tagihan/process', 'GenerateTagihan::process');
    $routes->post('generate-tagihan/rollback', 'GenerateTagihan::rollback');
    
    // Daftar Tagihan (Laporan)
    $routes->get('daftar-tagihan', 'DaftarTagihan::index');
    $routes->post('daftar-tagihan/update-nominal', 'DaftarTagihan::updateNominal');
    
    // Broadcast Tagihan WhatsApp
    $routes->get('broadcast-tagihan', 'BroadcastTagihan::index');
    $routes->get('broadcast-tagihan/get-message', 'BroadcastTagihan::getMessageAjax');
    $routes->post('broadcast-tagihan/check-contact', 'BroadcastTagihan::checkContactAjax');
    $routes->post('broadcast-tagihan/send-reminder', 'BroadcastTagihan::sendReminderAjax');
    $routes->post('broadcast-tagihan/update-phone', 'BroadcastTagihan::updatePhoneAjax');
    
    // Status SPP Siswa (Pengecekan Aman / Belum Aman)
    $routes->get('status-spp', 'StatusSpp::index');
    $routes->get('status-spp/(:num)', 'StatusSpp::index/$1');

    // Kasir Pembayaran
    $routes->get('kasir', 'Kasir::index');
    $routes->get('kasir/searchAjax', 'Kasir::searchAjax');
    $routes->get('kasir/bayar/(:num)', 'Kasir::bayar/$1');
    $routes->post('kasir/process', 'Kasir::process');
    $routes->get('kasir/cetak/(:num)', 'Kasir::cetak/$1');
    $routes->get('kasir/batal/(:num)', 'Kasir::batal/$1');
    $routes->post('kasir/kirim-wa/(:num)', 'Kasir::kirimWa/$1');
    
    // Riwayat Transaksi
    $routes->get('riwayat-transaksi', 'RiwayatTransaksi::index');
    
    // Buku Kas
    $routes->get('buku-kas', 'BukuKas::index');

    // Pengeluaran Kas
    $routes->get('pengeluaran', 'PengeluaranKas::index');
    $routes->post('pengeluaran/save', 'PengeluaranKas::save');
    $routes->get('pengeluaran/delete/(:num)', 'PengeluaranKas::delete/$1');
    $routes->post('pengeluaran/kategori/save', 'PengeluaranKas::saveKategori');
    $routes->get('pengeluaran/kategori/delete/(:num)', 'PengeluaranKas::deleteKategori/$1');
});

// --------------------------------------------------------------------
// Konfigurasi Sistem Module
// --------------------------------------------------------------------
$routes->group('konfigurasi', ['namespace' => 'Modules\Konfigurasi\Controllers', 'filter' => 'session'], static function ($routes) {
    $routes->get('/', 'Konfigurasi::index');
    $routes->post('save', 'Konfigurasi::save');
    $routes->post('test-wapify-connection', 'Konfigurasi::testWapifyConnection');
    $routes->post('test-wapify-send', 'Konfigurasi::testWapifySend');
    $routes->post('test-xendit-connection', 'Konfigurasi::testXenditConnection');
});

// --------------------------------------------------------------------
// Wali Kelas Module (Portal Pendidik & Guru)
// --------------------------------------------------------------------
$routes->group('wali-kelas', ['namespace' => 'Modules\WaliKelas\Controllers', 'filter' => 'session'], static function ($routes) {
    $routes->get('/', 'WaliKelas::index');
    $routes->get('dashboard', 'WaliKelas::dashboard');
    $routes->get('kelas/(:num)', 'WaliKelas::kelas/$1');
    
    // Murid Binaan
    $routes->get('murid', 'WaliKelas::daftarMurid');
    $routes->get('murid/profil/(:num)', 'WaliKelas::profilMurid/$1');
    $routes->post('murid/update-kontak', 'WaliKelas::updateKontakMurid');
    
    // Detail & Input Jurnal Perkembangan Siswa Tertentu
    $routes->get('siswa/(:num)/(:num)', 'WaliKelas::siswa/$1/$2');
    
    // Manajemen Jurnal Perkembangan Kelas
    $routes->get('perkembangan', 'WaliKelas::perkembangan');
    $routes->post('save-perkembangan', 'WaliKelas::savePerkembangan');
    $routes->get('delete-perkembangan/(:num)', 'WaliKelas::deletePerkembangan/$1');
    
    // Profil Guru & Pengaturan Akun
    $routes->get('profil', 'WaliKelas::profil');
    $routes->post('update-profil', 'WaliKelas::updateProfil');
    $routes->post('update-password', 'WaliKelas::updatePassword');
});

// --------------------------------------------------------------------
// Wali Murid Module (Portal Orang Tua)
// --------------------------------------------------------------------
$routes->group('wali-murid', ['namespace' => 'Modules\WaliMurid\Controllers', 'filter' => 'session'], static function ($routes) {
    $routes->get('/', 'WaliMurid::index');
    $routes->get('dashboard', 'WaliMurid::dashboard');
    $routes->get('perkembangan', 'WaliMurid::perkembangan');
    $routes->get('keuangan', 'WaliMurid::keuangan');
    $routes->post('bayar-xendit', 'WaliMurid::bayarXendit');
    $routes->get('pembayaran-sukses', 'WaliMurid::pembayaranSukses');
    $routes->get('pembayaran-batal', 'WaliMurid::pembayaranBatal');
    $routes->get('cctv', 'WaliMurid::cctv');
    $routes->get('profil', 'WaliMurid::profil');
    $routes->post('update-password', 'WaliMurid::updatePassword');
});

// --------------------------------------------------------------------
// Universal User Self-Profile Route
// --------------------------------------------------------------------
$routes->group('profil', ['namespace' => 'App\Controllers', 'filter' => 'session'], static function ($routes) {
    $routes->get('/', 'Profil::index');
    $routes->post('update-akun', 'Profil::updateAkun');
    $routes->post('update-password', 'Profil::updatePassword');
});

// --------------------------------------------------------------------
// Public Webhook API (Exempted from CSRF & Session Auth)
// --------------------------------------------------------------------
$routes->post('api/xendit/webhook', '\Modules\Keuangan\Controllers\XenditWebhook::index');

service('auth')->routes($routes);
