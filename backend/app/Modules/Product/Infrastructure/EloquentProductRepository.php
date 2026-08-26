<?php

namespace App\Modules\Product\Infrastructure;

use App\Modules\Product\Domain\CategoryEntity;
use App\Modules\Product\Domain\ProductEntity;
use App\Modules\Product\Domain\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        private readonly EloquentProductModel $model,
    ) {}

    public function findById(int $id): ?ProductEntity
    {
        $record = $this->model->with('category')->find($id);

        return $record ? $this->toEntity($record) : null;
    }

    public function findBySlug(string $slug): ?ProductEntity
    {
        $record = $this->model->with('category')->where('slug', $slug)->first();

        return $record ? $this->toEntity($record) : null;
    }

    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with('category');

        if (array_key_exists('is_active', $filters) && $filters['is_active'] !== null) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', (int) $filters['category_id']);
        }

        if (isset($filters['min_price']) && $filters['min_price'] !== null && $filters['min_price'] !== '') {
            $query->where('price', '>=', (float) $filters['min_price']);
        }

        if (isset($filters['max_price']) && $filters['max_price'] !== null && $filters['max_price'] !== '') {
            $query->where('price', '<=', (float) $filters['max_price']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query
            ->orderByDesc('id')
            ->paginate($perPage)
            ->through(fn (EloquentProductModel $record) => $this->toEntity($record)->toArray());
    }

    public function save(ProductEntity $product): ProductEntity
    {
        $data = [
            'category_id' => $product->categoryId,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'price' => $product->price,
            'stock_quantity' => $product->stockQuantity,
            'image_url' => $product->imageUrl,
            'is_active' => $product->isActive,
        ];

        if ($product->id) {
            $record = $this->model->findOrFail($product->id);
            $record->update($data);
        } else {
            $record = $this->model->create($data);
        }

        return $this->toEntity($record->fresh('category'));
    }

    public function delete(int $id): bool
    {
        return (bool) $this->model->destroy($id);
    }

    public function listActive(int $limit = 100): array
    {
        return $this->model->newQuery()
            ->with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->limit($limit)
            ->get()
            ->map(fn (EloquentProductModel $record) => $this->toEntity($record))
            ->all();
    }

    private function toEntity(EloquentProductModel $model): ProductEntity
    {
        $category = $model->relationLoaded('category') && $model->category
            ? new CategoryEntity(
                id: $model->category->id,
                name: $model->category->name,
                slug: $model->category->slug,
                description: $model->category->description,
                createdAt: $model->category->created_at?->toIso8601String(),
            )
            : null;

        return new ProductEntity(
            id: $model->id,
            categoryId: $model->category_id,
            name: $model->name,
            slug: $model->slug,
            description: $model->description,
            price: (float) $model->price,
            stockQuantity: (int) $model->stock_quantity,
            imageUrl: $model->image_url,
            isActive: (bool) $model->is_active,
            createdAt: $model->created_at?->toIso8601String(),
            category: $category,
        );
    }
}
