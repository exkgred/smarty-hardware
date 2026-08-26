<?php

namespace App\Modules\User\Infrastructure;

use App\Modules\Common\Domain\AddressData;
use App\Modules\User\Domain\UserEntity;
use App\Modules\User\Domain\UserRepositoryInterface;
use Laravel\Sanctum\HasApiTokens;

class EloquentUserRepository implements UserRepositoryInterface
{
    private function mapToEntity(EloquentUserModel $model): UserEntity
    {
        $address = is_array($model->address) ? AddressData::fromArray($model->address) : null;

        return new UserEntity(
            $model->id,
            $model->name,
            $model->email,
            $model->password,
            $model->role,
            $model->created_at ? $model->created_at->toDateTimeString() : null,
            $model->phone,
            $address && ! $address->isEmpty() ? $address : null,
        );
    }

    public function findById(int $id): ?UserEntity
    {
        $model = EloquentUserModel::find($id);

        return $model ? $this->mapToEntity($model) : null;
    }

    public function findByEmail(string $email): ?UserEntity
    {
        $model = EloquentUserModel::where('email', $email)->first();

        return $model ? $this->mapToEntity($model) : null;
    }

    public function save(UserEntity $user): UserEntity
    {
        $data = [
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'role' => $user->getRole(),
            'phone' => $user->getPhone(),
            'address' => $user->getAddress()?->toArray(),
        ];

        if ($user->getId()) {
            $model = EloquentUserModel::find($user->getId());
            $model->update($data);
        } else {
            $model = EloquentUserModel::create($data);
        }

        return $this->mapToEntity($model);
    }

    public function delete(int $id): bool
    {
        return (bool) EloquentUserModel::destroy($id);
    }

    public function createApiToken(int $userId, string $name = 'auth'): string
    {
        $model = EloquentUserModel::findOrFail($userId);

        return $model->createToken($name)->plainTextToken;
    }

    public function revokeCurrentToken(object $authenticatable): void
    {
        if (in_array(HasApiTokens::class, class_uses_recursive($authenticatable), true)) {
            $authenticatable->currentAccessToken()?->delete();
        }
    }

    public function listCustomers(): array
    {
        return EloquentUserModel::query()
            ->where('role', 'customer')
            ->orderBy('name')
            ->get()
            ->map(fn (EloquentUserModel $model) => $this->mapToEntity($model))
            ->all();
    }
}
