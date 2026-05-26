<?php

final class FirestoreStorage implements StorageDriver
{
    private string $projectId;
    private string $database;
    private string $baseUrl;

    public function __construct(array $config)
    {
        $this->projectId = (string)($config['google_cloud_project'] ?? '');
        $this->database = (string)($config['firestore_database'] ?? '(default)');
    }

    public function init(): void
    {
        if ($this->projectId === '') {
            throw new RuntimeException('GOOGLE_CLOUD_PROJECT is required for Firestore storage.');
        }

        $database = rawurlencode($this->database);
        $this->baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/{$database}/documents";
    }

    public function name(): string
    {
        return 'firestore';
    }

    public function all(string $table): array
    {
        $rows = [];
        $pageToken = null;

        do {
            $url = $this->baseUrl . '/' . rawurlencode($table);
            if ($pageToken !== null) {
                $url .= '?pageToken=' . rawurlencode($pageToken);
            }

            $response = $this->request('GET', $url);

            foreach ($response['documents'] ?? [] as $document) {
                $id = basename($document['name']);
                $rows[] = $this->decodeDocument($document, $id);
            }

            $pageToken = $response['nextPageToken'] ?? null;
        } while ($pageToken !== null);

        return $rows;
    }

    public function find(string $table, int|string $id): ?array
    {
        try {
            $document = $this->request('GET', $this->documentUrl($table, $id));
        } catch (RuntimeException $exception) {
            if (str_contains($exception->getMessage(), 'HTTP 404')) {
                return null;
            }

            throw $exception;
        }

        return $this->decodeDocument($document, (string)$id);
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

        $this->put($table, $id, $row);

        return $id;
    }

    public function update(string $table, int|string $id, array $attributes): void
    {
        $current = $this->find($table, $id);

        if ($current === null) {
            return;
        }

        $this->put($table, $id, array_merge($current, $attributes, [
            'id' => $current['id'],
            'updated_at' => date('Y-m-d H:i:s'),
        ]));
    }

    public function delete(string $table, int|string $id): bool
    {
        if ($this->find($table, $id) === null) {
            return false;
        }

        $this->request('DELETE', $this->documentUrl($table, $id));

        return true;
    }

    public function put(string $table, int|string $id, array $row): void
    {
        $now = date('Y-m-d H:i:s');
        $row = array_merge($row, [
            'id' => is_numeric($id) ? (int)$id : $id,
            'created_at' => $row['created_at'] ?? $now,
            'updated_at' => $row['updated_at'] ?? $now,
        ]);

        $this->request('PATCH', $this->documentUrl($table, $id), [
            'fields' => $this->encodeFields($row),
        ]);
        $this->bumpCounter($table, $id);
    }

    private function nextId(string $table): int
    {
        $counters = $this->find('_meta', 'counters') ?? ['id' => 'counters'];
        $key = $table . '_next_id';

        if (!isset($counters[$key])) {
            $ids = array_map(fn (array $row): int => (int)$row['id'], $this->all($table));
            $counters[$key] = $ids === [] ? 1 : max($ids) + 1;
        }

        $id = (int)$counters[$key];
        $counters[$key] = $id + 1;
        $this->put('_meta', 'counters', $counters);

        return $id;
    }

    private function bumpCounter(string $table, int|string $id): void
    {
        if ($table === '_meta' || !is_numeric($id)) {
            return;
        }

        $counters = $this->find('_meta', 'counters') ?? ['id' => 'counters'];
        $key = $table . '_next_id';
        $next = (int)$id + 1;

        if (!isset($counters[$key]) || (int)$counters[$key] < $next) {
            $counters[$key] = $next;
            $this->put('_meta', 'counters', $counters);
        }
    }

    private function documentUrl(string $table, int|string $id): string
    {
        return $this->baseUrl . '/' . rawurlencode($table) . '/' . rawurlencode((string)$id);
    }

    private function request(string $method, string $url, ?array $payload = null): array
    {
        $headers = [
            'Authorization: Bearer ' . $this->accessToken(),
            'Content-Type: application/json',
        ];

        $context = stream_context_create([
            'http' => [
                'method' => $method,
                'header' => implode("\r\n", $headers),
                'content' => $payload === null ? '' : json_encode($payload, JSON_UNESCAPED_SLASHES),
                'ignore_errors' => true,
            ],
        ]);

        $raw = file_get_contents($url, false, $context);
        $statusLine = $http_response_header[0] ?? 'HTTP/1.1 500';

        if (!preg_match('/\s(\d{3})\s/', $statusLine, $matches)) {
            throw new RuntimeException('Firestore request failed without HTTP status.');
        }

        $status = (int)$matches[1];
        $decoded = $raw === '' || $raw === false ? [] : json_decode((string)$raw, true);

        if ($status < 200 || $status >= 300) {
            $message = is_array($decoded) ? ($decoded['error']['message'] ?? (string)$raw) : (string)$raw;
            throw new RuntimeException("Firestore HTTP {$status}: {$message}");
        }

        return is_array($decoded) ? $decoded : [];
    }

    private function accessToken(): string
    {
        $token = getenv('FIRESTORE_ACCESS_TOKEN') ?: getenv('GOOGLE_OAUTH_ACCESS_TOKEN');
        if ($token !== false && $token !== '') {
            return $token;
        }

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "Metadata-Flavor: Google\r\n",
                'timeout' => 5,
            ],
        ]);

        $raw = @file_get_contents(
            'http://metadata.google.internal/computeMetadata/v1/instance/service-accounts/default/token',
            false,
            $context
        );

        if ($raw === false) {
            throw new RuntimeException('Could not get Google metadata access token.');
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded) || empty($decoded['access_token'])) {
            throw new RuntimeException('Invalid Google metadata access token response.');
        }

        return $decoded['access_token'];
    }

    private function decodeDocument(array $document, string $documentId): array
    {
        $row = [];

        foreach ($document['fields'] ?? [] as $name => $value) {
            $row[$name] = $this->decodeValue($value);
        }

        if (!isset($row['id'])) {
            $row['id'] = is_numeric($documentId) ? (int)$documentId : $documentId;
        }

        return $row;
    }

    private function encodeFields(array $row): array
    {
        $fields = [];

        foreach ($row as $key => $value) {
            $fields[$key] = $this->encodeValue($value);
        }

        return $fields;
    }

    private function encodeValue(mixed $value): array
    {
        if (is_bool($value)) {
            return ['booleanValue' => $value];
        }

        if (is_int($value)) {
            return ['integerValue' => (string)$value];
        }

        if (is_float($value)) {
            return ['doubleValue' => $value];
        }

        if ($value === null) {
            return ['nullValue' => null];
        }

        return ['stringValue' => (string)$value];
    }

    private function decodeValue(array $value): mixed
    {
        if (array_key_exists('booleanValue', $value)) {
            return (bool)$value['booleanValue'];
        }

        if (array_key_exists('integerValue', $value)) {
            return (int)$value['integerValue'];
        }

        if (array_key_exists('doubleValue', $value)) {
            return (float)$value['doubleValue'];
        }

        if (array_key_exists('nullValue', $value)) {
            return null;
        }

        return (string)($value['stringValue'] ?? '');
    }
}
