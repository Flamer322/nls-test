<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Data\Auth\LoginData;
use App\Http\Data\Auth\RegisterData;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function register(Request $request): Response
    {
        $this->authService->register(
            RegisterData::validateAndCreate($request->all())
        );

        return response(null, Response::HTTP_CREATED);
    }

    public function login(Request $request): Response
    {
        $this->authService->login(
            LoginData::validateAndCreate($request->all())
        );

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function logout(Request $request): Response
    {
        $this->authService->logout();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function me(Request $request): JsonResponse
    {
        return response()
            ->json($request->user(), 200);
    }
}
