<?php

namespace App\Modules\Inventory\Application\UseCases;

use App\Modules\Common\Domain\DomainException;
use App\Modules\Inventory\Application\DTOs\AdjustStockDTO;
use App\Modules\Inventory\Domain\InventoryServiceInterface;
use App\Modules\Inventory\Domain\StockMovementEntity;
use App\Modules\Inventory\Domain\StockMovementTypeEnum;

class AdjustStockUseCase
{
    public function __construct(
        private readonly InventoryServiceInterface $inventory,
    ) {}

    public function handle(AdjustStockDTO $dto, ?int $userId = null): StockMovementEntity
    {
        $type = StockMovementTypeEnum::tryFrom($dto->type);
        if ($type === null) {
            throw new DomainException('Tipo de movimentação inválido.');
        }

        return $this->inventory->adjust($dto->productId, $type, $dto->quantity, $dto->reason, $userId);
    }
}
