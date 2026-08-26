<?php

namespace App\Modules\Inventory\Infrastructure;

use App\Modules\Common\Domain\DomainException;
use App\Modules\Inventory\Domain\InventoryServiceInterface;
use App\Modules\Inventory\Domain\StockMovementEntity;
use App\Modules\Inventory\Domain\StockMovementTypeEnum;
use App\Modules\Product\Infrastructure\EloquentProductModel;
use Illuminate\Support\Facades\DB;

class EloquentInventoryService implements InventoryServiceInterface
{
    public function adjust(int $productId, StockMovementTypeEnum $type, int $quantity, string $reason, ?int $userId = null, ?int $orderId = null, ?string $documentNumber = null): StockMovementEntity
    {
        if ($quantity <= 0) {
            throw new DomainException('A quantidade deve ser maior que zero.');
        }

        if (! in_array($type, [StockMovementTypeEnum::IN, StockMovementTypeEnum::OUT], true)) {
            throw new DomainException('Tipo de ajuste inválido.');
        }

        return $this->apply($productId, $type, $quantity, $reason, $userId, $orderId, $documentNumber);
    }

    public function reserve(int $productId, int $quantity, int $orderId): StockMovementEntity
    {
        return $this->apply(
            $productId,
            StockMovementTypeEnum::RESERVE,
            $quantity,
            'Reserva de estoque para pedido #'.$orderId,
            null,
            $orderId,
        );
    }

    public function release(int $productId, int $quantity, int $orderId): StockMovementEntity
    {
        return $this->apply(
            $productId,
            StockMovementTypeEnum::RELEASE,
            $quantity,
            'Liberação de estoque do pedido #'.$orderId,
            null,
            $orderId,
        );
    }

    public function getMovements(int $productId): array
    {
        return EloquentStockMovementModel::query()
            ->with('product')
            ->where('product_id', $productId)
            ->orderByDesc('id')
            ->get()
            ->map(fn (EloquentStockMovementModel $model) => $this->toEntity($model)->toArray())
            ->all();
    }

    public function getRecentMovements(int $limit = 50): array
    {
        return EloquentStockMovementModel::query()
            ->with('product')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(fn (EloquentStockMovementModel $model) => $this->toEntity($model)->toArray())
            ->all();
    }

    public function getLowStockAlerts(int $threshold = 5): array
    {
        return EloquentProductModel::query()
            ->where('is_active', true)
            ->where(function ($q) use ($threshold) {
                $q->whereColumn('stock_quantity', '<=', 'stock_alert_threshold')
                    ->orWhere('stock_quantity', '<=', $threshold);
            })
            ->get()
            ->map(fn (EloquentProductModel $product) => [
                'id' => $product->id,
                'product_name' => $product->name,
                'stock_quantity' => (int) $product->stock_quantity,
            ])
            ->values()
            ->all();
    }

    private function apply(
        int $productId,
        StockMovementTypeEnum $type,
        int $quantity,
        string $reason,
        ?int $userId,
        ?int $orderId,
        ?string $documentNumber = null,
    ): StockMovementEntity {
        return DB::transaction(function () use ($productId, $type, $quantity, $reason, $userId, $orderId, $documentNumber) {
            /** @var EloquentProductModel $product */
            $product = EloquentProductModel::query()->lockForUpdate()->find($productId);
            if ($product === null) {
                throw new DomainException("Produto {$productId} não encontrado.", 404);
            }

            $delta = match ($type) {
                StockMovementTypeEnum::IN, StockMovementTypeEnum::RELEASE => $quantity,
                StockMovementTypeEnum::OUT, StockMovementTypeEnum::RESERVE => -$quantity,
            };

            $newStock = $product->stock_quantity + $delta;
            if ($newStock < 0) {
                throw new DomainException("Estoque insuficiente para {$product->name}.");
            }

            $product->stock_quantity = $newStock;
            $product->save();

            $movement = EloquentStockMovementModel::create([
                'product_id' => $productId,
                'type' => $type->value,
                'quantity' => $quantity,
                'reason' => $reason,
                'document_number' => $documentNumber,
                'user_id' => $userId,
                'order_id' => $orderId,
            ]);

            return $this->toEntity($movement->load('product'));
        });
    }

    private function toEntity(EloquentStockMovementModel $model): StockMovementEntity
    {
        return new StockMovementEntity(
            id: $model->id,
            productId: $model->product_id,
            type: StockMovementTypeEnum::from($model->type),
            quantity: (int) $model->quantity,
            reason: $model->reason,
            userId: $model->user_id,
            orderId: $model->order_id,
            createdAt: $model->created_at?->toIso8601String(),
            documentNumber: $model->document_number,
            productName: $model->product?->name,
        );
    }
}
