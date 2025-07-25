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
        $requestUri = $_SERVER['REQUEST_URI'];
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $path = trim($requestUri, '/');

        // Parse query string from URI
        $query = parse_url($requestUri, PHP_URL_QUERY);
        parse_str($query, $queryParams);

        // Determine action from query string, default to 'index'
        $action = isset($queryParams['action']) && $queryParams['action'] !== '' ? $queryParams['action'] : 'index';

        // Build route key (e.g. 'store.php?action=create')
        $script = basename(parse_url($requestUri, PHP_URL_PATH));
        $routeKey = $script;
        if ($action !== 'index') {
            $routeKey .= '?action=' . $action;
            // If id is present, append it
            if (isset($queryParams['id'])) {
                $routeKey .= '&id=' . $queryParams['id'];
            }
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod ) {
                
                $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_-]+)', $route['uri']);
                $pattern = str_replace('?', '\?', $pattern); // Escape ?
                $pattern = '/^' . $pattern . '$/';

                if (preg_match($pattern, $routeKey, $matches)) {

                    if (is_array($route['callback'])) {
                        $controller = new $route['callback'][0];
                        $method = $route['callback'][1];

                        // Pass matched parameters to the method if needed
                        array_shift($matches); // Remove full match

                        return call_user_func_array([$controller, $method], $matches);
                    }
                    if (is_callable($route['callback'])) {
                        return call_user_func($route['callback']);
                    }
                }
            }
        }

        // Fallback: 404 Not Found
        http_response_code(404);
        echo "404 - Page Not Found";
    }
}
