<?php

namespace App\Modules\Chatbot\Domain;

interface ConversationRepositoryInterface
{
    public function findOrCreateBySession(string $sessionId, ?int $userId = null): int;

    /**
     * @param  array<int, array{role: string, content: string}>  $messages
     */
    public function appendMessages(int $conversationId, array $messages): void;

    /**
     * @return array<int, array{role: string, content: string}>
     */
    public function history(string $sessionId, int $limit = 20): array;
}
