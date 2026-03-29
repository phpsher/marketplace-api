<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Services\AuthServiceInterface;
use App\DTO\LoginUserDTO;
use App\DTO\RegisterUserDTO;
use App\Exceptions\InternalServerErrorException;
use App\Exceptions\InvalidCredentialsException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Throwable;

readonly class AuthService implements AuthServiceInterface
{
    /*
     * @var int
     */
    protected int $ttl;

    public function __construct()
    {
        $this->ttl = 3600 * 24 * 3;
    }

    /**
     * @param RegisterUserDTO $DTO
     * @throws InternalServerErrorException
     * @return array
     */
    public function register(RegisterUserDTO $DTO): array
    {
        try {
            $user = User::create($DTO->toArray());
            Cache::put("user:email:$user->email", $user, $this->ttl);
        } catch (Throwable $e) {
            throw new InternalServerErrorException($e->getMessage());
        }

        $token = $user->createToken('token')->plainTextToken;

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }

    /**
     * @param LoginUserDTO $DTO
     * @throws InvalidCredentialsException
     * @throws InternalServerErrorException
     * @return array
     */
    public function login(LoginUserDTO $DTO): array
    {
        try {
            $user = Cache::remember("user:email:$DTO->email", $this->ttl, function () use ($DTO) {
                return User::where('email', $DTO->email)->firstOrFail();
            });
        } catch (Throwable $e) {
            throw new InternalServerErrorException($e->getMessage());
        }

        if (!$user || !Hash::check($DTO->password, $user->password)) {
            throw new InvalidCredentialsException('Invalid email or password.');
        }

        $token = $user->createToken('token')->plainTextToken;

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }

    /**
     * @return void
     */
    public function logout(): void
    {
        Auth::user()->tokens()->delete();
    }
}
