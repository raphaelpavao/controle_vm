<?php

final class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $method, string $path): void
    {
        $path = '/' . trim($path, '/');
        $path = $path === '//' ? '/' : $path;

        if (!Auth::check() && !in_array($path, ['/login', '/authenticate'], true)) {
            header('Location: ?route=' . rawurlencode('/login'));
            exit;
        }

        if (Auth::check() && $path === '/login') {
            header('Location: ?route=' . rawurlencode('/'));
            exit;
        }

        if (str_starts_with($path, '/users') && !Auth::isAdmin()) {
            http_response_code(403);
            (new DashboardController())->forbidden();
            return;
        }

        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            http_response_code(404);
            (new DashboardController())->notFound();
            return;
        }

        [$controllerClass, $action] = $handler;
        (new $controllerClass())->$action();
    }
}
