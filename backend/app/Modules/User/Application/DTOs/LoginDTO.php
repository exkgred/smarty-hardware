<?php

namespace App\Modules\User\Application\DTOs;

class LoginDTO
{
    public function __construct(
        public string $email,
        public string $password
    ) {}
}
