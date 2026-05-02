<?php
declare(strict_types=1);

namespace App\Core;

use Predis\Client;

/**
 * Singleton Redis client wrapper for the application.
 */
class RedisClient
{
    private static ?Client $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): Client
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

    $url = getenv('REDIS_URL') ?: 'redis://127.0.0.1:6379/0';

    self::$instance = new Client($url);

        return self::$instance;
    }
}
