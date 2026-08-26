<?php

namespace App\Modules\Invoice\Domain;

use App\Modules\Common\Domain\EntityInterface;

class InvoiceEntity implements EntityInterface
{
    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    public function __construct(
        public readonly ?int $id,
        public readonly string $number,
        public readonly InvoiceTypeEnum $type,
        public readonly string $customerName,
        public readonly float $total,
        public readonly array $items,
        public readonly ?int $orderId = null,
        public readonly ?int $userId = null,
        public readonly ?string $customerDocument = null,
        public readonly ?string $paymentMethod = null,
        public readonly ?string $notes = null,
        public readonly ?string $createdAt = null,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'type' => $this->type->value,
            'order_id' => $this->orderId,
            'user_id' => $this->userId,
            'customer_name' => $this->customerName,
            'customer_document' => $this->customerDocument,
            'payment_method' => $this->paymentMethod,
            'total' => $this->total,
            'items' => $this->items,
            'notes' => $this->notes,
            'created_at' => $this->createdAt,
        ];
    }
}
