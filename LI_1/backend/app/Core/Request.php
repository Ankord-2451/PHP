<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Wraps incoming HTTP request details.
 */
class Request
{
    private array $headers;
    private array $query;
    private array $body;
    private string $method;
    private string $uri;

    public function __construct()
    {
        $this->headers = getallheaders() ?: [];
        $this->query = $_GET;
        $this->body = $this->parseJsonBody();
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->uri = strtok($_SERVER['REQUEST_URI'] ?? '/', '?') ?: '/';
    }

    /**
     * Returns the HTTP method.
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Returns the request URI path.
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Returns query string parameters.
     * @return array<string, mixed>
     */
    public function getQueryParams(): array
    {
        return $this->query;
    }

    /**
     * Returns JSON-decoded request body.
     * @return array<string, mixed>
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * Returns a single header value if present.
     */
    public function getHeader(string $name): ?string
    {
        $name = strtolower($name);
        foreach ($this->headers as $key => $value) {
            if (strtolower($key) === $name) {
                return is_array($value) ? $value[0] : $value;
            }
        }

        return null;
    }

    /**
     * Parses the JSON request body into an array.
     * @return array<string, mixed>
     */
    private function parseJsonBody(): array
    {
        $input = file_get_contents('php://input');
        if (empty($input)) {
            return $_POST;
        }

        $parsed = json_decode($input, true);
        return is_array($parsed) ? $parsed : [];
    }
}
