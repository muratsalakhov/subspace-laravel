<?php

namespace App\DTO\Auth;

use App\DTO\BaseDTO;

class UserRegisterDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password
    ) {
    }
}
