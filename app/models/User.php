<?php

final class User
{
    public static function all(): array
    {
        $users = Storage::all('users');
        usort($users, fn (array $a, array $b): int => strcasecmp($a['name'], $b['name']));

        return $users;
    }

    public static function find(int $id): ?array
    {
        return Storage::find('users', $id);
    }

    public static function findByEmail(string $email): ?array
    {
        foreach (Storage::all('users') as $user) {
            if (strcasecmp($user['email'], trim($email)) === 0) {
                return $user;
            }
        }

        return null;
    }

    public static function create(array $data): int
    {
        self::ensureUniqueEmail(trim($data['email'] ?? ''));

        return Storage::insert('users', self::payload($data, true));
    }

    public static function update(int $id, array $data): void
    {
        self::ensureUniqueEmail(trim($data['email'] ?? ''), $id);
        $payload = self::payload($data, false);

        if (trim($data['password'] ?? '') === '') {
            unset($payload['password_hash']);
        }

        Storage::update('users', $id, $payload);
    }

    public static function delete(int $id): bool
    {
        $user = self::find($id);
        if ($user === null || Auth::id() === $id) {
            return false;
        }

        if (($user['role'] ?? '') === 'admin' && self::adminCount() <= 1) {
            return false;
        }

        return Storage::delete('users', $id);
    }

    public static function adminCount(): int
    {
        return count(array_filter(
            Storage::all('users'),
            fn (array $user): bool => ($user['role'] ?? '') === 'admin' && ($user['active'] ?? false)
        ));
    }

    private static function ensureUniqueEmail(string $email, ?int $ignoreId = null): void
    {
        foreach (Storage::all('users') as $user) {
            if (strcasecmp($user['email'], $email) === 0 && (int)$user['id'] !== (int)$ignoreId) {
                throw new RuntimeException('Duplicated user email.');
            }
        }
    }

    private static function payload(array $data, bool $passwordRequired): array
    {
        $role = in_array($data['role'] ?? 'user', ['admin', 'user'], true) ? $data['role'] : 'user';
        $payload = [
            'name' => trim($data['name'] ?? ''),
            'email' => trim($data['email'] ?? ''),
            'role' => $role,
            'active' => (string)($data['active'] ?? '0') === '1',
        ];

        if ($passwordRequired || trim($data['password'] ?? '') !== '') {
            $payload['password_hash'] = password_hash((string)$data['password'], PASSWORD_DEFAULT);
        }

        return $payload;
    }
}
