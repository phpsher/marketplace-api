<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\AuthServiceInterface;
use App\DTO\LoginUserDTO;
use App\DTO\RegisterUserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    use ResponseTrait;

    public function __construct(
        protected AuthServiceInterface $authService
    ) {
    }

    public function register(RegisterUserRequest $request): JsonResponse
    {
        $user = $this->authService->register(
            new RegisterUserDTO(
                name: $request->input('name'),
                email: $request->input('email'),
                password: $request->input('password'),
            )
        );


        return $this->success(
            message: 'Successfully register',
            data: [
                'user'  => $user['user'],
                'token' => $user['token'],
            ],
        );
    }

    public function login(LoginUserRequest $request): JsonResponse
    {
        $user = $this->authService->login(
            new LoginUserDTO(
                email: $request->input('email'),
                password: $request->input('password')
            )
        );

        return $this->success(
            message: 'Successfully login',
            data: [
                'user'  => $user['user'],
                'token' => $user['token'],
            ],
        );
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return $this->success(
            message: 'Successfully logout',
        );
    }
}
