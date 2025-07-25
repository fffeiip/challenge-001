<?php

use App\Controllers\StoreController;

$router->get('store.php', [StoreController::class, 'index']);
$router->get('store.php?action=create', [StoreController::class, 'create']);
$router->post('store.php?action=store', [StoreController::class, 'store']);
$router->get('store.php?action=show&id={id}', [StoreController::class, 'show']);
$router->get('store.php?action=edit&id={id}', [StoreController::class, 'edit']);
$router->post('store.php?action=update&id={id}', [StoreController::class, 'update']);
$router->post('store.php?action=delete&id={id}', [StoreController::class, 'delete']);
