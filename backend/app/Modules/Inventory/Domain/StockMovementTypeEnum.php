<?php

namespace App\Modules\Inventory\Domain;

enum StockMovementTypeEnum: string
{
    case IN = 'IN';
    case OUT = 'OUT';
    case RESERVE = 'RESERVE';
    case RELEASE = 'RELEASE';
}
