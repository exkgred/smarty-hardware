<?php

namespace App\Modules\Payment\Domain;

interface SavedCardRepositoryInterface
{
    /**
     * @return array<int, SavedCardEntity>
     */
    public function listByUser(int $userId): array;

    public function findByIdForUser(int $id, int $userId): ?SavedCardEntity;

    public function save(SavedCardEntity $card): SavedCardEntity;

    public function delete(int $id, int $userId): void;

    public function clearDefault(int $userId): void;
}
