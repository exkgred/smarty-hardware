<?php

namespace App\Modules\Payment\Domain;

interface TransactionRepositoryInterface
{
    public function findByIdempotencyKey(string $key): ?TransactionEntity;

    public function findSucceededByOrderId(int $orderId): ?TransactionEntity;

    public function save(TransactionEntity $transaction): TransactionEntity;
}
