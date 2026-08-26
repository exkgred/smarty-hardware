<?php

namespace App\Modules\Product\Application\UseCases;

use App\Modules\Common\Domain\DomainException;
use App\Modules\Product\Domain\ProductEntity;
use App\Modules\Product\Domain\ProductRepositoryInterface;

class GetProductDetailUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    public function handle(string $slug): ProductEntity
    {
        $product = $this->productRepository->findBySlug($slug);
        if ($product === null || ! $product->isActive) {
            throw new DomainException("Product with slug '{$slug}' not found.", 404);
        }

        return $product;
    }
}
