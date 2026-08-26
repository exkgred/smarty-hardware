<?php

namespace App\Modules\User\Application\UseCases;

use App\Modules\Common\Domain\DomainException;
use App\Modules\User\Application\DTOs\LoginDTO;
use App\Modules\User\Domain\UserEntity;
use App\Modules\User\Domain\UserRepositoryInterface;

class AuthenticateUseCase
{
    public function __construct(private UserRepositoryInterface $repository) {}

    public function handle(LoginDTO $dto): UserEntity
    {
        $user = $this->repository->findByEmail($dto->email);

        if ($user === null || ! password_verify($dto->password, $user->getPassword())) {
            throw new DomainException('Credenciais inválidas.', 401);
        }

        return $user;
    }
}
