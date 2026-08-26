<?php

namespace App\Modules\Chatbot\Infrastructure;

use Illuminate\Database\Eloquent\Model;

class EloquentKnowledgeModel extends Model
{
    protected $table = 'knowledge_documents';

    protected $fillable = [
        'title',
        'type',
        'content',
    ];
}
