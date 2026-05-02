<?php
declare(strict_types=1);

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Thin wrapper around firebase/php-jwt encoding and decoding.
 */
class JwtHelper
{
    private static function getSecret(): string
    {
        $secret = $_ENV['JWT_SECRET'] ?? getenv('JWT_SECRET') ?? '';
        if ($secret === '') {
            throw new \RuntimeException('JWT secret is not configured.');
        }

        return $secret;
    }

    public static function encode(array $payload): string
    {
        $secret = self::getSecret();
        return JWT::encode($payload, $secret, 'HS256');
    }

    public static function decode(string $token): array
    {
        $secret = self::getSecret();
        $decoded = JWT::decode($token, new Key($secret, 'HS256'));
        return json_decode(json_encode($decoded), true);
    }
}
