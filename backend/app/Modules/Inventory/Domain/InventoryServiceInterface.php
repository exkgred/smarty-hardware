<?php

namespace App\Modules\Inventory\Domain;

interface InventoryServiceInterface
{
    public function adjust(int $productId, StockMovementTypeEnum $type, int $quantity, string $reason, ?int $userId = null, ?int $orderId = null, ?string $documentNumber = null): StockMovementEntity;

    public function reserve(int $productId, int $quantity, int $orderId): StockMovementEntity;

    public function release(int $productId, int $quantity, int $orderId): StockMovementEntity;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getMovements(int $productId): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getRecentMovements(int $limit = 50): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getLowStockAlerts(int $threshold = 5): array;
}
