<?php

final class VirtualMachine
{
    public static function all(): array
    {
        $vms = Storage::all('virtual_machines');

        foreach ($vms as &$vm) {
            $server = PhysicalServer::find((int)$vm['physical_server_id']);
            $vm['server_name'] = $server['name'] ?? 'Servidor removido';
            $vm['server_hostname'] = $server['hostname'] ?? '';
            $vm['company_name'] = Company::label((int)($vm['company_id'] ?? 0));
        }

        usort($vms, fn (array $a, array $b): int => strcasecmp($a['name'], $b['name']));

        return $vms;
    }

    public static function find(int $id): ?array
    {
        $vm = Storage::find('virtual_machines', $id);
        if ($vm === null) {
            return null;
        }

        $server = PhysicalServer::find((int)$vm['physical_server_id']);
        $vm['server_name'] = $server['name'] ?? 'Servidor removido';
        $vm['server_hostname'] = $server['hostname'] ?? '';
        $vm['company_name'] = Company::label((int)($vm['company_id'] ?? 0));

        return $vm;
    }

    public static function create(array $data): int
    {
        self::ensureUniqueIp(trim($data['ip_address'] ?? ''));

        return Storage::insert('virtual_machines', self::payload($data));
    }

    public static function update(int $id, array $data): void
    {
        self::ensureUniqueIp(trim($data['ip_address'] ?? ''), $id);
        Storage::update('virtual_machines', $id, self::payload($data));
    }

    public static function delete(int $id): void
    {
        Storage::delete('virtual_machines', $id);
    }

    public static function stats(): array
    {
        $vms = Storage::all('virtual_machines');

        return [
            'total' => count($vms),
            'vcpus' => array_sum(array_column($vms, 'vcpus')),
            'ram_gb' => array_sum(array_column($vms, 'ram_gb')),
            'disk_gb' => array_sum(array_column($vms, 'disk_gb')),
        ];
    }

    private static function ensureUniqueIp(string $ip, ?int $ignoreId = null): void
    {
        foreach (Storage::all('virtual_machines') as $vm) {
            if ($vm['ip_address'] === $ip && (int)$vm['id'] !== (int)$ignoreId) {
                throw new RuntimeException('Duplicated VM IP.');
            }
        }
    }

    private static function payload(array $data): array
    {
        return [
            'physical_server_id' => (int)($data['physical_server_id'] ?? 0),
            'company_id' => (int)($data['company_id'] ?? 0),
            'name' => trim($data['name'] ?? ''),
            'hostname' => trim($data['hostname'] ?? ''),
            'ip_address' => trim($data['ip_address'] ?? ''),
            'vcpus' => max(1, (int)($data['vcpus'] ?? 1)),
            'ram_gb' => max(1, (int)($data['ram_gb'] ?? 1)),
            'disk_gb' => max(1, (int)($data['disk_gb'] ?? 10)),
            'status' => in_array($data['status'] ?? 'ativa', ['ativa', 'desligada', 'manutencao', 'desativada'], true)
                ? $data['status']
                : 'ativa',
            'notes' => trim($data['notes'] ?? ''),
        ];
    }
}
