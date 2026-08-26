<?php

namespace App\Modules\Chatbot\Application\UseCases;

use App\Modules\Chatbot\Domain\KnowledgeDocumentEntity;
use App\Modules\Chatbot\Domain\KnowledgeRepositoryInterface;

class IngestKnowledgeUseCase
{
    public function __construct(
        private readonly KnowledgeRepositoryInterface $knowledge,
    ) {}

    public function handle(string $title, string $content, string $type = 'faq'): KnowledgeDocumentEntity
    {
        return $this->knowledge->save(new KnowledgeDocumentEntity(
            id: null,
            title: $title,
            type: $type,
            content: $content,
        ));
    }
}
