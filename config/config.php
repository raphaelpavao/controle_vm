<?php

return [
    'app_name' => 'Controle de VMs',
    'storage_driver' => getenv('APP_STORAGE') ?: 'json',
    'storage_path' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'controle_vm.json',
    'google_cloud_project' => getenv('GOOGLE_CLOUD_PROJECT') ?: null,
    'firestore_database' => getenv('FIRESTORE_DATABASE') ?: '(default)',
];
