<?php

namespace App\Modules\Payment\Domain;

enum PaymentMethodEnum: string
{
    case PIX = 'PIX';
    case CREDIT_CARD = 'CREDIT_CARD';
    case DEBIT_CARD = 'DEBIT_CARD';
    case BOLETO = 'BOLETO';
    case CASH = 'CASH';
    case TRANSFER = 'TRANSFER';

    public function label(): string
    {
        return match ($this) {
            self::PIX => 'PIX',
            self::CREDIT_CARD => 'Cartão de crédito',
            self::DEBIT_CARD => 'Cartão de débito',
            self::BOLETO => 'Boleto bancário',
            self::CASH => 'Dinheiro (balcão)',
            self::TRANSFER => 'Transferência bancária',
        };
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            static fn (self $method) => ['value' => $method->value, 'label' => $method->label()],
            self::cases(),
        );
    }
}
