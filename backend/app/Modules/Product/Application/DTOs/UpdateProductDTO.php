<?php

namespace App\Modules\Product\Application\DTOs;

class UpdateProductDTO
{
    public function __construct(
        public readonly ?int $categoryId = null,
        public readonly ?string $name = null,
        public readonly ?string $slug = null,
        public readonly ?float $price = null,
        public readonly ?int $stockQuantity = null,
        public readonly ?string $description = null,
        public readonly ?string $imageUrl = null,
        public readonly ?bool $isActive = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            categoryId: isset($data['category_id']) ? (int) $data['category_id'] : null,
            name: $data['name'] ?? null,
            slug: $data['slug'] ?? null,
            price: isset($data['price']) ? (float) $data['price'] : null,
            stockQuantity: isset($data['stock_quantity']) ? (int) $data['stock_quantity'] : null,
            description: $data['description'] ?? null,
            imageUrl: $data['image_url'] ?? null,
            isActive: isset($data['is_active']) ? (bool) $data['is_active'] : null,
        );
    }
}
