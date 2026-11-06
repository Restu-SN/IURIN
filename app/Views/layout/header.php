<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#000000">
    <title>Iurin &middot; <?= esc($klub) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=JetBrains+Mono:wght@400;500;700&family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <?php /* CSS ditanam langsung supaya tidak tertahan sistem keamanan hosting gratis */ ?>
    <style><?= file_get_contents(FCPATH . 'assets/iurin.css') ?></style>
</head>
<body>
<header class="masthead">
    <div class="ticker" aria-hidden="true">
        <div class="ticker-track">
            <?php for ($i = 0; $i < 8; $i++): ?>
                <span>Kalo ga bayar gua smash nih</span><span class="sep">&#127992;</span>
                <span><?= esc($klub) ?></span><span class="sep">&#127992;</span>
            <?php endfor ?>
        </div>
    </div>
    <div class="mast-inner">
        <a class="wordmark" href="<?= site_url('anggota') ?>">
            <b>IURIN</b>
            <small><?= esc($klub) ?><br>Kas &amp; iuran main</small>
        </a>
        <div class="mast-user dropdown">
            <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-label="Akun">
                <i class="bi bi-person-square"></i>
                <span class="who"><?= esc($pengguna['nama']) ?></span>
                <i class="bi bi-chevron-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><span class="dropdown-item-text small text-secondary"><?= $isAdmin ? 'Admin' : 'Anggota (hanya lihat)' ?></span></li>
                <li><a class="dropdown-item" href="<?= site_url('password') ?>"><i class="bi bi-key me-2"></i>Ganti password</a></li>
                <li><a class="dropdown-item" href="<?= site_url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
            </ul>
        </div>
    </div>
    <nav class="tabs" aria-label="Menu utama">
        <a class="<?= $menu === 'anggota' ? 'active' : '' ?>" href="<?= site_url('anggota') ?>"><b>01</b>Anggota</a>
        <a class="<?= $menu === 'pembayaran' ? 'active' : '' ?>" href="<?= site_url('pembayaran') ?>"><b>02</b>Catat Main</a>
        <a class="<?= $menu === 'status' ? 'active' : '' ?>" href="<?= site_url('status') ?>"><b>03</b>Riwayat</a>
        <a class="<?= $menu === 'kas' ? 'active' : '' ?>" href="<?= site_url('kas') ?>"><b>04</b>Rekap Kas</a>
    </nav>
</header>

<main class="content">
    <?php if (session()->getFlashdata('flash')): ?>
        <div class="alert alert-<?= esc(session()->getFlashdata('flashTipe') ?? 'success') ?> alert-dismissible fade show">
            <i class="bi <?= session()->getFlashdata('flashTipe') === 'success' ? 'bi-check-square-fill' : 'bi-exclamation-square' ?>"></i>
            <span><?= esc(session()->getFlashdata('flash')) ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif ?>
    <?php if (! empty($error)): ?>
        <div class="alert alert-danger"><i class="bi bi-exclamation-square-fill"></i> <?= esc($error) ?></div>
    <?php endif ?>
