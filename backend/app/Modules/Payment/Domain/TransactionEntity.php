<?php

namespace App\Modules\Payment\Domain;

use App\Modules\Common\Domain\EntityInterface;

class TransactionEntity implements EntityInterface
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $orderId,
        public readonly string $gateway,
        public readonly ?string $gatewayPaymentId,
        public readonly float $amount,
        public readonly PaymentStatusEnum $status,
        public readonly ?string $idempotencyKey = null,
        public readonly ?array $payload = null,
        public readonly ?string $createdAt = null,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->orderId,
            'gateway' => $this->gateway,
            'gateway_payment_id' => $this->gatewayPaymentId,
            'amount' => $this->amount,
            'status' => $this->status->value,
            'idempotency_key' => $this->idempotencyKey,
            'created_at' => $this->createdAt,
        ];
    }
}
