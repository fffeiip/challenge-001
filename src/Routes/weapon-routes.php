<?php

use App\Controllers\WeaponController;

$router->get('weapon.php', [WeaponController::class, 'index']);
$router->get('weapon.php?action=create', [WeaponController::class, 'create']);
$router->post('weapon.php?action=store', [WeaponController::class, 'store']);
$router->get('weapon.php?action=edit&id={id}', [WeaponController::class, 'edit']);
$router->post('weapon.php?action=update&id={id}', [WeaponController::class, 'update']);
$router->get('weapon.php?action=show&id={id}', [WeaponController::class, 'show']);
$router->post('weapon.php?action=delete&id={id}', [WeaponController::class, 'delete']);