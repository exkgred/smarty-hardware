<?php

namespace App\Modules\Product\Application\UseCases;

use App\Modules\Common\Domain\DomainException;
use App\Modules\Product\Application\DTOs\CreateProductDTO;
use App\Modules\Product\Domain\ProductEntity;
use App\Modules\Product\Domain\ProductRepositoryInterface;

class CreateProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    public function handle(CreateProductDTO $dto): ProductEntity
    {
        $existing = $this->productRepository->findBySlug($dto->slug);
        if ($existing !== null) {
            throw new DomainException("A product with slug '{$dto->slug}' already exists.", 409);
        }

        $product = new ProductEntity(
            id: null,
            categoryId: $dto->categoryId,
            name: $dto->name,
            slug: $dto->slug,
            description: $dto->description,
            price: $dto->price,
            stockQuantity: $dto->stockQuantity,
            imageUrl: $dto->imageUrl,
            isActive: $dto->isActive,
        );

        return $this->productRepository->save($product);
    }
}
