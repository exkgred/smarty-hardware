<?php

namespace App\Modules\Inventory\Application\UseCases;

use App\Modules\Inventory\Domain\InventoryServiceInterface;

class GetStockAlertsUseCase
{
    public function __construct(
        private readonly InventoryServiceInterface $inventory,
    ) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function handle(int $threshold = 5): array
    {
        return $this->inventory->getLowStockAlerts($threshold);
    }
}
