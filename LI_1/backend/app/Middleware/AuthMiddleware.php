<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\RedisClient;
use App\Core\Response;
use App\Helpers\JwtHelper;
use App\Models\UserModel;

/**
 * Handles JWT authentication and admin authorization.
 */
class AuthMiddleware
{
    public static function require(): void
    {
        $token = self::resolveBearerToken();
        if ($token === null) {
            Response::error('Unauthorized', 401);
        }

        $payload = JwtHelper::decode($token);
        if (!isset($payload['user_id'])) {
            Response::error('Unauthorized', 401);
        }

        $redis = RedisClient::getInstance();
        $sessionKey = 'session:' . $payload['user_id'];
        $storedToken = $redis->get($sessionKey);
        if ($storedToken === null || $storedToken !== $token) {
            Response::error('Unauthorized', 401);
        }
    }

    public static function requireAdmin(): void
    {
        self::require();

        $token = self::resolveBearerToken();
        $payload = JwtHelper::decode($token);

        $userModel = new UserModel();
        $user = $userModel->findById((int)$payload['user_id']);
        if ($user === null || $user['role'] !== 'admin') {
            Response::error('Forbidden', 403);
        }
    }

    private static function resolveBearerToken(): ?string
    {
        $headers = getallheaders() ?: [];
        foreach ($headers as $name => $value) {
            if (strtolower($name) === 'authorization') {
                if (is_string($value) && str_starts_with($value, 'Bearer ')) {
                    return substr($value, 7);
                }
            }
        }

        if (isset($_SERVER['HTTP_AUTHORIZATION']) && str_starts_with($_SERVER['HTTP_AUTHORIZATION'], 'Bearer ')) {
            return substr($_SERVER['HTTP_AUTHORIZATION'], 7);
        }

        return null;
    }
}
