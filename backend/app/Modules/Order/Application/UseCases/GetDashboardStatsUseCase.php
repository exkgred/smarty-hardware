<?php

namespace App\Modules\Order\Application\UseCases;

use App\Modules\Inventory\Domain\InventoryServiceInterface;
use App\Modules\Order\Domain\OrderRepositoryInterface;

class GetDashboardStatsUseCase
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
        private readonly InventoryServiceInterface $inventory,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function handle(): array
    {
        $alerts = $this->inventory->getLowStockAlerts();
        $recent = $this->orders->findAll([], 5);

        return [
            'totalOrders' => $this->orders->countAll(),
            'pendingOrders' => $this->orders->countByStatus('PENDING'),
            'totalSales' => $this->orders->sumPaidTotal(),
            'lowStockProducts' => count($alerts),
            'recent_orders' => $recent->items(),
        ];
    }
}
