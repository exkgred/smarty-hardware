<?php

namespace App\Modules\Inventory\Application\UseCases;

use App\Modules\Inventory\Domain\InventoryServiceInterface;

class ListRecentMovementsUseCase
{
    public function __construct(private readonly InventoryServiceInterface $inventory) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function handle(int $limit = 50): array
    {
        return $this->inventory->getRecentMovements($limit);
    }
}
