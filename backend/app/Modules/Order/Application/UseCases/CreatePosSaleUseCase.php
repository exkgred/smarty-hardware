<?php

namespace App\Modules\Order\Application\UseCases;

use App\Modules\Common\Domain\DomainException;
use App\Modules\Order\Application\DTOs\CreateOrderDTO;
use App\Modules\Order\Domain\OrderChannelEnum;
use App\Modules\Payment\Application\UseCases\ProcessPaymentUseCase;
use App\Modules\Payment\Domain\PaymentMethodEnum;
use App\Modules\User\Domain\UserEntity;
use App\Modules\User\Domain\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreatePosSaleUseCase
{
    public function __construct(
        private readonly CreateOrderUseCase $createOrder,
        private readonly ProcessPaymentUseCase $processPayment,
        private readonly UserRepositoryInterface $users,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function handle(array $data, int $adminId): array
    {
        $customer = $this->resolveCustomer($data);

        return DB::transaction(function () use ($data, $customer) {
            $method = PaymentMethodEnum::tryFrom(strtoupper((string) ($data['payment_method'] ?? 'CASH')))
                ?? PaymentMethodEnum::CASH;

            $order = $this->createOrder->handle(new CreateOrderDTO(
                userId: (int) $customer->getId(),
                items: array_map(static fn ($item) => [
                    'product_id' => (int) $item['product_id'],
                    'quantity' => (int) $item['quantity'],
                ], $data['items'] ?? []),
                shippingCost: 0,
                shippingName: $customer->getName(),
                shippingAddress: 'Retirada no balcão Smarty Hardware',
                paymentMethod: $method,
                channel: OrderChannelEnum::POS,
                discountAmount: (float) ($data['discount'] ?? 0),
            ));

            $payment = $this->processPayment->handle((int) $order->id, (int) $customer->getId(), true, $method->value);

            return is_array($payment['order'] ?? null) ? $payment['order'] : $order->toArray();
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function resolveCustomer(array $data): UserEntity
    {
        if (! empty($data['customer_id'])) {
            $user = $this->users->findById((int) $data['customer_id']);
            if ($user === null) {
                throw new DomainException('Cliente não encontrado.', 404);
            }

            return $user;
        }

        $email = (string) ($data['customer_email'] ?? '');
        if ($email !== '') {
            $existing = $this->users->findByEmail($email);
            if ($existing !== null) {
                return $existing;
            }
        }

        $walkIn = $this->users->findByEmail('balcao@smartyhardware.test');
        if ($walkIn !== null && empty($data['customer_name'])) {
            return $walkIn;
        }

        if (! empty($data['customer_name'])) {
            $generated = $email !== '' ? $email : ('balcao+'.Str::lower(Str::random(8)).'@smartyhardware.test');

            return $this->users->save(new UserEntity(
                null,
                (string) $data['customer_name'],
                $generated,
                password_hash(Str::random(16), PASSWORD_BCRYPT),
                'customer',
            ));
        }

        if ($walkIn !== null) {
            return $walkIn;
        }

        throw new DomainException('Informe o cliente da venda.');
    }
}
