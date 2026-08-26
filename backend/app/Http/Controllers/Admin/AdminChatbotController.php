<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKnowledgeRequest;
use App\Modules\Chatbot\Application\UseCases\DeleteKnowledgeUseCase;
use App\Modules\Chatbot\Application\UseCases\IngestKnowledgeUseCase;
use App\Modules\Chatbot\Application\UseCases\ListKnowledgeUseCase;
use Illuminate\Http\JsonResponse;

class AdminChatbotController extends Controller
{
    public function __construct(
        private readonly ListKnowledgeUseCase $listKnowledge,
        private readonly IngestKnowledgeUseCase $ingestKnowledge,
        private readonly DeleteKnowledgeUseCase $deleteKnowledge,
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->listKnowledge->handle());
    }

    public function store(StoreKnowledgeRequest $request): JsonResponse
    {
        $document = $this->ingestKnowledge->handle(
            $request->validated('title'),
            $request->validated('content'),
            $request->validated('type') ?? 'faq',
        );

        return response()->json($document->toArray(), 201);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteKnowledge->handle($id);

        return response()->json(['message' => 'Documento excluído.']);
    }
}
