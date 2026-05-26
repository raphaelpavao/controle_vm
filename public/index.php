<?php

declare(strict_types=1);

session_start();

define('BASE_PATH', dirname(__DIR__));

$vendorAutoload = BASE_PATH . '/vendor/autoload.php';
if (is_file($vendorAutoload)) {
    require_once $vendorAutoload;
}

spl_autoload_register(function (string $className): void {
    $paths = [
        BASE_PATH . '/app/core/' . $className . '.php',
        BASE_PATH . '/app/storage/' . $className . '.php',
        BASE_PATH . '/app/controllers/' . $className . '.php',
        BASE_PATH . '/app/models/' . $className . '.php',
    ];

    foreach ($paths as $path) {
        if (is_file($path)) {
            require_once $path;
            return;
        }
    }
});

$config = require BASE_PATH . '/config/config.php';
Storage::init($config);

$router = new Router();
$router->get('/login', [LoginController::class, 'login']);
$router->post('/authenticate', [LoginController::class, 'authenticate']);
$router->post('/logout', [LoginController::class, 'logout']);

$router->get('/', [DashboardController::class, 'index']);

$router->get('/users', [UserController::class, 'index']);
$router->get('/users/create', [UserController::class, 'create']);
$router->post('/users/store', [UserController::class, 'store']);
$router->get('/users/edit', [UserController::class, 'edit']);
$router->post('/users/update', [UserController::class, 'update']);
$router->post('/users/delete', [UserController::class, 'delete']);

$router->get('/companies', [CompanyController::class, 'index']);
$router->get('/companies/create', [CompanyController::class, 'create']);
$router->post('/companies/store', [CompanyController::class, 'store']);
$router->get('/companies/show', [CompanyController::class, 'show']);
$router->get('/companies/edit', [CompanyController::class, 'edit']);
$router->post('/companies/update', [CompanyController::class, 'update']);
$router->post('/companies/delete', [CompanyController::class, 'delete']);

$router->get('/servers', [ServerController::class, 'index']);
$router->get('/servers/create', [ServerController::class, 'create']);
$router->post('/servers/store', [ServerController::class, 'store']);
$router->get('/servers/show', [ServerController::class, 'show']);
$router->get('/servers/edit', [ServerController::class, 'edit']);
$router->post('/servers/update', [ServerController::class, 'update']);
$router->post('/servers/delete', [ServerController::class, 'delete']);

$router->get('/vms', [VmController::class, 'index']);
$router->get('/vms/create', [VmController::class, 'create']);
$router->post('/vms/store', [VmController::class, 'store']);
$router->get('/vms/show', [VmController::class, 'show']);
$router->get('/vms/edit', [VmController::class, 'edit']);
$router->post('/vms/update', [VmController::class, 'update']);
$router->post('/vms/delete', [VmController::class, 'delete']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_GET['route'] ?? '/');
