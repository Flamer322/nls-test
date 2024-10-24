<?php

declare(strict_types=1);

namespace App\Services;

use App\Facades\JWTAuth;
use App\Http\Data\Auth\LoginData;
use App\Http\Data\Auth\RegisterData;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final readonly class AuthService
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    public function register(RegisterData $data): void
    {
        $user = new User([
            'name' => $data->name,
            'email' => $data->email,
            'password' => Hash::make($data->password),
        ]);

        $this->userRepository->save($user);
    }

    public function login(LoginData $data): void
    {
        $user = User::query()
            ->where('email', $data->email)
            ->first();

        if ($user === null || ! Hash::check($data->password, $user->password)) {
            throw new UnprocessableEntityHttpException('Wrong email or password');
        }

        $token = JWTAuth::fromUser($user);

        Cookie::queue('token', $token, 10080, '/', request()->getHost(), true, true, false, 'none');
    }

    public function logout(): void
    {
        Cookie::queue('token', null, -1, '/', request()->getHost(), true, true, false, 'none');
    }
}
