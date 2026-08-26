<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Modules\Common\Domain\DomainException;
use App\Modules\Order\Application\DTOs\CreateOrderDTO;
use App\Modules\Order\Application\UseCases\CreateOrderUseCase;
use App\Modules\Order\Application\UseCases\GetOrderUseCase;
use App\Modules\Order\Application\UseCases\ListMyOrdersUseCase;
use App\Modules\Payment\Application\UseCases\ProcessPaymentUseCase;
use App\Modules\Payment\Application\UseCases\SaveCardUseCase;
use App\Modules\Payment\Domain\CardTokenizer;
use App\Modules\Payment\Domain\PaymentMethodEnum;
use App\Modules\Payment\Domain\SavedCardRepositoryInterface;
use App\Modules\User\Application\UseCases\UpdateProfileUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartOrderController extends Controller
{
    public function __construct(
        private readonly CreateOrderUseCase $createOrder,
        private readonly ListMyOrdersUseCase $listMyOrders,
        private readonly GetOrderUseCase $getOrder,
        private readonly ProcessPaymentUseCase $processPayment,
        private readonly SaveCardUseCase $saveCard,
        private readonly SavedCardRepositoryInterface $cards,
        private readonly UpdateProfileUseCase $updateProfile,
    ) {}

    public function checkout(CheckoutRequest $request): JsonResponse
    {
        $userId = (int) $request->user()->id;
        $data = $request->validated();

        if (! empty($data['save_address'])) {
            $this->updateProfile->handle($userId, [
                'name' => $data['name'] ?? $request->user()->name,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? [],
            ]);
        }

        $cardMeta = $this->resolveCard($userId, $data);
        $order = $this->createOrder->handle(CreateOrderDTO::fromArray($data, $userId));
        $this->processPayment->handle(
            (int) $order->id,
            $userId,
            false,
            $data['payment_method'] ?? null,
            $cardMeta,
        );

        return response()->json(
            $this->getOrder->handle((int) $order->id, $userId)->toArray(),
            201
        );
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function resolveCard(int $userId, array $data): array
    {
        $method = strtoupper((string) ($data['payment_method'] ?? ''));
        if ($method !== PaymentMethodEnum::CREDIT_CARD->value && $method !== PaymentMethodEnum::DEBIT_CARD->value) {
            return [];
        }

        if (! empty($data['card_id'])) {
            $saved = $this->cards->findByIdForUser((int) $data['card_id'], $userId);
            if ($saved === null) {
                throw new DomainException('Cartão salvo não encontrado.', 404);
            }

            return [
                'last_four' => $saved->lastFour,
                'brand' => $saved->brand,
                'holder_name' => $saved->holderName,
            ];
        }

        if (empty($data['card']['number'])) {
            if ($method === PaymentMethodEnum::CREDIT_CARD->value) {
                throw new DomainException('Informe ou selecione um cartão de crédito.');
            }

            return [];
        }

        $number = CardTokenizer::normalizeNumber((string) $data['card']['number']);
        $month = (int) ($data['card']['exp_month'] ?? 0);
        $year = (int) ($data['card']['exp_year'] ?? 0);
        if ($year > 0 && $year < 100) {
            $year += 2000;
        }
        CardTokenizer::assertValid($number, $month, $year, (string) ($data['card']['cvv'] ?? ''));

        $saved = null;
        if (! empty($data['save_card'])) {
            $saved = $this->saveCard->handle($userId, $data['card']);
        }

        return [
            'last_four' => $saved?->lastFour ?? substr($number, -4),
            'brand' => $saved?->brand ?? CardTokenizer::brand($number),
            'holder_name' => $data['card']['holder_name'] ?? null,
        ];
    }

    public function myOrders(Request $request): JsonResponse
    {
        return response()->json($this->listMyOrders->handle((int) $request->user()->id));
    }

    public function show(Request $request, int $id): JsonResponse
    {
        return response()->json(
            $this->getOrder->handle($id, (int) $request->user()->id)->toArray()
        );
    }
}
