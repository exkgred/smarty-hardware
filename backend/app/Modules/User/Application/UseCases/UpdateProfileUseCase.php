<?php

namespace App\Modules\User\Application\UseCases;

use App\Modules\Common\Domain\AddressData;
use App\Modules\Common\Domain\DomainException;
use App\Modules\User\Domain\UserEntity;
use App\Modules\User\Domain\UserRepositoryInterface;

class UpdateProfileUseCase
{
    public function __construct(private readonly UserRepositoryInterface $users) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(int $userId, array $data): UserEntity
    {
        $user = $this->users->findById($userId);
        if ($user === null) {
            throw new DomainException('Usuário não encontrado.', 404);
        }

        $name = trim((string) ($data['name'] ?? $user->getName()));
        $phone = isset($data['phone']) ? trim((string) $data['phone']) : $user->getPhone();
        $address = isset($data['address']) && is_array($data['address'])
            ? AddressData::fromArray($data['address'])
            : $user->getAddress();

        return $this->users->save($user->withProfile(
            name: $name !== '' ? $name : $user->getName(),
            phone: $phone !== '' ? $phone : null,
            address: $address,
        ));
    }
}
