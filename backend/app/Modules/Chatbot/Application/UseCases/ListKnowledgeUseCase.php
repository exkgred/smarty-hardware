<?php

namespace App\Modules\Chatbot\Application\UseCases;

use App\Modules\Chatbot\Domain\KnowledgeRepositoryInterface;

class ListKnowledgeUseCase
{
    public function __construct(
        private readonly KnowledgeRepositoryInterface $knowledge,
    ) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function handle(): array
    {
        return array_map(
            static fn ($document) => $document->toArray(),
            $this->knowledge->all(),
        );
    }
}
