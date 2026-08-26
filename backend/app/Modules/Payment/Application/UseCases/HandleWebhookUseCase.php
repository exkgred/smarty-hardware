<?php

namespace App\Modules\Payment\Application\UseCases;

use App\Modules\Order\Domain\OrderRepositoryInterface;
use App\Modules\Order\Domain\OrderStatusEnum;
use App\Modules\Payment\Domain\PaymentGatewayInterface;
use App\Modules\Payment\Domain\PaymentStatusEnum;
use App\Modules\Payment\Domain\TransactionEntity;
use App\Modules\Payment\Domain\TransactionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class HandleWebhookUseCase
{
    public function __construct(
        private readonly PaymentGatewayInterface $gateway,
        private readonly TransactionRepositoryInterface $transactions,
        private readonly OrderRepositoryInterface $orders,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function handle(string $payload, ?string $signature): array
    {
        $event = $this->gateway->processWebhook($payload, $signature);

        return DB::transaction(function () use ($event) {
            $existing = $this->transactions->findByIdempotencyKey($event['event_id']);
            if ($existing !== null) {
                return ['duplicate' => true, 'event_id' => $event['event_id']];
            }

            if (! $event['succeeded'] || $event['order_id'] === null) {
                return ['duplicate' => false, 'processed' => false];
            }

            $order = $this->orders->findById($event['order_id']);
            if ($order !== null && $order->status !== OrderStatusEnum::PAID) {
                $this->orders->save($order->withStatus(OrderStatusEnum::PAID));
            }

            $this->transactions->save(new TransactionEntity(
                id: null,
                orderId: $event['order_id'],
                gateway: 'stripe',
                gatewayPaymentId: $event['gateway_payment_id'],
                amount: $order?->total ?? 0,
                status: PaymentStatusEnum::SUCCEEDED,
                idempotencyKey: $event['event_id'],
                payload: $event,
            ));

            return ['duplicate' => false, 'processed' => true, 'order_id' => $event['order_id']];
        });
    }
}
