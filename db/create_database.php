<?php

// This script should be run from the command line: php db/create_database.php

if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.");
}

echo "Database creation script started.\n";

// Load the main application bootstrap file.
require_once __DIR__ . '/../src/bootstrap.php';

$config = $GLOBALS['db_config'];

try {
    $host = $config['host'];
    $dbname = $config['name'];
    $user = $config['user'];
    $pass = $config['pass'];
    $charset = $config['charset'];

    echo "Attempting to connect to MySQL server at $host...\n";

    // Connect without dbname to create the database
    $pdo = new PDO("mysql:host=$host;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    echo "Connection successful.\n";

    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET $charset COLLATE utf8mb4_unicode_ci");
    echo "Database '$dbname' created or already exists.\n";

} catch (PDOException $e) {
     // Provide more helpful error messages for common issues
    if ($e->getCode() === 1045) { // Access denied
        die("\nDB ERROR: Access denied for user '$user'.\n"
          . "Please check your DB_USERNAME and DB_PASSWORD in your .env file or your MySQL setup.\n");
    } elseif ($e->getCode() === 2002) { // Can't connect
        die("\nDB ERROR: Can't connect to MySQL server on '$host'.\n"
          . "Please ensure your database server is running and accessible.\n");
    }
    die("DB ERROR: " . $e->getMessage() . "\n");
}