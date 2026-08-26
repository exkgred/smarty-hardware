<?php

namespace App\Modules\Payment\Infrastructure;

use App\Modules\Payment\Domain\PaymentGatewayInterface;
use App\Modules\Payment\Domain\PaymentMethodEnum;
use Illuminate\Support\Str;

class FakePaymentGateway implements PaymentGatewayInterface
{
    public function createPaymentIntent(int $orderId, int $amountCents, string $currency = 'brl', array $options = []): array
    {
        $method = PaymentMethodEnum::tryFrom(strtoupper((string) ($options['payment_method'] ?? 'PIX')))
            ?? PaymentMethodEnum::PIX;
        $reais = number_format($amountCents / 100, 2, ',', '.');

        return [
            'gateway' => 'sandbox',
            'gateway_payment_id' => 'fake_'.$method->value.'_'.Str::uuid()->toString(),
            'client_secret' => null,
            'auto_succeed' => true,
            'payment_method' => $method->value,
            'receipt' => $this->receipt($method, $reais, $orderId, $options),
        ];
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    private function receipt(PaymentMethodEnum $method, string $reais, int $orderId, array $options = []): array
    {
        $lastFour = (string) ($options['last_four'] ?? '4242');
        $brand = strtoupper((string) ($options['brand'] ?? 'card'));

        return match ($method) {
            PaymentMethodEnum::PIX => [
                'label' => $method->label(),
                'status' => 'Aprovado (sandbox)',
                'instructions' => "PIX simulado de R$ {$reais} confirmado na hora.",
                'copy_paste' => '00020126580014BR.GOV.BCB.PIX0136smarty@smartyhardware.test520400005303986540'.$orderId.'5802BR5925SMARTY HARDWARE LTDA6009SAO PAULO62070503***6304ABCD',
            ],
            PaymentMethodEnum::CREDIT_CARD => [
                'label' => $method->label(),
                'status' => 'Autorizado (sandbox)',
                'instructions' => "Crédito em 1x de R$ {$reais} autorizado no cartão {$brand} **** {$lastFour}.",
                'auth_code' => 'AUTH-'.str_pad((string) $orderId, 6, '0', STR_PAD_LEFT),
                'last_four' => $lastFour,
                'brand' => strtolower((string) ($options['brand'] ?? 'visa')),
            ],
            PaymentMethodEnum::DEBIT_CARD => [
                'label' => $method->label(),
                'status' => 'Aprovado (sandbox)',
                'instructions' => "Débito de R$ {$reais} aprovado no cartão {$brand} **** {$lastFour}.",
                'auth_code' => 'DEB-'.str_pad((string) $orderId, 6, '0', STR_PAD_LEFT),
                'last_four' => $lastFour,
                'brand' => strtolower((string) ($options['brand'] ?? 'card')),
            ],
            PaymentMethodEnum::BOLETO => [
                'label' => $method->label(),
                'status' => 'Registrado (sandbox)',
                'instructions' => "Boleto sandbox liquidado automaticamente. Valor R$ {$reais}.",
                'digitable_line' => '23793.38128 60007.827136 95000.063305 1 84440000'.str_pad((string) ($orderId * 100), 8, '0', STR_PAD_LEFT),
            ],
            PaymentMethodEnum::CASH => [
                'label' => $method->label(),
                'status' => 'Recebido no balcão',
                'instructions' => "Pagamento em dinheiro de R$ {$reais} registrado na venda #{$orderId}.",
            ],
            PaymentMethodEnum::TRANSFER => [
                'label' => $method->label(),
                'status' => 'Creditado (sandbox)',
                'instructions' => "TED/DOC sandbox para Smarty Hardware — R$ {$reais}.",
                'bank' => '341 — Itaú',
                'agency' => '0001',
                'account' => '12345-6',
            ],
        };
    }

    public function processWebhook(string $payload, ?string $signature): array
    {
        $data = json_decode($payload, true) ?: [];

        return [
            'event_id' => (string) ($data['id'] ?? 'fake_event_'.Str::uuid()),
            'type' => (string) ($data['type'] ?? 'payment_intent.succeeded'),
            'order_id' => isset($data['order_id']) ? (int) $data['order_id'] : null,
            'gateway_payment_id' => $data['gateway_payment_id'] ?? null,
            'succeeded' => true,
        ];
    }

    public function refund(string $gatewayPaymentId, int $amountCents): bool
    {
        return true;
    }
}
