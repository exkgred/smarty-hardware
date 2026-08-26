<?php

namespace App\Http\Controllers;

use App\Modules\Payment\Application\UseCases\ProcessPaymentUseCase;
use App\Modules\Payment\Domain\PaymentMethodEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        private readonly ProcessPaymentUseCase $processPayment,
    ) {}

    public function methods(): JsonResponse
    {
        return response()->json(PaymentMethodEnum::options());
    }

    public function intent(Request $request): JsonResponse
    {
        $data = $request->validate([
            'orderId' => ['required', 'integer'],
        ]);

        return response()->json(
            $this->processPayment->handle((int) $data['orderId'], (int) $request->user()->id)
        );
    }
}
