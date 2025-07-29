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

    $seedsPath = __DIR__ . '/seeds.sql';
    echo "Seeding data from $seedsPath...\n";
    $pdo->exec(file_get_contents($seedsPath));
    echo "Database seeded successfully.\n";
} catch (PDOException | Exception $e) {
    die("❌ An error occurred: " . $e->getMessage() . "\n");
}
