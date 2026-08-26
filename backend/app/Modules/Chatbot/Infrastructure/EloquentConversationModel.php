<?php

namespace App\Modules\Chatbot\Infrastructure;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EloquentConversationModel extends Model
{
    protected $table = 'conversations';

    protected $fillable = [
        'session_id',
        'user_id',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(EloquentMessageModel::class, 'conversation_id');
    }
}
