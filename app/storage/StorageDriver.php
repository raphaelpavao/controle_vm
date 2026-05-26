<?php

interface StorageDriver
{
    public function init(): void;

    public function name(): string;

    public function all(string $table): array;

    public function find(string $table, int|string $id): ?array;

    public function insert(string $table, array $row): int|string;

    public function update(string $table, int|string $id, array $attributes): void;

    public function delete(string $table, int|string $id): bool;
}
