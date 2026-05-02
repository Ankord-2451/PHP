<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;
use App\Helpers\JwtHelper;

/**
 * Handles authentication routes and responses.
 */
class AuthController
{
    private AuthService $service;

    public function __construct()
    {
        $this->service = new AuthService();
    }

    public function register(Request $request): void
    {
        $result = $this->service->register($request->getBody());
        Response::success($result, 'User created successfully', 201);
    }

    public function login(Request $request): void
    {
        $result = $this->service->login($request->getBody());
        Response::success($result, 'Login successful');
    }

    public function logout(Request $request): void
    {
        $token = $this->resolveToken($request);
        if ($token === null) {
            Response::error('Unauthorized', 401);
        }

        $this->service->logout($token);
        Response::success(null, 'Logout successful');
    }

    public function forgotPassword(Request $request): void
    {
        $this->service->forgotPassword($request->getBody());
    }

    public function resetPassword(Request $request): void
    {
        $this->service->resetPassword($request->getBody());
    }

    private function resolveToken(Request $request): ?string
    {
        $header = $request->getHeader('Authorization');
        if ($header !== null && str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }

        return null;
    }
}
