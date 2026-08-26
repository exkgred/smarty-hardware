<?php

namespace App\Modules\Payment\Domain;

use App\Modules\Common\Domain\EntityInterface;

class SavedCardEntity implements EntityInterface
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $userId,
        public readonly string $brand,
        public readonly string $lastFour,
        public readonly string $holderName,
        public readonly int $expMonth,
        public readonly int $expYear,
        public readonly string $token,
        public readonly bool $isDefault = false,
        public readonly ?string $createdAt = null,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'brand' => $this->brand,
            'last_four' => $this->lastFour,
            'holder_name' => $this->holderName,
            'exp_month' => $this->expMonth,
            'exp_year' => $this->expYear,
            'is_default' => $this->isDefault,
            'label' => strtoupper($this->brand).' •••• '.$this->lastFour,
            'created_at' => $this->createdAt,
        ];
    }
}
