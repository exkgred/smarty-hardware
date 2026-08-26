<?php

namespace App\Modules\Product\Application\UseCases;

use App\Modules\Product\Domain\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListProductsUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    public function handle(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->productRepository->findAll($filters, $perPage);
    }
}
