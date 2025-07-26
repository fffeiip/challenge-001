<?php

// This script should only be run from the command line.
if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.");
}

require_once __DIR__ . '/../src/bootstrap.php';

try {
    echo "Connecting to the database...\n";
    $pdo = Database::getConnection();
    echo "Connection successful.\n\n";

    $schemaPath = __DIR__ . '/schema.sql';
    echo "Applying schema from $schemaPath...\n";
    $pdo->exec(file_get_contents($schemaPath));
    echo "Schema applied successfully.\n";
} catch (PDOException | Exception $e) {
    die("❌ An error occurred: " . $e->getMessage() . "\n");
}