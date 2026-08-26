<?php

namespace App\Modules\User\Application\DTOs;

use InvalidArgumentException;

class RegisterCustomerDTO
{
    public string $name;

    public string $email;

    public string $password;

    public function __construct(array $data)
    {
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            throw new InvalidArgumentException('Missing required fields');
        }
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->password = $data['password'];
    }
}
