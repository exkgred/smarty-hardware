<?php

namespace App\Modules\Product\Domain;

interface CategoryRepositoryInterface
{
    public function findById(int $id): ?CategoryEntity;

    public function findAll(): array;

    public function save(CategoryEntity $category): CategoryEntity;

    public function delete(int $id): bool;
}
