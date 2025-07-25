<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Router;

$router = new Router();

require_once __DIR__ . '/Routes/store-routes.php';

$router->dispatch();
