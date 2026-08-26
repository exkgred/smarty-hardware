<?php

namespace App\Modules\Invoice\Application\UseCases;

use App\Modules\Common\Domain\DomainException;
use App\Modules\Invoice\Domain\InvoiceEntity;
use App\Modules\Invoice\Domain\InvoiceRepositoryInterface;
use App\Modules\Invoice\Domain\InvoiceTypeEnum;
use App\Modules\Order\Domain\OrderEntity;
use App\Modules\Order\Domain\OrderRepositoryInterface;
use App\Modules\User\Domain\UserRepositoryInterface;

class IssueSaleInvoiceUseCase
{
    public function __construct(
        private readonly InvoiceRepositoryInterface $invoices,
        private readonly OrderRepositoryInterface $orders,
        private readonly UserRepositoryInterface $users,
    ) {}

    public function handle(int $orderId): InvoiceEntity
    {
        $existing = $this->invoices->findByOrderId($orderId);
        if ($existing !== null) {
            return $existing;
        }

        $order = $this->orders->findById($orderId);
        if ($order === null) {
            throw new DomainException("Pedido {$orderId} não encontrado.", 404);
        }

        return $this->fromOrder($order);
    }

    public function fromOrder(OrderEntity $order): InvoiceEntity
    {
        if ($order->id === null) {
            throw new DomainException('Pedido ainda não foi persistido.');
        }

        $existing = $this->invoices->findByOrderId((int) $order->id);
        if ($existing !== null) {
            return $existing;
        }

        $user = $this->users->findById($order->userId);

        return $this->invoices->save(new InvoiceEntity(
            id: null,
            number: $this->invoices->nextNumber(InvoiceTypeEnum::SALE),
            type: InvoiceTypeEnum::SALE,
            customerName: $user?->getName() ?? $order->shippingName ?? 'Cliente Smarty',
            total: $order->total,
            items: array_map(static fn ($item) => [
                'product_id' => $item->productId,
                'name' => $item->productName,
                'quantity' => $item->quantity,
                'unit_price' => $item->unitPrice,
                'subtotal' => $item->subtotal,
            ], $order->items),
            orderId: $order->id,
            userId: $order->userId,
            paymentMethod: $order->paymentMethod?->value,
            notes: $order->channel->value === 'POS' ? 'Venda no balcão Smarty Hardware' : 'Venda online Smarty Hardware',
        ));
    }
}
