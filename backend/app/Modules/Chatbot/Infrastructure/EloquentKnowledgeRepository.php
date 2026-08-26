<?php

namespace App\Modules\Chatbot\Infrastructure;

use App\Modules\Chatbot\Domain\KnowledgeDocumentEntity;
use App\Modules\Chatbot\Domain\KnowledgeRepositoryInterface;

class EloquentKnowledgeRepository implements KnowledgeRepositoryInterface
{
    public function all(): array
    {
        return EloquentKnowledgeModel::query()
            ->orderByDesc('id')
            ->get()
            ->map(fn (EloquentKnowledgeModel $model) => $this->toEntity($model))
            ->all();
    }

    public function save(KnowledgeDocumentEntity $document): KnowledgeDocumentEntity
    {
        $record = EloquentKnowledgeModel::create([
            'title' => $document->title,
            'type' => $document->type,
            'content' => $document->content,
        ]);

        return $this->toEntity($record);
    }

    public function delete(int $id): bool
    {
        return (bool) EloquentKnowledgeModel::destroy($id);
    }

    private function toEntity(EloquentKnowledgeModel $model): KnowledgeDocumentEntity
    {
        return new KnowledgeDocumentEntity(
            id: $model->id,
            title: $model->title,
            type: $model->type,
            content: $model->content,
            createdAt: $model->created_at?->toIso8601String(),
        );
    }
}
