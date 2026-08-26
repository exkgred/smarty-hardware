<?php

namespace App\Modules\Order\Application\DTOs;

use App\Modules\Common\Domain\AddressData;
use App\Modules\Order\Domain\OrderChannelEnum;
use App\Modules\Payment\Domain\PaymentMethodEnum;

class CreateOrderDTO
{
    /**
     * @param  array<int, array{product_id:int, quantity:int}>  $items
     */
    public function __construct(
        public readonly int $userId,
        public readonly array $items,
        public readonly float $shippingCost = 0,
        public readonly ?string $shippingName = null,
        public readonly ?string $shippingAddress = null,
        public readonly PaymentMethodEnum $paymentMethod = PaymentMethodEnum::PIX,
        public readonly OrderChannelEnum $channel = OrderChannelEnum::ONLINE,
        public readonly float $discountAmount = 0,
        public readonly ?AddressData $shippingDetails = null,
    ) {}

    public static function fromArray(array $data, int $userId): self
    {
        $items = array_map(static function ($item) {
            return [
                'product_id' => (int) $item['product_id'],
                'quantity' => (int) $item['quantity'],
            ];
        }, $data['items'] ?? []);

        $method = PaymentMethodEnum::tryFrom(strtoupper((string) ($data['payment_method'] ?? $data['paymentMethod'] ?? 'PIX')))
            ?? PaymentMethodEnum::PIX;
        $channel = OrderChannelEnum::tryFrom(strtoupper((string) ($data['channel'] ?? 'ONLINE')))
            ?? OrderChannelEnum::ONLINE;

        $details = null;
        if (isset($data['address']) && is_array($data['address'])) {
            $details = AddressData::fromArray($data['address']);
        } elseif (isset($data['zip']) || isset($data['cep']) || isset($data['street'])) {
            $details = AddressData::fromArray($data);
        }

        $line = $details && ! $details->isEmpty()
            ? $details->line()
            : ($data['shipping_address'] ?? (is_string($data['address'] ?? null) ? $data['address'] : null));

        return new self(
            userId: $userId,
            items: $items,
            shippingCost: (float) ($data['shippingCost'] ?? $data['shipping_cost'] ?? 0),
            shippingName: $data['shipping_name'] ?? $data['name'] ?? null,
            shippingAddress: is_string($line) ? $line : null,
            paymentMethod: $method,
            channel: $channel,
            discountAmount: (float) ($data['discount'] ?? $data['discount_amount'] ?? 0),
            shippingDetails: $details,
        );
    }
}
