<?php

namespace App\Modules\Chatbot\Application\UseCases;

use App\Modules\Chatbot\Domain\ConversationRepositoryInterface;

class GetConversationHistoryUseCase
{
    public function __construct(
        private readonly ConversationRepositoryInterface $conversations,
    ) {}

    /**
     * @return array<int, array{role: string, content: string}>
     */
    public function handle(string $sessionId): array
    {
        return $this->conversations->history($sessionId);
    }
}
