<?php

namespace App\Modules\Payment\Domain;

interface PaymentGatewayInterface
{
    /**
     * @param  array<string, mixed>  $options
     * @return array{gateway: string, gateway_payment_id: string, client_secret: ?string, auto_succeed: bool, payment_method?: string, receipt?: array<string, mixed>}
     */
    public function createPaymentIntent(int $orderId, int $amountCents, string $currency = 'brl', array $options = []): array;

    /**
     * @return array{event_id: string, type: string, order_id: ?int, gateway_payment_id: ?string, succeeded: bool}
     */
    public function processWebhook(string $payload, ?string $signature): array;

    public function refund(string $gatewayPaymentId, int $amountCents): bool;
}
