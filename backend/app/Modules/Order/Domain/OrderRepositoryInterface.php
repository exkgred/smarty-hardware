<?php

namespace App\Modules\Order\Domain;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    public function findById(int $id): ?OrderEntity;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findByUserId(int $userId): array;

    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function save(OrderEntity $order): OrderEntity;

    public function countAll(): int;

    public function countByStatus(string $status): int;

    public function sumPaidTotal(): float;
}
