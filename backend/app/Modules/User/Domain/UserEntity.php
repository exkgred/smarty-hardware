<?php

namespace App\Modules\User\Domain;

use App\Modules\Common\Domain\AddressData;
use App\Modules\Common\Domain\EntityInterface;

class UserEntity implements EntityInterface
{
    public function __construct(
        private ?int $id,
        private string $name,
        private string $email,
        private string $password,
        private string $role,
        private ?string $created_at = null,
        private ?string $phone = null,
        private ?AddressData $address = null,
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getAddress(): ?AddressData
    {
        return $this->address;
    }

    public function withProfile(string $name, ?string $phone, ?AddressData $address): self
    {
        return new self(
            $this->id,
            $name,
            $this->email,
            $this->password,
            $this->role,
            $this->created_at,
            $phone,
            $address,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'phone' => $this->phone,
            'address' => $this->address?->toArray(),
            'created_at' => $this->created_at,
        ];
    }
}
