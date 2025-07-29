<?php 

// config/database.php

// This file returns the database configuration array.
// It reads from environment variables (.env) with sensible defaults.
return [
    'host'      => $_ENV['DB_HOST'] ?? '127.0.0.1',
    'name'      => $_ENV['DB_DATABASE'] ?? 'challenge_db',
    'user'      => $_ENV['DB_USERNAME'] ?? 'root',
    'pass'      => $_ENV['DB_PASSWORD'] ?? '',
    'charset'   => 'utf8mb4',
];