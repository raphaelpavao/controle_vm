<?php

final class FirestoreStorage implements StorageDriver
{
    private array $config;
    private mixed $database = null;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function init(): void
    {
        if (!class_exists('\\Google\\Cloud\\Firestore\\FirestoreClient')) {
            throw new RuntimeException(
                'Firestore driver requires the google/cloud-firestore Composer package.'
            );
        }

        $options = [];

        if (!empty($this->config['google_cloud_project'])) {
            $options['projectId'] = $this->config['google_cloud_project'];
        }

        if (!empty($this->config['firestore_database'])) {
            $options['database'] = $this->config['firestore_database'];
        }

        $clientClass = '\\Google\\Cloud\\Firestore\\FirestoreClient';
        $this->database = new $clientClass($options);
    }

    public function name(): string
    {
        return 'firestore';
    }

    public function all(string $table): array
    {
        $rows = [];

        foreach ($this->database->collection($table)->documents() as $document) {
            if (!$document->exists()) {
                continue;
            }

            $rows[] = $this->normalizeRow($document->data(), $document->id());
        }

        return $rows;
    }

    public function find(string $table, int|string $id): ?array
    {
        $document = $this->database->collection($table)->document((string)$id)->snapshot();

        if (!$document->exists()) {
            return null;
        }

        return $this->normalizeRow($document->data(), $document->id());
    }

    public function insert(string $table, array $row): int|string
    {
        $id = $this->nextId($table);
        $now = date('Y-m-d H:i:s');
        $row = array_merge($row, [
            'id' => $id,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->database->collection($table)->document((string)$id)->set($row);

        return $id;
    }

    public function update(string $table, int|string $id, array $attributes): void
    {
        $current = $this->find($table, $id);

        if ($current === null) {
            return;
        }

        $row = array_merge($current, $attributes, [
            'id' => $current['id'],
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->database->collection($table)->document((string)$id)->set($row);
    }

    public function delete(string $table, int|string $id): bool
    {
        if ($this->find($table, $id) === null) {
            return false;
        }

        $this->database->collection($table)->document((string)$id)->delete();

        return true;
    }

    public function put(string $table, int|string $id, array $row): void
    {
        $now = date('Y-m-d H:i:s');
        $row = array_merge($row, [
            'id' => is_numeric($id) ? (int)$id : $id,
            'updated_at' => $row['updated_at'] ?? $now,
        ]);

        if (!isset($row['created_at'])) {
            $row['created_at'] = $now;
        }

        $this->database->collection($table)->document((string)$id)->set($row);
        $this->bumpCounter($table, $id);
    }

    private function nextId(string $table): int
    {
        $counterRef = $this->database->collection('_meta')->document('counters');
        $snapshot = $counterRef->snapshot();
        $counters = $snapshot->exists() ? $snapshot->data() : [];
        $key = $table . '_next_id';

        if (!isset($counters[$key])) {
            $ids = array_map(fn (array $row): int => (int)$row['id'], $this->all($table));
            $counters[$key] = $ids === [] ? 1 : max($ids) + 1;
        }

        $id = (int)$counters[$key];
        $counters[$key] = $id + 1;
        $counterRef->set($counters);

        return $id;
    }

    private function bumpCounter(string $table, int|string $id): void
    {
        if (!is_numeric($id)) {
            return;
        }

        $counterRef = $this->database->collection('_meta')->document('counters');
        $snapshot = $counterRef->snapshot();
        $counters = $snapshot->exists() ? $snapshot->data() : [];
        $key = $table . '_next_id';
        $next = (int)$id + 1;

        if (!isset($counters[$key]) || (int)$counters[$key] < $next) {
            $counters[$key] = $next;
            $counterRef->set($counters);
        }
    }

    private function normalizeRow(array $row, string $documentId): array
    {
        if (!isset($row['id'])) {
            $row['id'] = is_numeric($documentId) ? (int)$documentId : $documentId;
        }

        if (is_numeric($row['id'])) {
            $row['id'] = (int)$row['id'];
        }

        return $row;
    }
}
