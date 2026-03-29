<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\DTO\LoginUserDTO;
use App\DTO\RegisterUserDTO;

interface AuthServiceInterface
{
    public function register(RegisterUserDTO $DTO): array;

    public function login(LoginUserDTO $DTO): array;

    public function logout(): void;
}
