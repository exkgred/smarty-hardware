<?php

namespace App\Modules\Product\Domain;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function findById(int $id): ?ProductEntity;

    public function findBySlug(string $slug): ?ProductEntity;

    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function save(ProductEntity $product): ProductEntity;

    public function delete(int $id): bool;

    /**
     * @return array<int, ProductEntity>
     */
    public function listActive(int $limit = 100): array;
}
