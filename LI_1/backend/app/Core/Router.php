<?php
declare(strict_types=1);

namespace App\Core;

use App\Middleware\AuthMiddleware;

/**
 * Simple router that matches HTTP methods and path templates.
 */
class Router
{
    /** @var array<int, array{method:string,pattern:string,handler:string,middleware:array<string>}> */
    private array $routes = [];

    public function get(string $pattern, string $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $pattern, $handler, $middleware);
    }

    public function post(string $pattern, string $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $pattern, $handler, $middleware);
    }

    public function put(string $pattern, string $handler, array $middleware = []): void
    {
        $this->addRoute('PUT', $pattern, $handler, $middleware);
    }

    public function delete(string $pattern, string $handler, array $middleware = []): void
    {
        $this->addRoute('DELETE', $pattern, $handler, $middleware);
    }

    private function addRoute(string $method, string $pattern, string $handler, array $middleware): void
    {
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(Request $request): void
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $request->getMethod()) {
                continue;
            }

            $params = [];
            if ($this->matchRoute($route['pattern'], $request->getUri(), $params)) {
                foreach ($route['middleware'] as $middleware) {
                    if ($middleware === 'auth') {
                        AuthMiddleware::require();
                    }
                    if ($middleware === 'admin') {
                        AuthMiddleware::requireAdmin();
                    }
                }

                [$controllerName, $action] = explode('@', $route['handler']);
                $controllerClass = 'App\\Controllers\\' . $controllerName;

                if (!class_exists($controllerClass)) {
                    Response::error('Handler not found', 500);
                }

                $controller = new $controllerClass();
                if (!method_exists($controller, $action)) {
                    Response::error('Handler method not found', 500);
                }

                call_user_func_array([$controller, $action], array_merge([$request], $params));
                return;
            }
        }

        Response::error('Not Found', 404);
    }

    private function matchRoute(string $pattern, string $uri, array &$params): bool
    {
        $patternSegments = explode('/', trim($pattern, '/'));
        $uriSegments = explode('/', trim($uri, '/'));

        if (count($patternSegments) !== count($uriSegments)) {
            return false;
        }

        foreach ($patternSegments as $index => $segment) {
            if (str_starts_with($segment, ':')) {
                $key = substr($segment, 1);
                $params[$key] = $uriSegments[$index];
                continue;
            }

            if ($segment !== $uriSegments[$index]) {
                return false;
            }
        }

        return true;
    }
}
