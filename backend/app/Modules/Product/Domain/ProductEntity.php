<?php

namespace App\Modules\Product\Domain;

use App\Modules\Common\Domain\EntityInterface;

class ProductEntity implements EntityInterface
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $categoryId,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $description,
        public readonly float $price,
        public readonly int $stockQuantity,
        public readonly ?string $imageUrl = null,
        public readonly bool $isActive = true,
        public readonly ?string $createdAt = null,
        public readonly ?CategoryEntity $category = null,
    ) {}

    public function isInStock(): bool
    {
        return $this->stockQuantity > 0;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->categoryId,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'stock_quantity' => $this->stockQuantity,
            'image_url' => $this->imageUrl,
            'is_active' => $this->isActive,
            'is_in_stock' => $this->isInStock(),
            'created_at' => $this->createdAt,
            'category' => $this->category?->toArray(),
        ];
    }
}
