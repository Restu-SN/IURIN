<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('login', 'Auth::index');
$routes->post('login', 'Auth::login');
$routes->get('logout', 'Auth::logout');

// Semua halaman di bawah ini wajib login
$routes->group('', ['filter' => 'login'], static function (RouteCollection $routes) {
    $routes->get('/', 'Anggota::index');

    // Melihat: boleh untuk admin dan akun anggota
    $routes->get('anggota', 'Anggota::index');
    $routes->get('pembayaran', 'Pembayaran::index');
    $routes->get('status', 'Status::index');
    $routes->get('kas', 'Kas::index');
    $routes->get('kas/struk/(:segment)', 'Kas::struk/$1');
    $routes->get('password', 'Password::index');
    $routes->post('password', 'Password::simpan');

    // Mengubah data: hanya admin
    $routes->group('', ['filter' => 'admin'], static function (RouteCollection $routes) {
        $routes->get('anggota/tambah', 'Anggota::form');
        $routes->get('anggota/edit/(:num)', 'Anggota::form/$1');
        $routes->post('anggota/simpan', 'Anggota::simpan');
        $routes->post('anggota/hapus/(:num)', 'Anggota::hapus/$1');
        $routes->post('pembayaran', 'Pembayaran::simpan');
        $routes->post('pembayaran/pengeluaran', 'Pembayaran::pengeluaran');
        $routes->post('pembayaran/kas-lalu', 'Pembayaran::kasLalu');
        $routes->post('kas/struk/hapus/(:segment)', 'Kas::hapusStruk/$1');
    });
});
