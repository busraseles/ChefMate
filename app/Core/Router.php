<?php

namespace App\Core;

final class Router
{
    
    private array $routes = [];

    public function get(string $path, array $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, array $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    public function put(string $path, array $handler, array $middleware = []): void
    {
        $this->add('PUT', $path, $handler, $middleware);
    }

    public function delete(string $path, array $handler, array $middleware = []): void
    {
        $this->add('DELETE', $path, $handler, $middleware);
    }

    private function add(string $method, string $path, array $handler, array $middleware): void
    {
        
        $regex = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $path);
        $regex = '#^' . $regex . '$#';

        $this->routes[] = [
            'method'     => $method,
            'pattern'    => $path,
            'regex'      => $regex,
            'handler'    => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(Request $request, ?string $uri = null): void
    {
        if ($uri === null) {
            $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
        }
        $uri = rtrim($uri, '/');
        if ($uri === '') {
            $uri = '/';
        }

        $method = $request->method();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['regex'], $uri, $matches)) {
                
                $params = array_filter(
                    $matches,
                    static fn($key) => is_string($key),
                    ARRAY_FILTER_USE_KEY
                );
                $request->setRouteParams($params);

                foreach ($route['middleware'] as $middleware) {

                    
                    if (is_array($middleware)) {
                        $middlewareClass = array_shift($middleware);
                        $args = $middleware; 
                        (new $middlewareClass(...$args))->handle($request);
                    } else {
                        (new $middleware())->handle($request);
                    }
                }

                [$controllerClass, $methodName] = $route['handler'];
                $controller = new $controllerClass();
                $controller->$methodName($request);
                return;
            }
        }

        Response::notFound('İstenen adres bulunamadı: ' . $uri);
    }
}
