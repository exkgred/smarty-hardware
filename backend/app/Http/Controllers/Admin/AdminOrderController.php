<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Order\Application\UseCases\GetOrderUseCase;
use App\Modules\Order\Application\UseCases\ListOrdersUseCase;
use App\Modules\Order\Application\UseCases\UpdateOrderStatusUseCase;
use App\Modules\Payment\Application\UseCases\RefundUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function __construct(
        private readonly ListOrdersUseCase $listOrders,
        private readonly GetOrderUseCase $getOrder,
        private readonly UpdateOrderStatusUseCase $updateStatus,
        private readonly RefundUseCase $refund,
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->listOrders->handle(
                ['status' => $request->query('status')],
                (int) $request->query('per_page', 20),
            )
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getOrder->handle($id, null, true)->toArray());
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string'],
        ]);

        return response()->json(
            $this->updateStatus->handle($id, $data['status'])->toArray()
        );
    }

    public function refund(int $id): JsonResponse
    {
        return response()->json($this->refund->handle($id));
    }
}
