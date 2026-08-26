<?php

namespace App\Modules\Payment\Domain;

use App\Modules\Common\Domain\DomainException;

class CardTokenizer
{
    public static function normalizeNumber(string $number): string
    {
        return preg_replace('/\D/', '', $number) ?? '';
    }

    public static function brand(string $number): string
    {
        $digits = self::normalizeNumber($number);
        if (str_starts_with($digits, '4')) {
            return 'visa';
        }
        if (preg_match('/^(5[1-5]|2[2-7])/', $digits)) {
            return 'mastercard';
        }
        if (preg_match('/^3[47]/', $digits)) {
            return 'amex';
        }
        if (preg_match('/^(636368|438935|504175|451416|509|650)/', $digits)) {
            return 'elo';
        }

        return 'card';
    }

    public static function assertValid(string $number, int $expMonth, int $expYear, string $cvv): void
    {
        $digits = self::normalizeNumber($number);
        if (strlen($digits) < 13 || strlen($digits) > 19 || ! self::luhn($digits)) {
            throw new DomainException('Número de cartão inválido.');
        }
        if ($expMonth < 1 || $expMonth > 12) {
            throw new DomainException('Mês de validade inválido.');
        }
        $nowYear = (int) date('Y');
        $nowMonth = (int) date('n');
        if ($expYear < $nowYear || ($expYear === $nowYear && $expMonth < $nowMonth)) {
            throw new DomainException('Cartão vencido.');
        }
        if (! preg_match('/^\d{3,4}$/', $cvv)) {
            throw new DomainException('CVV inválido.');
        }
    }

    public static function token(int $userId, string $lastFour, int $expMonth, int $expYear): string
    {
        return 'tok_'.hash('sha256', $userId.'|'.$lastFour.'|'.$expMonth.'|'.$expYear.'|smarty-sandbox');
    }

    private static function luhn(string $number): bool
    {
        $sum = 0;
        $alt = false;
        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $n = (int) $number[$i];
            if ($alt) {
                $n *= 2;
                if ($n > 9) {
                    $n -= 9;
                }
            }
            $sum += $n;
            $alt = ! $alt;
        }

        return $sum % 10 === 0;
    }
}
