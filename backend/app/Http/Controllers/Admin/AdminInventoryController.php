<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdjustStockRequest;
use App\Http\Requests\StockEntryRequest;
use App\Modules\Inventory\Application\DTOs\AdjustStockDTO;
use App\Modules\Inventory\Application\UseCases\AdjustStockUseCase;
use App\Modules\Inventory\Application\UseCases\GetStockAlertsUseCase;
use App\Modules\Inventory\Application\UseCases\ListRecentMovementsUseCase;
use App\Modules\Inventory\Application\UseCases\ListStockMovementsUseCase;
use App\Modules\Inventory\Application\UseCases\RegisterStockEntryUseCase;
use Illuminate\Http\JsonResponse;

class AdminInventoryController extends Controller
{
    public function __construct(
        private readonly ListStockMovementsUseCase $listMovements,
        private readonly ListRecentMovementsUseCase $listRecent,
        private readonly AdjustStockUseCase $adjustStock,
        private readonly GetStockAlertsUseCase $getAlerts,
        private readonly RegisterStockEntryUseCase $registerEntry,
    ) {}

    public function recent(): JsonResponse
    {
        return response()->json($this->listRecent->handle());
    }

    public function movements(int $productId): JsonResponse
    {
        return response()->json($this->listMovements->handle($productId));
    }

    public function entry(StockEntryRequest $request): JsonResponse
    {
        $data = $request->validated();

        return response()->json(
            $this->registerEntry->handle(
                $data['items'],
                $data['supplier'],
                $data['document_number'] ?? null,
                (int) $request->user()->id,
            ),
            201,
        );
    }

    public function adjust(AdjustStockRequest $request): JsonResponse
    {
        $movement = $this->adjustStock->handle(
            AdjustStockDTO::fromArray($request->validated()),
            (int) $request->user()->id,
        );

        return response()->json($movement->toArray(), 201);
    }

    public function alerts(): JsonResponse
    {
        return response()->json($this->getAlerts->handle());
    }
}
