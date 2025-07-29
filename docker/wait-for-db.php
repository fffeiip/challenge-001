<?php
// docker/wait-for-db.php

// This script is designed to be run from the Docker entrypoint to wait for the DB to be ready.

// Load Composer's autoloader. The path is relative to the project root (/var/www).
require_once '/var/www/vendor/autoload.php';

// Load environment variables from .env file
try {
    // The path is relative to the project root (/var/www).
    $dotenv = Dotenv\Dotenv::createImmutable('/var/www');
    $dotenv->load();
} catch (\Dotenv\Exception\InvalidPathException $e) {
    // .env file is not mandatory, might use Docker environment variables
    echo "Notice: .env file not found. Relying on Docker environment variables.\n";
}

$db_host = $_ENV['DB_HOST'] ?? 'db'; // 'db' is a common service name in docker-compose
$db_port = $_ENV['DB_PORT'] ?? '3306';
$db_user = $_ENV['DB_USERNAME'] ?? 'root';
$db_pass = $_ENV['DB_PASSWORD'] ?? '';

$max_attempts = 30;
$wait_seconds = 2;

echo "Attempting to connect to database at $db_host:$db_port...\n";

for ($i = 1; $i <= $max_attempts; ++$i) {
    try {
        $pdo = new PDO("mysql:host=$db_host;port=$db_port", $db_user, $db_pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        echo "Database connection successful!\n";
        exit(0); // Success
    } catch (PDOException $e) {
        echo "Attempt $i of $max_attempts: Database connection failed. Waiting $wait_seconds seconds...\n";
        sleep($wait_seconds);
    }
}

echo "Error: Could not connect to the database after $max_attempts attempts.\n";
exit(1); // Failure

