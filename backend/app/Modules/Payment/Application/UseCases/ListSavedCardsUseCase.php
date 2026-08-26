<?php

namespace App\Modules\Payment\Application\UseCases;

use App\Modules\Payment\Domain\SavedCardRepositoryInterface;

class ListSavedCardsUseCase
{
    public function __construct(private readonly SavedCardRepositoryInterface $cards) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function handle(int $userId): array
    {
        return array_map(
            static fn ($card) => $card->toArray(),
            $this->cards->listByUser($userId),
        );
    }
}
