<?php

namespace App\Core;

class Router
{
    private $routes = [];

    public function get($uri, $callback)
    {
        $this->addRoute('GET', $uri, $callback);
    }

    public function post($uri, $callback)
    {
        $this->addRoute('POST', $uri, $callback);
    }

    public function addRoute($method, $uri, $callback)
    {
        $this->routes[] = [
            'method' => $method,
            'uri' => trim($uri, '/'),
            'callback' => $callback
        ];
    }

    public function dispatch()
    {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $path = trim($requestUri, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $route['uri'] === $path) {

                if (is_array($route['callback'])) {
                    $controller = new $route['callback'][0];
                    $method = $route['callback'][1];
                    return call_user_func([$controller, $method]);
                }

                if (is_callable($route['callback'])) {
                    return call_user_func($route['callback']);
                }
            }
        }

        // Fallback: 404 Not Found
        http_response_code(404);
        echo "404 - Page Not Found";
    }
}
