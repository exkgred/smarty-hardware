<?php

namespace App\Modules\Invoice\Application\UseCases;

use App\Modules\Common\Domain\DomainException;
use App\Modules\Invoice\Domain\InvoiceRepositoryInterface;

class GetInvoiceUseCase
{
    public function __construct(private readonly InvoiceRepositoryInterface $invoices) {}

    /**
     * @return array<string, mixed>
     */
    public function handle(int $id): array
    {
        $invoice = $this->invoices->findById($id);
        if ($invoice === null) {
            throw new DomainException("Nota {$id} não encontrada.", 404);
        }

        return $invoice->toArray();
    }
}
