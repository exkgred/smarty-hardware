<?php

namespace App\Modules\Chatbot\Infrastructure;

use App\Modules\Chatbot\Domain\ConversationRepositoryInterface;

class EloquentConversationRepository implements ConversationRepositoryInterface
{
    public function findOrCreateBySession(string $sessionId, ?int $userId = null): int
    {
        $conversation = EloquentConversationModel::query()->firstOrCreate(
            ['session_id' => $sessionId],
            ['user_id' => $userId],
        );

        if ($userId !== null && $conversation->user_id === null) {
            $conversation->update(['user_id' => $userId]);
        }

        return (int) $conversation->id;
    }

    public function appendMessages(int $conversationId, array $messages): void
    {
        foreach ($messages as $message) {
            EloquentMessageModel::create([
                'conversation_id' => $conversationId,
                'role' => $message['role'],
                'content' => $message['content'],
            ]);
        }
    }

    public function history(string $sessionId, int $limit = 20): array
    {
        $conversation = EloquentConversationModel::query()->where('session_id', $sessionId)->first();
        if ($conversation === null) {
            return [];
        }

        return $conversation->messages()
            ->orderBy('id')
            ->limit($limit)
            ->get()
            ->map(fn (EloquentMessageModel $message) => [
                'role' => $message->role,
                'content' => $message->content,
            ])
            ->all();
    }
}
