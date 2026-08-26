<?php

namespace App\Modules\Payment\Application\UseCases;

use App\Modules\Common\Domain\DomainException;
use App\Modules\Payment\Domain\CardTokenizer;
use App\Modules\Payment\Domain\SavedCardEntity;
use App\Modules\Payment\Domain\SavedCardRepositoryInterface;

class SaveCardUseCase
{
    public function __construct(private readonly SavedCardRepositoryInterface $cards) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(int $userId, array $data): SavedCardEntity
    {
        $number = CardTokenizer::normalizeNumber((string) ($data['number'] ?? ''));
        $month = (int) ($data['exp_month'] ?? 0);
        $year = (int) ($data['exp_year'] ?? 0);
        if ($year < 100) {
            $year += 2000;
        }
        CardTokenizer::assertValid($number, $month, $year, (string) ($data['cvv'] ?? ''));

        $holder = trim((string) ($data['holder_name'] ?? ''));
        if ($holder === '') {
            throw new DomainException('Informe o nome impresso no cartão.');
        }

        $lastFour = substr($number, -4);
        $isDefault = (bool) ($data['is_default'] ?? false) || $this->cards->listByUser($userId) === [];
        if ($isDefault) {
            $this->cards->clearDefault($userId);
        }

        return $this->cards->save(new SavedCardEntity(
            id: null,
            userId: $userId,
            brand: CardTokenizer::brand($number),
            lastFour: $lastFour,
            holderName: $holder,
            expMonth: $month,
            expYear: $year,
            token: CardTokenizer::token($userId, $lastFour, $month, $year),
            isDefault: $isDefault,
        ));
    }
}
