<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

$vendorAutoload = BASE_PATH . '/vendor/autoload.php';
if (is_file($vendorAutoload)) {
    require_once $vendorAutoload;
}

require_once BASE_PATH . '/app/storage/StorageDriver.php';
require_once BASE_PATH . '/app/storage/FirestoreStorage.php';

$config = require BASE_PATH . '/config/config.php';
$jsonPath = $config['storage_path'];

if (!is_file($jsonPath)) {
    fwrite(STDERR, "Arquivo JSON nao encontrado: {$jsonPath}" . PHP_EOL);
    exit(1);
}

$data = json_decode((string)file_get_contents($jsonPath), true);
if (!is_array($data)) {
    fwrite(STDERR, "Arquivo JSON invalido." . PHP_EOL);
    exit(1);
}

$firestore = new FirestoreStorage(array_merge($config, ['storage_driver' => 'firestore']));
$firestore->init();

foreach (['users', 'companies', 'physical_servers', 'virtual_machines'] as $table) {
    foreach ($data[$table] ?? [] as $row) {
        if (!isset($row['id'])) {
            continue;
        }

        $firestore->put($table, $row['id'], $row);
    }
}

echo 'Migracao concluida.' . PHP_EOL;
