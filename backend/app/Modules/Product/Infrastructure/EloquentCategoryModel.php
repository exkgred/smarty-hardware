<?php

namespace App\Modules\Product\Infrastructure;

use Illuminate\Database\Eloquent\Model;

class EloquentCategoryModel extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function products()
    {
        return $this->hasMany(EloquentProductModel::class, 'category_id');
    }
}
