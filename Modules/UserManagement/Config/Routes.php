<?php

namespace Modules\UserManagement\Config;

$routes->group('users', ['namespace' => 'Modules\UserManagement\Controllers'], static function ($routes) {
    $routes->get('/', 'Users::index');
    // Nanti rute login/logout dari Shield bisa kita tempatkan atau gabungkan di sini
});
