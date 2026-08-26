<?php

namespace App\Modules\Product\Infrastructure;

use App\Modules\Product\Domain\CategoryEntity;
use App\Modules\Product\Domain\CategoryRepositoryInterface;

class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(
        private readonly EloquentCategoryModel $model,
    ) {}

    public function findById(int $id): ?CategoryEntity
    {
        $record = $this->model->find($id);

        return $record ? $this->toEntity($record) : null;
    }

    public function findAll(): array
    {
        return $this->model->all()->map(fn ($r) => $this->toEntity($r))->toArray();
    }

    public function save(CategoryEntity $category): CategoryEntity
    {
        if ($category->id) {
            $record = $this->model->findOrFail($category->id);
            $record->update($category->toArray());
        } else {
            $record = $this->model->create([
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
            ]);
        }

        return $this->toEntity($record->fresh());
    }

    public function delete(int $id): bool
    {
        return (bool) $this->model->destroy($id);
    }

    private function toEntity(EloquentCategoryModel $model): CategoryEntity
    {
        return new CategoryEntity(
            id: $model->id,
            name: $model->name,
            slug: $model->slug,
            description: $model->description,
            createdAt: $model->created_at?->toIso8601String(),
        );
    }
}
