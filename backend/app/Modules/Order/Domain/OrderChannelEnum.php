<?php

namespace App\Modules\Order\Domain;

enum OrderChannelEnum: string
{
    case ONLINE = 'ONLINE';
    case POS = 'POS';
}
