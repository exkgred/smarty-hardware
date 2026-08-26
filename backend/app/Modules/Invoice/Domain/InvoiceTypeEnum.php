<?php

namespace App\Modules\Invoice\Domain;

enum InvoiceTypeEnum: string
{
    case SALE = 'SALE';
    case ENTRY = 'ENTRY';
}
