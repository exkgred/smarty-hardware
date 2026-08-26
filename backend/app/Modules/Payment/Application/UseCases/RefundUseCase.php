<?php

namespace App\Modules\Payment\Application\UseCases;

use App\Modules\Common\Domain\DomainException;
use App\Modules\Inventory\Domain\InventoryServiceInterface;
use App\Modules\Order\Domain\OrderRepositoryInterface;
use App\Modules\Order\Domain\OrderStatusEnum;
use App\Modules\Payment\Domain\PaymentGatewayInterface;
use App\Modules\Payment\Domain\PaymentStatusEnum;
use App\Modules\Payment\Domain\TransactionEntity;
use App\Modules\Payment\Domain\TransactionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class RefundUseCase
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
        private readonly TransactionRepositoryInterface $transactions,
        private readonly PaymentGatewayInterface $gateway,
        private readonly InventoryServiceInterface $inventory,
    ) {}

    public function handle(int $orderId): array
    {
        return DB::transaction(function () use ($orderId) {
            $order = $this->orders->findById($orderId);
            if ($order === null) {
                throw new DomainException('Pedido não encontrado.', 404);
            }
            if ($order->status !== OrderStatusEnum::PAID) {
                throw new DomainException('Somente pedidos pagos podem ser reembolsados.');
            }

            $transaction = $this->transactions->findSucceededByOrderId($orderId);
            if ($transaction?->gatewayPaymentId) {
                $this->gateway->refund(
                    $transaction->gatewayPaymentId,
                    (int) round($order->total * 100),
                );
            }

            foreach ($order->items as $item) {
                $this->inventory->release($item->productId, $item->quantity, (int) $order->id);
            }

            $updated = $this->orders->save($order->withStatus(OrderStatusEnum::CANCELLED));

            if ($transaction !== null) {
                $this->transactions->save(new TransactionEntity(
                    id: $transaction->id,
                    orderId: $transaction->orderId,
                    gateway: $transaction->gateway,
                    gatewayPaymentId: $transaction->gatewayPaymentId,
                    amount: $transaction->amount,
                    status: PaymentStatusEnum::REFUNDED,
                    idempotencyKey: $transaction->idempotencyKey,
                    payload: $transaction->payload,
                ));
            }

            return $updated->toArray();
        });
    }
}
