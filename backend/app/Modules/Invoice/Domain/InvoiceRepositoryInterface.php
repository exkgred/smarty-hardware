<?php

namespace App\Modules\Invoice\Domain;

interface InvoiceRepositoryInterface
{
    public function findById(int $id): ?InvoiceEntity;

    public function findByOrderId(int $orderId): ?InvoiceEntity;

    /**
     * @param  array<string, mixed>  $filters
     * @return array<int, array<string, mixed>>
     */
    public function list(array $filters = []): array;

    public function save(InvoiceEntity $invoice): InvoiceEntity;

    public function nextNumber(InvoiceTypeEnum $type): string;
}
