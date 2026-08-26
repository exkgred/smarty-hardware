<?php

namespace App\Modules\Product\Infrastructure;

use Illuminate\Database\Eloquent\Model;

class EloquentProductModel extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'stock_quantity',
        'image_url',
        'is_active',
    ];

    protected $casts = [
        'price' => 'float',
        'stock_quantity' => 'integer',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(EloquentCategoryModel::class, 'category_id');
    }
}
