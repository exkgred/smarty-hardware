<?php

namespace App\Modules\Product\Application\UseCases;

use App\Modules\Common\Domain\DomainException;
use App\Modules\Product\Application\DTOs\UpdateProductDTO;
use App\Modules\Product\Domain\ProductEntity;
use App\Modules\Product\Domain\ProductRepositoryInterface;

class UpdateProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    public function handle(int $id, UpdateProductDTO $dto): ProductEntity
    {
        $existing = $this->productRepository->findById($id);
        if ($existing === null) {
            throw new DomainException("Product with id {$id} not found.", 404);
        }

        if ($dto->slug !== null && $dto->slug !== $existing->slug) {
            $slugTaken = $this->productRepository->findBySlug($dto->slug);
            if ($slugTaken !== null) {
                throw new DomainException("A product with slug '{$dto->slug}' already exists.", 409);
            }
        }

        $updated = new ProductEntity(
            id: $existing->id,
            categoryId: $dto->categoryId ?? $existing->categoryId,
            name: $dto->name ?? $existing->name,
            slug: $dto->slug ?? $existing->slug,
            description: $dto->description ?? $existing->description,
            price: $dto->price ?? $existing->price,
            stockQuantity: $dto->stockQuantity ?? $existing->stockQuantity,
            imageUrl: $dto->imageUrl ?? $existing->imageUrl,
            isActive: $dto->isActive ?? $existing->isActive,
        );

        return $this->productRepository->save($updated);
    }
}
