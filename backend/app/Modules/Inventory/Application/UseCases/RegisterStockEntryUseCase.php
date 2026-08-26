<?php

namespace App\Modules\Inventory\Application\UseCases;

use App\Modules\Common\Domain\DomainException;
use App\Modules\Inventory\Domain\InventoryServiceInterface;
use App\Modules\Inventory\Domain\StockMovementTypeEnum;
use App\Modules\Invoice\Domain\InvoiceEntity;
use App\Modules\Invoice\Domain\InvoiceRepositoryInterface;
use App\Modules\Invoice\Domain\InvoiceTypeEnum;
use App\Modules\Product\Domain\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;

class RegisterStockEntryUseCase
{
    public function __construct(
        private readonly InventoryServiceInterface $inventory,
        private readonly ProductRepositoryInterface $products,
        private readonly InvoiceRepositoryInterface $invoices,
    ) {}

    /**
     * @param  array<int, array{product_id:int, quantity:int, unit_cost?:float}>  $items
     * @return array{invoice: array<string, mixed>, movements: array<int, array<string, mixed>>}
     */
    public function handle(array $items, string $supplier, ?string $documentNumber, ?int $userId): array
    {
        if ($items === []) {
            throw new DomainException('Informe ao menos um item na nota de entrada.');
        }

        return DB::transaction(function () use ($items, $supplier, $documentNumber, $userId) {
            $invoiceItems = [];
            $movements = [];
            $total = 0.0;
            $number = $documentNumber !== null && $documentNumber !== ''
                ? $documentNumber
                : $this->invoices->nextNumber(InvoiceTypeEnum::ENTRY);

            foreach ($items as $item) {
                $product = $this->products->findById((int) $item['product_id']);
                if ($product === null) {
                    throw new DomainException('Produto '.$item['product_id'].' não encontrado.', 404);
                }
                $qty = (int) $item['quantity'];
                $cost = (float) ($item['unit_cost'] ?? $product->price);
                $subtotal = $cost * $qty;
                $total += $subtotal;

                $movement = $this->inventory->adjust(
                    (int) $product->id,
                    StockMovementTypeEnum::IN,
                    $qty,
                    'Entrada de mercadoria — '.$supplier,
                    $userId,
                    null,
                    $number,
                );
                $movements[] = $movement->toArray();
                $invoiceItems[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'quantity' => $qty,
                    'unit_price' => $cost,
                    'subtotal' => $subtotal,
                ];
            }

            $invoice = $this->invoices->save(new InvoiceEntity(
                id: null,
                number: $number,
                type: InvoiceTypeEnum::ENTRY,
                customerName: $supplier,
                total: $total,
                items: $invoiceItems,
                notes: 'Nota de entrada de estoque',
            ));

            return [
                'invoice' => $invoice->toArray(),
                'movements' => $movements,
            ];
        });
    }
}
