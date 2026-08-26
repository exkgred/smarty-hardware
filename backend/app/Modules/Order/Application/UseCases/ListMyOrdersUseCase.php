<?php

namespace App\Modules\Order\Application\UseCases;

use App\Modules\Order\Domain\OrderRepositoryInterface;

class ListMyOrdersUseCase
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
    ) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function handle(int $userId): array
    {
        return $this->orders->findByUserId($userId);
    }
}
