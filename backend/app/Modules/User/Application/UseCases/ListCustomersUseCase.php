<?php

namespace App\Modules\User\Application\UseCases;

use App\Modules\User\Domain\UserRepositoryInterface;

class ListCustomersUseCase
{
    public function __construct(private readonly UserRepositoryInterface $users) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function handle(): array
    {
        return array_map(
            static fn ($user) => $user->toArray(),
            $this->users->listCustomers(),
        );
    }
}
