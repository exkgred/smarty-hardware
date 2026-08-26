<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PosSaleRequest;
use App\Modules\Invoice\Application\UseCases\GetInvoiceUseCase;
use App\Modules\Invoice\Application\UseCases\IssueSaleInvoiceUseCase;
use App\Modules\Invoice\Application\UseCases\ListInvoicesUseCase;
use App\Modules\Order\Application\UseCases\CreatePosSaleUseCase;
use App\Modules\User\Application\UseCases\ListCustomersUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminSaleController extends Controller
{
    public function __construct(
        private readonly CreatePosSaleUseCase $createPosSale,
        private readonly ListCustomersUseCase $listCustomers,
        private readonly ListInvoicesUseCase $listInvoices,
        private readonly GetInvoiceUseCase $getInvoice,
        private readonly IssueSaleInvoiceUseCase $issueInvoice,
    ) {}

    public function store(PosSaleRequest $request): JsonResponse
    {
        $order = $this->createPosSale->handle($request->validated(), (int) $request->user()->id);

        return response()->json($order, 201);
    }

    public function customers(): JsonResponse
    {
        return response()->json($this->listCustomers->handle());
    }

    public function invoices(Request $request): JsonResponse
    {
        return response()->json($this->listInvoices->handle([
            'type' => $request->query('type'),
        ]));
    }

    public function showInvoice(int $id): JsonResponse
    {
        return response()->json($this->getInvoice->handle($id));
    }

    public function issueInvoice(int $id): JsonResponse
    {
        return response()->json($this->issueInvoice->handle($id)->toArray(), 201);
    }
}
