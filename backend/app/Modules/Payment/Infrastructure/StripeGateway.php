<?php

namespace App\Modules\Payment\Infrastructure;

use App\Modules\Common\Domain\DomainException;
use App\Modules\Payment\Domain\PaymentGatewayInterface;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripeGateway implements PaymentGatewayInterface
{
    public function createPaymentIntent(int $orderId, int $amountCents, string $currency = 'brl', array $options = []): array
    {
        $client = new StripeClient((string) config('services.stripe.secret'));
        $intent = $client->paymentIntents->create([
            'amount' => $amountCents,
            'currency' => $currency,
            'metadata' => [
                'order_id' => (string) $orderId,
                'payment_method' => (string) ($options['payment_method'] ?? 'PIX'),
            ],
            'automatic_payment_methods' => ['enabled' => true],
        ]);

        return [
            'gateway' => 'stripe',
            'gateway_payment_id' => $intent->id,
            'client_secret' => $intent->client_secret,
            'auto_succeed' => false,
        ];
    }

    public function processWebhook(string $payload, ?string $signature): array
    {
        $secret = (string) config('services.stripe.webhook_secret');
        try {
            $event = Webhook::constructEvent($payload, (string) $signature, $secret);
        } catch (SignatureVerificationException $e) {
            throw new DomainException('Assinatura do webhook inválida.', 401);
        } catch (\UnexpectedValueException $e) {
            throw new DomainException('Payload de webhook inválido.');
        }

        $object = $event->data->object;
        $orderId = isset($object->metadata->order_id) ? (int) $object->metadata->order_id : null;

        return [
            'event_id' => $event->id,
            'type' => $event->type,
            'order_id' => $orderId,
            'gateway_payment_id' => $object->id ?? null,
            'succeeded' => $event->type === 'payment_intent.succeeded',
        ];
    }

    public function refund(string $gatewayPaymentId, int $amountCents): bool
    {
        $client = new StripeClient((string) config('services.stripe.secret'));
        $client->refunds->create([
            'payment_intent' => $gatewayPaymentId,
            'amount' => $amountCents,
        ]);

        return true;
    }
}
