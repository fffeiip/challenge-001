<?php

use App\Controllers\StoreController;

$router->get('store.php', [StoreController::class, 'index']);
$router->get('store.php?action=create', [StoreController::class, 'create']);
$router->post('store.php?action=store', [StoreController::class, 'store']);
