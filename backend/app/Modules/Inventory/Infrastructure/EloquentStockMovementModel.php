<?php

namespace App\Modules\Inventory\Infrastructure;

use App\Modules\Product\Infrastructure\EloquentProductModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EloquentStockMovementModel extends Model
{
    protected $table = 'inventory_movements';

    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'reason',
        'document_number',
        'user_id',
        'order_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(EloquentProductModel::class, 'product_id');
    }
}
