<?php

namespace App\Modules\Chatbot\Application\UseCases;

use App\Modules\Chatbot\Domain\KnowledgeRepositoryInterface;

class DeleteKnowledgeUseCase
{
    public function __construct(
        private readonly KnowledgeRepositoryInterface $knowledge,
    ) {}

    public function handle(int $id): bool
    {
        return $this->knowledge->delete($id);
    }
}
