<?php

namespace App\Modules\Invoice\Infrastructure;

use App\Modules\Invoice\Domain\InvoiceEntity;
use App\Modules\Invoice\Domain\InvoiceRepositoryInterface;
use App\Modules\Invoice\Domain\InvoiceTypeEnum;

class EloquentInvoiceRepository implements InvoiceRepositoryInterface
{
    public function findById(int $id): ?InvoiceEntity
    {
        $record = EloquentInvoiceModel::query()->find($id);

        return $record ? $this->toEntity($record) : null;
    }

    public function findByOrderId(int $orderId): ?InvoiceEntity
    {
        $record = EloquentInvoiceModel::query()->where('order_id', $orderId)->first();

        return $record ? $this->toEntity($record) : null;
    }

    public function list(array $filters = []): array
    {
        $query = EloquentInvoiceModel::query()->orderByDesc('id');

        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query
            ->limit(100)
            ->get()
            ->map(fn (EloquentInvoiceModel $model) => $this->toEntity($model)->toArray())
            ->all();
    }

    public function save(InvoiceEntity $invoice): InvoiceEntity
    {
        $data = [
            'number' => $invoice->number,
            'type' => $invoice->type->value,
            'order_id' => $invoice->orderId,
            'user_id' => $invoice->userId,
            'customer_name' => $invoice->customerName,
            'customer_document' => $invoice->customerDocument,
            'payment_method' => $invoice->paymentMethod,
            'total' => $invoice->total,
            'items' => $invoice->items,
            'notes' => $invoice->notes,
        ];

        if ($invoice->id) {
            $record = EloquentInvoiceModel::query()->findOrFail($invoice->id);
            $record->update($data);
        } else {
            $record = EloquentInvoiceModel::query()->create($data);
        }

        return $this->toEntity($record->fresh());
    }

    public function nextNumber(InvoiceTypeEnum $type): string
    {
        $prefix = $type === InvoiceTypeEnum::SALE ? 'SMTY-S' : 'SMTY-E';
        $count = EloquentInvoiceModel::query()->where('type', $type->value)->count() + 1;

        return $prefix.'-'.now()->format('Y').'-'.str_pad((string) $count, 6, '0', STR_PAD_LEFT);
    }

    private function toEntity(EloquentInvoiceModel $model): InvoiceEntity
    {
        return new InvoiceEntity(
            id: $model->id,
            number: $model->number,
            type: InvoiceTypeEnum::from($model->type),
            customerName: $model->customer_name,
            total: (float) $model->total,
            items: is_array($model->items) ? $model->items : [],
            orderId: $model->order_id,
            userId: $model->user_id,
            customerDocument: $model->customer_document,
            paymentMethod: $model->payment_method,
            notes: $model->notes,
            createdAt: $model->created_at?->toIso8601String(),
        );
    }
}
