<?php

namespace App\Modules\Product\Application\UseCases;

use App\Modules\Product\Domain\CategoryRepositoryInterface;

class ListCategoriesUseCase
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
    ) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function handle(): array
    {
        return array_map(
            static fn ($category) => $category->toArray(),
            $this->categoryRepository->findAll(),
        );
    }
}
