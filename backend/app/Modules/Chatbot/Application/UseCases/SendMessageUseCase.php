<?php

namespace App\Modules\Chatbot\Application\UseCases;

use App\Modules\Chatbot\Domain\AIServiceInterface;
use App\Modules\Chatbot\Domain\ConversationRepositoryInterface;
use App\Modules\Chatbot\Domain\KnowledgeRepositoryInterface;
use App\Modules\Chatbot\Infrastructure\SimilaritySearchService;
use App\Modules\Product\Domain\ProductRepositoryInterface;

class SendMessageUseCase
{
    public function __construct(
        private readonly ConversationRepositoryInterface $conversations,
        private readonly KnowledgeRepositoryInterface $knowledge,
        private readonly ProductRepositoryInterface $products,
        private readonly SimilaritySearchService $search,
        private readonly AIServiceInterface $ai,
    ) {}

    /**
     * @return array{message: string}
     */
    public function handle(string $sessionId, string $message, ?int $userId = null): array
    {
        $conversationId = $this->conversations->findOrCreateBySession($sessionId, $userId);
        $history = $this->conversations->history($sessionId);
        $chunks = $this->search->relevantChunks(
            $message,
            $this->knowledge->all(),
            $this->products->listActive(200),
        );
        $reply = $this->ai->reply($message, $history, $chunks);

        $this->conversations->appendMessages($conversationId, [
            ['role' => 'user', 'content' => $message],
            ['role' => 'assistant', 'content' => $reply],
        ]);

        return ['message' => $reply];
    }
}
