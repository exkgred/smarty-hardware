<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveCardRequest;
use App\Modules\Payment\Application\UseCases\DeleteSavedCardUseCase;
use App\Modules\Payment\Application\UseCases\ListSavedCardsUseCase;
use App\Modules\Payment\Application\UseCases\SaveCardUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SavedCardController extends Controller
{
    public function __construct(
        private readonly ListSavedCardsUseCase $listCards,
        private readonly SaveCardUseCase $saveCard,
        private readonly DeleteSavedCardUseCase $deleteCard,
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->listCards->handle((int) $request->user()->id));
    }

    public function store(SaveCardRequest $request): JsonResponse
    {
        $card = $this->saveCard->handle((int) $request->user()->id, $request->validated());

        return response()->json($card->toArray(), 201);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->deleteCard->handle($id, (int) $request->user()->id);

        return response()->json(['message' => 'Cartão removido.']);
    }
}
