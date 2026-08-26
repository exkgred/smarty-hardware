<?php

namespace App\Modules\Product\Application\UseCases;

use App\Modules\Common\Domain\DomainException;
use App\Modules\Product\Domain\ProductRepositoryInterface;

class DeleteProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    public function handle(int $id): bool
    {
        $existing = $this->productRepository->findById($id);
        if ($existing === null) {
            throw new DomainException("Product with id {$id} not found.", 404);
        }

        return $this->productRepository->delete($id);
    }
}
