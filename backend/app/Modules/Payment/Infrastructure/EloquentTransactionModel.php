<?php

namespace App\Modules\Payment\Infrastructure;

use App\Modules\Order\Infrastructure\EloquentOrderModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EloquentTransactionModel extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'order_id',
        'gateway',
        'gateway_payment_id',
        'idempotency_key',
        'amount',
        'status',
        'payload',
    ];

    protected $casts = [
        'amount' => 'float',
        'payload' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(EloquentOrderModel::class, 'order_id');
    }
}
