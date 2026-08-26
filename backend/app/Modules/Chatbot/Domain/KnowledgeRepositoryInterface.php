<?php

namespace App\Modules\Chatbot\Domain;

interface KnowledgeRepositoryInterface
{
    /**
     * @return array<int, KnowledgeDocumentEntity>
     */
    public function all(): array;

    public function save(KnowledgeDocumentEntity $document): KnowledgeDocumentEntity;

    public function delete(int $id): bool;
}
