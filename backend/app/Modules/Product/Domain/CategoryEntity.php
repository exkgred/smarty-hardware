<?php

namespace App\Modules\Product\Domain;

use App\Modules\Common\Domain\EntityInterface;

class CategoryEntity implements EntityInterface
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $description = null,
        public readonly ?string $createdAt = null,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'created_at' => $this->createdAt,
        ];
    }
}
