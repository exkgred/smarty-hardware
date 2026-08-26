<?php

namespace App\Modules\Chatbot\Infrastructure;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EloquentMessageModel extends Model
{
    protected $table = 'messages';

    protected $fillable = [
        'conversation_id',
        'role',
        'content',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(EloquentConversationModel::class, 'conversation_id');
    }
}
