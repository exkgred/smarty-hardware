<?php

namespace App\Modules\Order\Application\UseCases;

use App\Modules\Common\Domain\DomainException;
use App\Modules\Inventory\Domain\InventoryServiceInterface;
use App\Modules\Order\Application\DTOs\CreateOrderDTO;
use App\Modules\Order\Domain\OrderEntity;
use App\Modules\Order\Domain\OrderItemEntity;
use App\Modules\Order\Domain\OrderRepositoryInterface;
use App\Modules\Order\Domain\OrderStatusEnum;
use App\Modules\Product\Domain\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CreateOrderUseCase
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
        private readonly InventoryServiceInterface $inventory,
        private readonly ProductRepositoryInterface $products,
    ) {}

    public function handle(CreateOrderDTO $dto): OrderEntity
    {
        if ($dto->items === []) {
            throw new DomainException('O pedido precisa de pelo menos um item.');
        }

        return DB::transaction(function () use ($dto) {
            $lineItems = [];
            $subtotal = 0.0;

            foreach ($dto->items as $item) {
                if ($item['quantity'] <= 0) {
                    throw new DomainException('Quantidade inválida.');
                }

                $product = $this->products->findById($item['product_id']);
                if ($product === null || ! $product->isActive) {
                    throw new DomainException("Produto {$item['product_id']} não encontrado.", 404);
                }
                if ($product->stockQuantity < $item['quantity']) {
                    throw new DomainException("Estoque insuficiente para {$product->name}.");
                }

                $lineSubtotal = $product->price * $item['quantity'];
                $subtotal += $lineSubtotal;
                $lineItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'subtotal' => $lineSubtotal,
                ];
            }

            $discount = max(0, $dto->discountAmount);
            $order = $this->orders->save(new OrderEntity(
                id: null,
                userId: $dto->userId,
                status: OrderStatusEnum::PENDING,
                subtotal: $subtotal,
                discountAmount: $discount,
                shippingCost: $dto->shippingCost,
                total: max(0, $subtotal - $discount + $dto->shippingCost),
                items: array_map(static fn (array $line) => new OrderItemEntity(
                    id: null,
                    productId: (int) $line['product']->id,
                    productName: $line['product']->name,
                    unitPrice: $line['product']->price,
                    quantity: $line['quantity'],
                    subtotal: $line['subtotal'],
                ), $lineItems),
                shippingName: $dto->shippingName,
                shippingAddress: $dto->shippingAddress,
                paymentMethod: $dto->paymentMethod,
                channel: $dto->channel,
                shippingDetails: $dto->shippingDetails,
            ));

            foreach ($lineItems as $line) {
                $this->inventory->reserve((int) $line['product']->id, $line['quantity'], (int) $order->id);
            }

            return $this->orders->findById((int) $order->id) ?? $order;
        });
    }
}
