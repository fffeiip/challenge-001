<?php

namespace App\Interfaces;

interface WeaponRepositoryInterface
{
    public function getAll(int $page, string $sort, string $order, string $filter): array;
    public function find(int $id): ?array;
    public function create(array $data): bool;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
