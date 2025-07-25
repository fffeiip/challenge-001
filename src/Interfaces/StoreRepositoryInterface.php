<?php

namespace App\Interfaces;

interface StoreRepositoryInterface
{
    public function getAll(): array;
    public function find();
    public function create(array $data): bool;
    public function update();
    public function delete();
}
