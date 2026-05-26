<?php
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
    <title><?= e($appName) ?> - Login</title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body class="auth-page">
    <?php require $contentView; ?>
</body>
</html>
