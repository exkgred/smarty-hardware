<?php

namespace App\Modules\Invoice\Application\UseCases;

use App\Modules\Invoice\Domain\InvoiceRepositoryInterface;

class ListInvoicesUseCase
{
    public function __construct(private readonly InvoiceRepositoryInterface $invoices) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function handle(array $filters = []): array
    {
        return $this->invoices->list($filters);
    }
}
