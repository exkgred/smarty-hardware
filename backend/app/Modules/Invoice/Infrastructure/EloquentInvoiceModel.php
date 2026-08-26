<?php

namespace App\Modules\Invoice\Infrastructure;

use App\Modules\Order\Infrastructure\EloquentOrderModel;
use App\Modules\User\Infrastructure\EloquentUserModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EloquentInvoiceModel extends Model
{
    protected $table = 'invoices';

    protected $fillable = [
        'number',
        'type',
        'order_id',
        'user_id',
        'customer_name',
        'customer_document',
        'payment_method',
        'total',
        'items',
        'notes',
    ];

    protected $casts = [
        'total' => 'float',
        'items' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(EloquentOrderModel::class, 'order_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(EloquentUserModel::class, 'user_id');
    }
}
