<?php

namespace App\Interfaces;

interface WeaponRepositoryInterface
{
    public function getAll(int $page, string $sort, string $order, string $filter, string $status): array;
    public function find(int $id): ?array;
    public function create(array $data): bool;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function count(string $filter, string $status): int;
    public function getAllStores(): array;
    public function getStore(int $storeId): ?array;

}
