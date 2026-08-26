<?php

namespace App\Modules\Payment\Application\UseCases;

use App\Modules\Common\Domain\DomainException;
use App\Modules\Payment\Domain\SavedCardRepositoryInterface;

class DeleteSavedCardUseCase
{
    public function __construct(private readonly SavedCardRepositoryInterface $cards) {}

    public function handle(int $id, int $userId): void
    {
        if ($this->cards->findByIdForUser($id, $userId) === null) {
            throw new DomainException('Cartão não encontrado.', 404);
        }
        $this->cards->delete($id, $userId);
    }
}
