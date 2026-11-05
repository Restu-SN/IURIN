<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#000000">
    <title>Masuk &middot; Iurin &middot; <?= esc($klub) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=JetBrains+Mono:wght@400;500;700&family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <?php /* CSS ditanam langsung supaya tidak tertahan sistem keamanan hosting gratis */ ?>
    <style><?= file_get_contents(FCPATH . 'assets/iurin.css') ?></style>
</head>
<body class="auth-body">
<div class="auth">
    <svg class="kok" viewBox="0 0 300 220" fill="none" stroke="#fff" stroke-width="2.5" aria-hidden="true">
        <g transform="rotate(-8 150 110) translate(0 220) rotate(-90)">
            <ellipse cx="110" cy="38" rx="92" ry="18"/>
            <path d="M18 38 L72 212 M202 38 L148 212"/>
            <path d="M49 50 L86 212 M80 55 L99 212 M110 56 L110 212 M140 55 L121 212 M171 50 L134 212"/>
            <path d="M40 104 Q110 128 180 104 M58 162 Q110 180 162 162" stroke-width="2"/>
            <rect x="68" y="210" width="84" height="14" fill="#000"/>
            <path d="M68 224 Q68 280 110 280 Q152 280 152 224 Z" fill="#fff"/>
        </g>
    </svg>
    <img class="bg-photo" src="<?= base_url('assets/bg-login.jpg') ?>" alt="" onerror="this.remove()">

    <section class="auth-card">
        <div class="club"><?= esc($klub) ?></div>
        <h1>IURIN</h1>
        <?php if (! empty($error)): ?>
            <div class="auth-error"><i class="bi bi-exclamation-circle"></i> <?= esc($error) ?></div>
        <?php endif ?>
        <form method="post" action="<?= site_url('login') ?>">
            <?= csrf_field() ?>
            <input type="text" name="username" class="form-control mb-3" placeholder="Username" aria-label="Username"
                   required autofocus autocomplete="username" value="<?= esc($username ?? '') ?>">
            <input type="password" name="password" class="form-control mb-4" placeholder="Password" aria-label="Password"
                   required autocomplete="current-password">
            <button class="btn btn-masuk w-100">Masuk</button>
        </form>
    </section>
</div>
</body>
</html>
