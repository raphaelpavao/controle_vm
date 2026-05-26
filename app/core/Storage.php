<?php

final class Storage
{
    private static StorageDriver $driver;

    public static function init(array|string $config): void
    {
        if (is_string($config)) {
            $config = [
                'storage_driver' => 'json',
                'storage_path' => $config,
            ];
        }

        self::$driver = match ($config['storage_driver'] ?? 'json') {
            'firestore' => new FirestoreStorage($config),
            default => new JsonStorage($config),
        };

        self::$driver->init();
        self::seedAdminUser();
    }

    public static function all(string $table): array
    {
        return self::$driver->all($table);
    }

    public static function find(string $table, int|string $id): ?array
    {
        return self::$driver->find($table, $id);
    }

    public static function insert(string $table, array $row): int|string
    {
        return self::$driver->insert($table, $row);
    }

    public static function update(string $table, int|string $id, array $attributes): void
    {
        self::$driver->update($table, $id, $attributes);
    }

    public static function delete(string $table, int|string $id): bool
    {
        return self::$driver->delete($table, $id);
    }

    public static function driverName(): string
    {
        return self::$driver->name();
    }

    private static function seedAdminUser(): void
    {
        if (self::$driver->all('users') !== []) {
            return;
        }

        $email = getenv('ADMIN_DEFAULT_EMAIL') ?: 'admin@local';
        $password = getenv('ADMIN_DEFAULT_PASSWORD') ?: bin2hex(random_bytes(12));

        self::$driver->insert('users', [
            'name' => 'Administrador',
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'admin',
            'active' => true,
        ]);
    }
}
