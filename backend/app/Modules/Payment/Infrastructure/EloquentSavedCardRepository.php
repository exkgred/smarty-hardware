<?php

namespace App\Modules\Payment\Infrastructure;

use App\Modules\Payment\Domain\SavedCardEntity;
use App\Modules\Payment\Domain\SavedCardRepositoryInterface;

class EloquentSavedCardRepository implements SavedCardRepositoryInterface
{
    public function listByUser(int $userId): array
    {
        return EloquentSavedCardModel::query()
            ->where('user_id', $userId)
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->get()
            ->map(fn (EloquentSavedCardModel $model) => $this->toEntity($model))
            ->all();
    }

    public function findByIdForUser(int $id, int $userId): ?SavedCardEntity
    {
        $record = EloquentSavedCardModel::query()->where('id', $id)->where('user_id', $userId)->first();

        return $record ? $this->toEntity($record) : null;
    }

    public function save(SavedCardEntity $card): SavedCardEntity
    {
        $data = [
            'user_id' => $card->userId,
            'brand' => $card->brand,
            'last_four' => $card->lastFour,
            'holder_name' => $card->holderName,
            'exp_month' => $card->expMonth,
            'exp_year' => $card->expYear,
            'token' => $card->token,
            'is_default' => $card->isDefault,
        ];

        if ($card->id) {
            $record = EloquentSavedCardModel::query()->findOrFail($card->id);
            $record->update($data);
        } else {
            $record = EloquentSavedCardModel::query()->create($data);
        }

        return $this->toEntity($record->fresh());
    }

    public function delete(int $id, int $userId): void
    {
        EloquentSavedCardModel::query()->where('id', $id)->where('user_id', $userId)->delete();
    }

    public function clearDefault(int $userId): void
    {
        EloquentSavedCardModel::query()->where('user_id', $userId)->update(['is_default' => false]);
    }

    private function toEntity(EloquentSavedCardModel $model): SavedCardEntity
    {
        return new SavedCardEntity(
            id: $model->id,
            userId: $model->user_id,
            brand: $model->brand,
            lastFour: $model->last_four,
            holderName: $model->holder_name,
            expMonth: (int) $model->exp_month,
            expYear: (int) $model->exp_year,
            token: $model->token,
            isDefault: (bool) $model->is_default,
            createdAt: $model->created_at?->toIso8601String(),
        );
    }
}
