<?php

namespace App\Modules\Inventory\Application\UseCases;

use App\Modules\Inventory\Domain\InventoryServiceInterface;

class ListStockMovementsUseCase
{
    public function __construct(
        private readonly InventoryServiceInterface $inventory,
    ) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function handle(int $productId): array
    {
        return $this->inventory->getMovements($productId);
    }
}
