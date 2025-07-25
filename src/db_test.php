<?php

require_once __DIR__ . '/Core/Database.php';

use App\Core\Database;

spl_autoload_register(function ($class) {
    $prefix = 'Core\\';
    $base_dir = __DIR__ . '/Core/';
    
    if (strpos($class, $prefix) === 0) {
        $relative_class = substr($class, strlen($prefix));
        $file = $base_dir . $relative_class . '.php';

        if (file_exists($file)) {
            require $file;
        }
    }
});

$dotenvPath = __DIR__ . '/.env';
if (file_exists($dotenvPath)) {
    $lines = file($dotenvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) {
            continue;
        }
        list($key, $value) = explode('=', $line, 2);
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}

try {
    $db = Database::getInstance()->getConnection();
    echo " Database connection successful!";
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage();
}
