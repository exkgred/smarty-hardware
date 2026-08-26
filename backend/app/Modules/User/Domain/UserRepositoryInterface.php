<?php

namespace App\Modules\User\Domain;

interface UserRepositoryInterface
{
    public function findById(int $id): ?UserEntity;

    public function findByEmail(string $email): ?UserEntity;

    public function save(UserEntity $user): UserEntity;

    public function delete(int $id): bool;

    public function createApiToken(int $userId, string $name = 'auth'): string;

    public function revokeCurrentToken(object $authenticatable): void;

    /**
     * @return array<int, UserEntity>
     */
    public function listCustomers(): array;
}
