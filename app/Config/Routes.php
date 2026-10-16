<?php

use CodeIgniter\Router\RouteCollection;

// --------------------------------------------------------------------
// Website Profil Publik (Landing Page)
// --------------------------------------------------------------------
$routes->get('/', 'Web::index', ['namespace' => 'Modules\Web\Controllers']);

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
    
    // Siswa
    $routes->get('siswa', 'Siswa::index');
    $routes->post('siswa/save', 'Siswa::save');
    $routes->get('siswa/delete/(:num)', 'Siswa::delete/$1');
    $routes->get('siswa/profil/(:num)', 'Siswa::profil/$1');
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
});

service('auth')->routes($routes);
