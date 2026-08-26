<?php

namespace App\Modules\Chatbot\Domain;

use App\Modules\Common\Domain\EntityInterface;

class KnowledgeDocumentEntity implements EntityInterface
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $title,
        public readonly string $type,
        public readonly string $content,
        public readonly ?string $createdAt = null,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'content' => $this->content,
            'created_at' => $this->createdAt,
        ];
    }
}
