<?php

interface DataRepository
{
    public function all(): array;
    public function find(string $field, $value): array;
    public function save(array $data): bool;
    public function delete($id): bool;
    public static function createFromData(array $data): self;
}