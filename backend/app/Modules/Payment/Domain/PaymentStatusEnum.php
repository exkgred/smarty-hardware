<?php

namespace App\Modules\Payment\Domain;

enum PaymentStatusEnum: string
{
    case PENDING = 'PENDING';
    case SUCCEEDED = 'SUCCEEDED';
    case FAILED = 'FAILED';
    case REFUNDED = 'REFUNDED';
}
