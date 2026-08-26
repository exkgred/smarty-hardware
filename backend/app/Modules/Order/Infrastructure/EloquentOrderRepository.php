<?php

namespace App\Modules\Order\Infrastructure;

use App\Modules\Common\Domain\AddressData;
use App\Modules\Order\Domain\OrderChannelEnum;
use App\Modules\Order\Domain\OrderEntity;
use App\Modules\Order\Domain\OrderItemEntity;
use App\Modules\Order\Domain\OrderRepositoryInterface;
use App\Modules\Order\Domain\OrderStatusEnum;
use App\Modules\Payment\Domain\PaymentMethodEnum;
use App\Modules\Payment\Domain\PaymentStatusEnum;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function findById(int $id): ?OrderEntity
    {
        $record = EloquentOrderModel::with(['items', 'user', 'invoice', 'transactions'])->find($id);

        return $record ? $this->toEntity($record) : null;
    }

    public function findByUserId(int $userId): array
    {
        return EloquentOrderModel::with(['items', 'user', 'invoice', 'transactions'])
            ->where('user_id', $userId)
            ->orderByDesc('id')
            ->get()
            ->map(fn (EloquentOrderModel $model) => $this->toEntity($model)->toArray())
            ->all();
    }

    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = EloquentOrderModel::query()->with(['items', 'user', 'invoice', 'transactions']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['channel'])) {
            $query->where('channel', $filters['channel']);
        }

        return $query
            ->orderByDesc('id')
            ->paginate($perPage)
            ->through(fn (EloquentOrderModel $model) => $this->toEntity($model)->toArray());
    }

    public function save(OrderEntity $order): OrderEntity
    {
        return DB::transaction(function () use ($order) {
            $data = [
                'user_id' => $order->userId,
                'status' => $order->status->value,
                'subtotal' => $order->subtotal,
                'discount_amount' => $order->discountAmount,
                'shipping_cost' => $order->shippingCost,
                'total' => $order->total,
                'shipping_name' => $order->shippingName,
                'shipping_address' => $order->shippingAddress,
                'payment_method' => $order->paymentMethod?->value,
                'channel' => $order->channel->value,
                'shipping_details' => $order->shippingDetails?->toArray(),
            ];

            if ($order->id) {
                $record = EloquentOrderModel::findOrFail($order->id);
                $record->update($data);
            } else {
                $record = EloquentOrderModel::create($data);
                foreach ($order->items as $item) {
                    $record->items()->create([
                        'product_id' => $item->productId,
                        'product_name' => $item->productName,
                        'unit_price' => $item->unitPrice,
                        'quantity' => $item->quantity,
                        'subtotal' => $item->subtotal,
                    ]);
                }
            }

            return $this->toEntity($record->fresh(['items', 'user', 'invoice', 'transactions']));
        });
    }

    public function countAll(): int
    {
        return EloquentOrderModel::query()->count();
    }

    public function countByStatus(string $status): int
    {
        return EloquentOrderModel::query()->where('status', $status)->count();
    }

    public function sumPaidTotal(): float
    {
        return (float) EloquentOrderModel::query()->where('status', 'PAID')->sum('total');
    }

    private function toEntity(EloquentOrderModel $model): OrderEntity
    {
        $items = $model->items->map(fn (EloquentOrderItemModel $item) => new OrderItemEntity(
            id: $item->id,
            productId: $item->product_id,
            productName: $item->product_name,
            unitPrice: (float) $item->unit_price,
            quantity: (int) $item->quantity,
            subtotal: (float) $item->subtotal,
        ))->all();

        $paidTx = $model->relationLoaded('transactions')
            ? $model->transactions->firstWhere('status', PaymentStatusEnum::SUCCEEDED->value)
            : null;

        $invoice = $model->relationLoaded('invoice') && $model->invoice
            ? [
                'id' => $model->invoice->id,
                'number' => $model->invoice->number,
                'type' => $model->invoice->type,
                'total' => (float) $model->invoice->total,
                'created_at' => $model->invoice->created_at?->toIso8601String(),
            ]
            : null;

        return new OrderEntity(
            id: $model->id,
            userId: $model->user_id,
            status: OrderStatusEnum::from($model->status),
            subtotal: (float) $model->subtotal,
            discountAmount: (float) $model->discount_amount,
            shippingCost: (float) $model->shipping_cost,
            total: (float) $model->total,
            items: $items,
            shippingName: $model->shipping_name,
            shippingAddress: $model->shipping_address,
            createdAt: $model->created_at?->toIso8601String(),
            paymentMethod: PaymentMethodEnum::tryFrom((string) $model->payment_method),
            channel: OrderChannelEnum::tryFrom((string) $model->channel) ?? OrderChannelEnum::ONLINE,
            paymentReceipt: is_array($paidTx?->payload) ? ($paidTx->payload['receipt'] ?? $paidTx->payload) : null,
            invoice: $invoice,
            customerName: $model->user?->name,
            customerEmail: $model->user?->email,
            shippingDetails: is_array($model->shipping_details) ? AddressData::fromArray($model->shipping_details) : null,
        );
    }
}
