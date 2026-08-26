<?php

namespace App\Http\Controllers;

use App\Modules\Payment\Application\UseCases\HandleWebhookUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentWebhookController extends Controller
{
    public function __construct(
        private readonly HandleWebhookUseCase $handleWebhook,
    ) {}

    public function stripe(Request $request): JsonResponse
    {
        $result = $this->handleWebhook->handle(
            $request->getContent(),
            $request->header('Stripe-Signature'),
        );

        return response()->json($result);
    }
}
