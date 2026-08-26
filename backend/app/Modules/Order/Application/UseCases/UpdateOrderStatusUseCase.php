<?php

namespace App\Modules\Order\Application\UseCases;

use App\Modules\Common\Domain\DomainException;
use App\Modules\Inventory\Domain\InventoryServiceInterface;
use App\Modules\Order\Domain\OrderEntity;
use App\Modules\Order\Domain\OrderRepositoryInterface;
use App\Modules\Order\Domain\OrderStatusEnum;
use Illuminate\Support\Facades\DB;

class UpdateOrderStatusUseCase
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
        private readonly InventoryServiceInterface $inventory,
    ) {}

    public function handle(int $id, string $status): OrderEntity
    {
        $newStatus = OrderStatusEnum::tryFrom(strtoupper($status));
        if ($newStatus === null) {
            throw new DomainException('Status de pedido inválido.');
        }

        return DB::transaction(function () use ($id, $newStatus) {
            $order = $this->orders->findById($id);
            if ($order === null) {
                throw new DomainException("Pedido {$id} não encontrado.", 404);
            }

            if ($order->status === $newStatus) {
                return $order;
            }

            if ($newStatus === OrderStatusEnum::CANCELLED && $order->status !== OrderStatusEnum::CANCELLED) {
                foreach ($order->items as $item) {
                    $this->inventory->release($item->productId, $item->quantity, (int) $order->id);
                }
            }

            return $this->orders->save($order->withStatus($newStatus));
        });
    }
}
