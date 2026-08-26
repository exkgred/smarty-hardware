<?php

namespace App\Modules\Payment\Infrastructure;

use App\Modules\User\Infrastructure\EloquentUserModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EloquentSavedCardModel extends Model
{
    protected $table = 'saved_cards';

    protected $fillable = [
        'user_id',
        'brand',
        'last_four',
        'holder_name',
        'exp_month',
        'exp_year',
        'token',
        'is_default',
    ];

    protected $hidden = [
        'token',
    ];

    protected $casts = [
        'exp_month' => 'integer',
        'exp_year' => 'integer',
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(EloquentUserModel::class, 'user_id');
    }
}
