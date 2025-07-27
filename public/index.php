<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\DashboardController;

// Start routing
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$method = $_SERVER['REQUEST_METHOD'];

// Handle root URL
if ($uri === '') {
    return (new DashboardController())->index();
}

// Define routes: "METHOD|pattern" => [ControllerClass, "action"]
$routes = [
    'GET|stores'            => ['App\Controllers\StoreController', 'index'],
    'GET|stores/create'     => ['App\Controllers\StoreController', 'create'],
    'POST|stores/store'     => ['App\Controllers\StoreController', 'store'],
    'GET|stores/edit/(\d+)'   => ['App\Controllers\StoreController', 'edit'],
    'POST|stores/update/(\d+)' => ['App\Controllers\StoreController', 'update'],
    'POST|stores/delete/(\d+)' => ['App\Controllers\StoreController', 'destroy'],
    'GET|stores/show/(\d+)'    => ['App\Controllers\StoreController','show'],

    'GET|weapons'           => ['App\Controllers\WeaponController', 'index'],
    'GET|weapons/create'    => ['App\Controllers\WeaponController', 'create'],
    'POST|weapons/store'    => ['App\Controllers\WeaponController', 'store'],
    'GET|weapons/edit/(\d+)'  => ['App\Controllers\WeaponController', 'edit'],
    'POST|weapons/update/(\d+)' => ['App\Controllers\WeaponController', 'update'],
    'POST|weapons/delete/(\d+)' => ['App\Controllers\WeaponController', 'destroy'],
    'GET|weapons/show/(\d+)'    => ['App\Controllers\WeaponController','show'],

    'GET|users'             => ['App\Controllers\UserController', 'index'],
    'GET|users/create'      => ['App\Controllers\UserController', 'create'],
    'POST|users/store'      => ['App\Controllers\UserController', 'store'],
    'GET|users/edit/(\d+)'    => ['App\Controllers\UserController', 'edit'],
    'POST|users/update/(\d+)'  => ['App\Controllers\UserController', 'update'],
    'POST|users/delete/(\d+)'  => ['App\Controllers\UserController', 'destroy'],
    'GET|users/show/(\d+)'     => ['App\Controllers\UserController','show'],

    'GET|login'             => ['App\Controllers\AuthController', 'login'],
    'POST|login'            => ['App\Controllers\AuthController', 'login'],
    'GET|logout'            => ['App\Controllers\AuthController', 'logout'],

    'GET|weapons/pdf/(\d+)'   => ['App\Controllers\WeaponController','pdf'],
    'GET|weapons/pdf'         => ['App\Controllers\WeaponController','pdfAll'],
    'GET|stores/pdf/(\d+)'    => ['App\Controllers\StoreController','pdf'],
    'GET|stores/pdf'          => ['App\Controllers\StoreController','pdfAll'],
];

foreach ($routes as $route => $handler) {
    list($routeMethod, $routePattern) = explode('|', $route, 2);
    if ($method === $routeMethod && preg_match('#^' . $routePattern . '$#', $uri, $matches)) {
        array_shift($matches);
        list($controllerClass, $action) = $handler;
        return (new $controllerClass())->$action(...$matches);
    }
}

// 404 Not Found
http_response_code(404);
echo "<h1>404 Not Found</h1>";