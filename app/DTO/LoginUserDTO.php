<?php

declare(strict_types=1);

namespace App\DTO;

class LoginUserDTO
{
    /**
     * @param string $email
     * @param string $password
     */
    public function __construct(
        public string $email,
        public string $password
    ) {
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'email'    => $this->email,
            'password' => $this->password,
        ];
    }
}
