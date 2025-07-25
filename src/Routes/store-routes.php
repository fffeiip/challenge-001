<?php

use App\Controllers\StoreController;

$router->get('store.php', [StoreController::class, 'index']);
