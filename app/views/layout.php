<?php
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$currentUser = Auth::user();

function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function route_url(string $route): string
{
    [$path, $query] = array_pad(explode('&', $route, 2), 2, '');

    return '?route=' . rawurlencode($path) . ($query !== '' ? '&' . $query : '');
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($appName) ?></title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
    <aside class="sidebar">
        <div class="brand">
            <span class="brand-mark">VM</span>
            <strong><?= e($appName) ?></strong>
        </div>
        <nav>
            <a href="<?= route_url('/') ?>">Dashboard</a>
            <a href="<?= route_url('/companies') ?>">Empresas</a>
            <a href="<?= route_url('/servers') ?>">Servidores fisicos</a>
            <a href="<?= route_url('/vms') ?>">VMs</a>
            <?php if (Auth::isAdmin()): ?>
                <a href="<?= route_url('/users') ?>">Usuarios</a>
            <?php endif; ?>
        </nav>
        <div class="user-box">
            <span><?= e($currentUser['name'] ?? '') ?></span>
            <form method="post" action="<?= route_url('/logout') ?>">
                <button type="submit">Sair</button>
            </form>
        </div>
    </aside>

    <main class="main">
        <?php if ($flash): ?>
            <div class="flash <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>

        <?php require $contentView; ?>
    </main>
</body>
</html>
