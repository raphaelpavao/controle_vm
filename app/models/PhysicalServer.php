<?php

final class PhysicalServer
{
    public static function all(): array
    {
        $servers = Storage::all('physical_servers');
        $vms = Storage::all('virtual_machines');

        foreach ($servers as &$server) {
            $serverVms = array_filter($vms, fn (array $vm): bool => (int)$vm['physical_server_id'] === (int)$server['id']);
            $server['company_name'] = Company::label((int)($server['company_id'] ?? 0));
            $server['vm_count'] = count($serverVms);
            $server['used_vcpus'] = array_sum(array_column($serverVms, 'vcpus'));
            $server['used_ram_gb'] = array_sum(array_column($serverVms, 'ram_gb'));
            $server['used_disk_gb'] = array_sum(array_column($serverVms, 'disk_gb'));
        }

        usort($servers, fn (array $a, array $b): int => strcasecmp($a['name'], $b['name']));

        return $servers;
    }

    public static function find(int $id): ?array
    {
        $server = Storage::find('physical_servers', $id);
        if ($server === null) {
            return null;
        }

        $server['company_name'] = Company::label((int)($server['company_id'] ?? 0));

        return $server;
    }

    public static function create(array $data): int
    {
        self::ensureUniqueIp(trim($data['management_ip'] ?? ''));

        return Storage::insert('physical_servers', self::payload($data));
    }

    public static function update(int $id, array $data): void
    {
        self::ensureUniqueIp(trim($data['management_ip'] ?? ''), $id);
        Storage::update('physical_servers', $id, self::payload($data));
    }

    public static function delete(int $id): bool
    {
        foreach (Storage::all('virtual_machines') as $vm) {
            if ((int)$vm['physical_server_id'] === $id) {
                return false;
            }
        }

        return Storage::delete('physical_servers', $id);
    }

    public static function vms(int $serverId): array
    {
        $vms = array_filter(
            Storage::all('virtual_machines'),
            fn (array $vm): bool => (int)$vm['physical_server_id'] === $serverId
        );

        usort($vms, fn (array $a, array $b): int => strcasecmp($a['name'], $b['name']));

        return array_values($vms);
    }

    private static function ensureUniqueIp(string $ip, ?int $ignoreId = null): void
    {
        foreach (Storage::all('physical_servers') as $server) {
            if ($server['management_ip'] === $ip && (int)$server['id'] !== (int)$ignoreId) {
                throw new RuntimeException('Duplicated management IP.');
            }
        }
    }

    private static function payload(array $data): array
    {
        return [
            'name' => trim($data['name'] ?? ''),
            'company_id' => (int)($data['company_id'] ?? 0),
            'hostname' => trim($data['hostname'] ?? ''),
            'management_ip' => trim($data['management_ip'] ?? ''),
            'location' => trim($data['location'] ?? ''),
            'cpu_model' => trim($data['cpu_model'] ?? ''),
            'total_vcpus' => max(0, (int)($data['total_vcpus'] ?? 0)),
            'total_ram_gb' => max(0, (int)($data['total_ram_gb'] ?? 0)),
            'total_disk_gb' => max(0, (int)($data['total_disk_gb'] ?? 0)),
            'notes' => trim($data['notes'] ?? ''),
        ];
    }
}
