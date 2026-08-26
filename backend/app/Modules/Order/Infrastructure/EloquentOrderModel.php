<?php

namespace App\Modules\Order\Infrastructure;

use App\Modules\Invoice\Infrastructure\EloquentInvoiceModel;
use App\Modules\Payment\Infrastructure\EloquentTransactionModel;
use App\Modules\User\Infrastructure\EloquentUserModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EloquentOrderModel extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'status',
        'subtotal',
        'discount_amount',
        'shipping_cost',
        'total',
        'shipping_name',
        'shipping_address',
        'payment_method',
        'channel',
        'shipping_details',
    ];

    protected $casts = [
        'subtotal' => 'float',
        'discount_amount' => 'float',
        'shipping_cost' => 'float',
        'total' => 'float',
        'shipping_details' => 'array',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(EloquentOrderItemModel::class, 'order_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(EloquentUserModel::class, 'user_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(EloquentTransactionModel::class, 'order_id');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(EloquentInvoiceModel::class, 'order_id');
    }
}
