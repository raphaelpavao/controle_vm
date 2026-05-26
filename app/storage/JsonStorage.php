<?php

final class JsonStorage implements StorageDriver
{
    private string $path;
    private array $data = [];

    public function __construct(array $config)
    {
        $this->path = $config['storage_path'];
    }

    public function init(): void
    {
        $directory = dirname($this->path);

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        if (!is_file($this->path)) {
            $this->data = $this->emptyData();
            $this->persist();
            return;
        }

        $decoded = json_decode((string)file_get_contents($this->path), true);
        $this->data = is_array($decoded) ? array_replace_recursive($this->emptyData(), $decoded) : $this->emptyData();
        $this->migrate();
        $this->persist();
    }

    public function name(): string
    {
        return 'json';
    }

    public function all(string $table): array
    {
        return array_values($this->data[$table] ?? []);
    }

    public function find(string $table, int|string $id): ?array
    {
        foreach ($this->all($table) as $row) {
            if ((string)$row['id'] === (string)$id) {
                return $row;
            }
        }

        return null;
    }

    public function insert(string $table, array $row): int|string
    {
        $id = (int)$this->data['_meta'][$table . '_next_id'];
        $this->data['_meta'][$table . '_next_id'] = $id + 1;

        $now = date('Y-m-d H:i:s');
        $row = array_merge($row, [
            'id' => $id,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->data[$table][] = $row;
        $this->persist();

        return $id;
    }

    public function update(string $table, int|string $id, array $attributes): void
    {
        foreach ($this->data[$table] as $index => $row) {
            if ((string)$row['id'] === (string)$id) {
                $this->data[$table][$index] = array_merge($row, $attributes, [
                    'id' => $row['id'],
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $this->persist();
                return;
            }
        }
    }

    public function delete(string $table, int|string $id): bool
    {
        foreach ($this->data[$table] as $index => $row) {
            if ((string)$row['id'] === (string)$id) {
                array_splice($this->data[$table], $index, 1);
                $this->persist();
                return true;
            }
        }

        return false;
    }

    private function emptyData(): array
    {
        return [
            '_meta' => [
                'users_next_id' => 1,
                'companies_next_id' => 1,
                'physical_servers_next_id' => 1,
                'virtual_machines_next_id' => 1,
            ],
            'users' => [],
            'companies' => [],
            'physical_servers' => [],
            'virtual_machines' => [],
        ];
    }

    private function migrate(): void
    {
        foreach ($this->emptyData() as $key => $value) {
            if (!array_key_exists($key, $this->data)) {
                $this->data[$key] = $value;
            }
        }

        foreach ($this->emptyData()['_meta'] as $key => $value) {
            if (!array_key_exists($key, $this->data['_meta'])) {
                $this->data['_meta'][$key] = $value;
            }
        }
    }

    private function persist(): void
    {
        file_put_contents(
            $this->path,
            json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }
}
