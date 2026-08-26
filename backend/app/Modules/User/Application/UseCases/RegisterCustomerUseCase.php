<?php

namespace App\Modules\User\Application\UseCases;

use App\Modules\Common\Domain\DomainException;
use App\Modules\User\Application\DTOs\RegisterCustomerDTO;
use App\Modules\User\Domain\UserEntity;
use App\Modules\User\Domain\UserRepositoryInterface;

class RegisterCustomerUseCase
{
    public function __construct(private UserRepositoryInterface $repository) {}

    public function handle(RegisterCustomerDTO $dto): UserEntity
    {
        if ($this->repository->findByEmail($dto->email)) {
            throw new DomainException('Email already exists', 409);
        }

        $user = new UserEntity(
            null,
            $dto->name,
            $dto->email,
            password_hash($dto->password, PASSWORD_BCRYPT),
            'customer'
        );

        return $this->repository->save($user);
    }
}
