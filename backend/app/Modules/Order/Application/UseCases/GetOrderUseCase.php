<?php

namespace App\Modules\Order\Application\UseCases;

use App\Modules\Common\Domain\DomainException;
use App\Modules\Order\Domain\OrderEntity;
use App\Modules\Order\Domain\OrderRepositoryInterface;

class GetOrderUseCase
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
    ) {}

    public function handle(int $id, ?int $userId = null, bool $asAdmin = false): OrderEntity
    {
        $order = $this->orders->findById($id);
        if ($order === null) {
            throw new DomainException("Pedido {$id} não encontrado.", 404);
        }

        if (! $asAdmin && $userId !== null && $order->userId !== $userId) {
            throw new DomainException('Pedido não encontrado.', 404);
        }

        return $order;
    }
}
