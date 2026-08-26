<?php

namespace App\Modules\Common\Domain;

interface RepositoryInterface
{
    public function findById(int $id): ?EntityInterface;

    public function save(EntityInterface $entity): EntityInterface;

    public function delete(int $id): bool;
}
