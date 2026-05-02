<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

/**
 * Singleton database connection for PostgreSQL using PDO.
 */
class Database
{
    private static ?PDO $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $host = getenv('DB_HOST') ?: 'localhost';
        $port = getenv('DB_PORT') ?: '5432';
        $dbName = getenv('DB_NAME') ?: 'bookease';
        $user = getenv('DB_USER') ?: 'postgres';
        $pass = getenv('DB_PASS') ?: 'postgres';

        $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s', $host, $port, $dbName);

        try {
            self::$instance = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $exception) {
            Response::error('Database connection failed', 500);
        }

        return self::$instance;
    }
}

