<?php

namespace App\Modules\Order\Domain;

use App\Modules\Common\Domain\EntityInterface;

class OrderItemEntity implements EntityInterface
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $productId,
        public readonly string $productName,
        public readonly float $unitPrice,
        public readonly int $quantity,
        public readonly float $subtotal,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->productId,
            'product_name' => $this->productName,
            'unit_price' => $this->unitPrice,
            'quantity' => $this->quantity,
            'subtotal' => $this->subtotal,
        ];
    }
}
