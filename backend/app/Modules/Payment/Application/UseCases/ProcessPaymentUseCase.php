<?php

namespace App\Modules\Payment\Application\UseCases;

use App\Modules\Common\Domain\DomainException;
use App\Modules\Invoice\Application\UseCases\IssueSaleInvoiceUseCase;
use App\Modules\Order\Domain\OrderRepositoryInterface;
use App\Modules\Order\Domain\OrderStatusEnum;
use App\Modules\Payment\Domain\PaymentGatewayInterface;
use App\Modules\Payment\Domain\PaymentMethodEnum;
use App\Modules\Payment\Domain\PaymentStatusEnum;
use App\Modules\Payment\Domain\TransactionEntity;
use App\Modules\Payment\Domain\TransactionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ProcessPaymentUseCase
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
        private readonly PaymentGatewayInterface $gateway,
        private readonly TransactionRepositoryInterface $transactions,
        private readonly IssueSaleInvoiceUseCase $issueInvoice,
    ) {}

    /**
     * @param  array<string, mixed>  $cardMeta
     * @return array<string, mixed>
     */
    public function handle(int $orderId, int $userId, bool $asAdmin = false, ?string $paymentMethod = null, array $cardMeta = []): array
    {
        return DB::transaction(function () use ($orderId, $userId, $asAdmin, $paymentMethod, $cardMeta) {
            $order = $this->orders->findById($orderId);
            if ($order === null || (! $asAdmin && $order->userId !== $userId)) {
                throw new DomainException('Pedido não encontrado.', 404);
            }
            if ($order->status === OrderStatusEnum::PAID) {
                throw new DomainException('Pedido já está pago.');
            }
            if ($order->status === OrderStatusEnum::CANCELLED) {
                throw new DomainException('Não é possível pagar um pedido cancelado.');
            }

            $method = PaymentMethodEnum::tryFrom(strtoupper((string) ($paymentMethod ?? $order->paymentMethod?->value ?? 'PIX')))
                ?? PaymentMethodEnum::PIX;

            $intent = $this->gateway->createPaymentIntent(
                (int) $order->id,
                (int) round($order->total * 100),
                'brl',
                [
                    'payment_method' => $method->value,
                    'last_four' => $cardMeta['last_four'] ?? null,
                    'brand' => $cardMeta['brand'] ?? null,
                    'holder_name' => $cardMeta['holder_name'] ?? null,
                ],
            );

            $status = $intent['auto_succeed'] ? PaymentStatusEnum::SUCCEEDED : PaymentStatusEnum::PENDING;
            $transaction = $this->transactions->save(new TransactionEntity(
                id: null,
                orderId: (int) $order->id,
                gateway: $intent['gateway'],
                gatewayPaymentId: $intent['gateway_payment_id'],
                amount: $order->total,
                status: $status,
                payload: $intent,
            ));

            if ($intent['auto_succeed']) {
                $this->orders->save($order->withStatus(OrderStatusEnum::PAID));
                $this->issueInvoice->handle((int) $order->id);
            }

            $fresh = $this->orders->findById((int) $order->id) ?? $order;

            return [
                'transaction' => $transaction->toArray(),
                'client_secret' => $intent['client_secret'],
                'order_status' => $intent['auto_succeed'] ? OrderStatusEnum::PAID->value : $order->status->value,
                'payment_method' => $method->value,
                'payment_receipt' => $intent['receipt'] ?? null,
                'order' => $fresh->toArray(),
            ];
        });
    }
}
