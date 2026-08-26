<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendChatMessageRequest;
use App\Modules\Chatbot\Application\UseCases\GetConversationHistoryUseCase;
use App\Modules\Chatbot\Application\UseCases\SendMessageUseCase;
use Illuminate\Http\JsonResponse;

class ChatbotController extends Controller
{
    public function __construct(
        private readonly SendMessageUseCase $sendMessage,
        private readonly GetConversationHistoryUseCase $getHistory,
    ) {}

    public function send(SendChatMessageRequest $request): JsonResponse
    {
        $userId = $request->user()?->id;

        return response()->json(
            $this->sendMessage->handle(
                $request->validated('sessionId'),
                $request->validated('message'),
                $userId ? (int) $userId : null,
            )
        );
    }

    public function history(string $sessionId): JsonResponse
    {
        return response()->json($this->getHistory->handle($sessionId));
    }
}
