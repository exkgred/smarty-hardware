<?php

namespace App\Modules\Payment\Infrastructure;

use App\Modules\Payment\Domain\PaymentStatusEnum;
use App\Modules\Payment\Domain\TransactionEntity;
use App\Modules\Payment\Domain\TransactionRepositoryInterface;

class EloquentTransactionRepository implements TransactionRepositoryInterface
{
    public function findByIdempotencyKey(string $key): ?TransactionEntity
    {
        $record = EloquentTransactionModel::query()->where('idempotency_key', $key)->first();

        return $record ? $this->toEntity($record) : null;
    }

    public function findSucceededByOrderId(int $orderId): ?TransactionEntity
    {
        $record = EloquentTransactionModel::query()
            ->where('order_id', $orderId)
            ->where('status', PaymentStatusEnum::SUCCEEDED->value)
            ->latest('id')
            ->first();

        return $record ? $this->toEntity($record) : null;
    }

    public function save(TransactionEntity $transaction): TransactionEntity
    {
        $data = [
            'order_id' => $transaction->orderId,
            'gateway' => $transaction->gateway,
            'gateway_payment_id' => $transaction->gatewayPaymentId,
            'idempotency_key' => $transaction->idempotencyKey,
            'amount' => $transaction->amount,
            'status' => $transaction->status->value,
            'payload' => $transaction->payload,
        ];

        if ($transaction->id) {
            $record = EloquentTransactionModel::findOrFail($transaction->id);
            $record->update($data);
        } else {
            $record = EloquentTransactionModel::create($data);
        }

        return $this->toEntity($record->fresh());
    }

    private function toEntity(EloquentTransactionModel $model): TransactionEntity
    {
        return new TransactionEntity(
            id: $model->id,
            orderId: $model->order_id,
            gateway: $model->gateway,
            gatewayPaymentId: $model->gateway_payment_id,
            amount: (float) $model->amount,
            status: PaymentStatusEnum::from($model->status),
            idempotencyKey: $model->idempotency_key,
            payload: $model->payload,
            createdAt: $model->created_at?->toIso8601String(),
        );
    }
}
