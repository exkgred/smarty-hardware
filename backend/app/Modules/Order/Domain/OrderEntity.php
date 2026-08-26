<?php

namespace App\Modules\Order\Domain;

use App\Modules\Common\Domain\AddressData;
use App\Modules\Common\Domain\EntityInterface;
use App\Modules\Payment\Domain\PaymentMethodEnum;

class OrderEntity implements EntityInterface
{
    /**
     * @param  array<int, OrderItemEntity>  $items
     * @param  array<string, mixed>|null  $paymentReceipt
     * @param  array<string, mixed>|null  $invoice
     */
    public function __construct(
        public readonly ?int $id,
        public readonly int $userId,
        public readonly OrderStatusEnum $status,
        public readonly float $subtotal,
        public readonly float $discountAmount,
        public readonly float $shippingCost,
        public readonly float $total,
        public readonly array $items = [],
        public readonly ?string $shippingName = null,
        public readonly ?string $shippingAddress = null,
        public readonly ?string $createdAt = null,
        public readonly ?PaymentMethodEnum $paymentMethod = null,
        public readonly OrderChannelEnum $channel = OrderChannelEnum::ONLINE,
        public readonly ?array $paymentReceipt = null,
        public readonly ?array $invoice = null,
        public readonly ?string $customerName = null,
        public readonly ?string $customerEmail = null,
        public readonly ?AddressData $shippingDetails = null,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'customer_name' => $this->customerName,
            'customer_email' => $this->customerEmail,
            'status' => $this->status->value,
            'subtotal' => $this->subtotal,
            'discount_amount' => $this->discountAmount,
            'shipping_cost' => $this->shippingCost,
            'total' => $this->total,
            'shipping_name' => $this->shippingName,
            'shipping_address' => $this->shippingAddress,
            'shipping_details' => $this->shippingDetails?->toArray(),
            'payment_method' => $this->paymentMethod?->value,
            'payment_method_label' => $this->paymentMethod?->label(),
            'channel' => $this->channel->value,
            'payment_receipt' => $this->paymentReceipt,
            'invoice' => $this->invoice,
            'items' => array_map(static fn (OrderItemEntity $item) => $item->toArray(), $this->items),
            'created_at' => $this->createdAt,
        ];
    }

    public function withStatus(OrderStatusEnum $status): self
    {
        return new self(
            id: $this->id,
            userId: $this->userId,
            status: $status,
            subtotal: $this->subtotal,
            discountAmount: $this->discountAmount,
            shippingCost: $this->shippingCost,
            total: $this->total,
            items: $this->items,
            shippingName: $this->shippingName,
            shippingAddress: $this->shippingAddress,
            createdAt: $this->createdAt,
            paymentMethod: $this->paymentMethod,
            channel: $this->channel,
            paymentReceipt: $this->paymentReceipt,
            invoice: $this->invoice,
            customerName: $this->customerName,
            customerEmail: $this->customerEmail,
            shippingDetails: $this->shippingDetails,
        );
    }
}
