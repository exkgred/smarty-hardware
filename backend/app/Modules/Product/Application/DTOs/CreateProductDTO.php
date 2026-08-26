<?php

namespace App\Modules\Product\Application\DTOs;

class CreateProductDTO
{
    public function __construct(
        public readonly int $categoryId,
        public readonly string $name,
        public readonly string $slug,
        public readonly float $price,
        public readonly int $stockQuantity,
        public readonly ?string $description = null,
        public readonly ?string $imageUrl = null,
        public readonly bool $isActive = true,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            categoryId: (int) $data['category_id'],
            name: $data['name'],
            slug: $data['slug'],
            price: (float) $data['price'],
            stockQuantity: (int) ($data['stock_quantity'] ?? 0),
            description: $data['description'] ?? null,
            imageUrl: $data['image_url'] ?? null,
            isActive: (bool) ($data['is_active'] ?? true),
        );
    }
}
