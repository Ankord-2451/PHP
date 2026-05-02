<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Static helper for sending standardized JSON responses.
 */
class Response
{
    /**
     * Sends a JSON response with the given payload and status.
     * @param  mixed $payload
     * @param  int   $statusCode
     * @return void
     */
    public static function json(mixed $payload, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Sends a success response with optional data and message.
     * @param  mixed  $data
     * @param  string|null $message
     * @param  int    $statusCode
     * @return void
     */
    public static function success(mixed $data = null, ?string $message = null, int $statusCode = 200): void
    {
        $response = ['success' => true];
        if ($data !== null) {
            $response['data'] = $data;
        }
        if ($message !== null) {
            $response['message'] = $message;
        }
        self::json($response, $statusCode);
    }

    /**
     * Sends an error response with a message or field errors.
     * @param  string|array<string, string> $error
     * @param  int $statusCode
     * @return void
     */
    public static function error(string|array $error, int $statusCode = 400): void
    {
        http_response_code($statusCode);
        if (is_array($error)) {
            echo json_encode(['success' => false, 'errors' => $error], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => false, 'error' => $error], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }
}
