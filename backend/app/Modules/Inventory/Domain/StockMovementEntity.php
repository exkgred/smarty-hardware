<?php

namespace App\Modules\Inventory\Domain;

use App\Modules\Common\Domain\EntityInterface;

class StockMovementEntity implements EntityInterface
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $productId,
        public readonly StockMovementTypeEnum $type,
        public readonly int $quantity,
        public readonly string $reason,
        public readonly ?int $userId = null,
        public readonly ?int $orderId = null,
        public readonly ?string $createdAt = null,
        public readonly ?string $documentNumber = null,
        public readonly ?string $productName = null,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->productId,
            'product_name' => $this->productName,
            'type' => $this->type->value,
            'quantity' => $this->quantity,
            'reason' => $this->reason,
            'document_number' => $this->documentNumber,
            'user_id' => $this->userId,
            'order_id' => $this->orderId,
            'created_at' => $this->createdAt,
        ];
    }
}
