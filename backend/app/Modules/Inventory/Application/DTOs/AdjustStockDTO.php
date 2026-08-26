<?php

namespace App\Modules\Inventory\Application\DTOs;

class AdjustStockDTO
{
    public function __construct(
        public readonly int $productId,
        public readonly string $type,
        public readonly int $quantity,
        public readonly string $reason,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            productId: (int) $data['product_id'],
            type: strtoupper((string) $data['type']),
            quantity: (int) $data['quantity'],
            reason: (string) $data['reason'],
        );
    }
}
