<?php

abstract class Controller
{
    protected function view(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);

        $config = require BASE_PATH . '/config/config.php';
        $appName = $config['app_name'];
        $contentView = BASE_PATH . '/app/views/' . $view . '.php';

        require BASE_PATH . '/app/views/layout.php';
    }

    protected function redirect(string $route): void
    {
        [$path, $query] = array_pad(explode('&', $route, 2), 2, '');

        header('Location: ?route=' . rawurlencode($path) . ($query !== '' ? '&' . $query : ''));
        exit;
    }

    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    protected function idFromRequest(): int
    {
        return max(0, (int)($_GET['id'] ?? $_POST['id'] ?? 0));
    }
}
