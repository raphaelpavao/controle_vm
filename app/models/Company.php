<?php

final class Company
{
    public static function all(): array
    {
        $companies = Storage::all('companies');
        $servers = Storage::all('physical_servers');
        $vms = Storage::all('virtual_machines');

        foreach ($companies as &$company) {
            $company['server_count'] = count(array_filter(
                $servers,
                fn (array $server): bool => (int)($server['company_id'] ?? 0) === (int)$company['id']
            ));
            $company['vm_count'] = count(array_filter(
                $vms,
                fn (array $vm): bool => (int)($vm['company_id'] ?? 0) === (int)$company['id']
            ));
        }

        usort($companies, fn (array $a, array $b): int => strcasecmp($a['name'], $b['name']));

        return $companies;
    }

    public static function find(int $id): ?array
    {
        return Storage::find('companies', $id);
    }

    public static function create(array $data): int
    {
        self::ensureUniqueDocument(trim($data['document'] ?? ''));

        return Storage::insert('companies', self::payload($data));
    }

    public static function update(int $id, array $data): void
    {
        self::ensureUniqueDocument(trim($data['document'] ?? ''), $id);
        Storage::update('companies', $id, self::payload($data));
    }

    public static function delete(int $id): bool
    {
        foreach (Storage::all('physical_servers') as $server) {
            if ((int)($server['company_id'] ?? 0) === $id) {
                return false;
            }
        }

        foreach (Storage::all('virtual_machines') as $vm) {
            if ((int)($vm['company_id'] ?? 0) === $id) {
                return false;
            }
        }

        return Storage::delete('companies', $id);
    }

    public static function label(?int $id): string
    {
        if (!$id) {
            return 'Sem empresa';
        }

        $company = self::find($id);

        return $company['name'] ?? 'Empresa removida';
    }

    private static function ensureUniqueDocument(string $document, ?int $ignoreId = null): void
    {
        if ($document === '') {
            return;
        }

        foreach (Storage::all('companies') as $company) {
            if (($company['document'] ?? '') === $document && (int)$company['id'] !== (int)$ignoreId) {
                throw new RuntimeException('Duplicated company document.');
            }
        }
    }

    private static function payload(array $data): array
    {
        return [
            'name' => trim($data['name'] ?? ''),
            'document' => trim($data['document'] ?? ''),
            'contact_name' => trim($data['contact_name'] ?? ''),
            'contact_email' => trim($data['contact_email'] ?? ''),
            'phone' => trim($data['phone'] ?? ''),
            'notes' => trim($data['notes'] ?? ''),
        ];
    }
}
