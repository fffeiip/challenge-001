<?php
/**
 * A command-line script to reset and seed the database.
 * To run: php db/reset_db.php
 *
 * This script will:
 * 1. Drop existing tables to ensure a clean slate.
 * 2. Execute the schema.sql file to create the table structures.
 * 3. Execute the seeds.sql file to populate the tables with sample data.
 */

// This script should only be run from the command line.
if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.");
}

// We need the bootstrap file to access the Database class and config.
require_once __DIR__ . '/../src/bootstrap.php';

try {
    echo "Connecting to the database...\n";
    $pdo = Database::getConnection();
    echo "Connection successful.\n\n";

    // --- 1. Drop existing tables (good for a complete reset) ---
    echo "Dropping existing tables (if they exist)...\n";
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $pdo->exec("DROP TABLE IF EXISTS weapons;");
    $pdo->exec("DROP TABLE IF EXISTS stores;");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
    echo "Tables dropped.\n\n";

    // --- 2. Load and execute the schema ---
    $schemaPath = __DIR__ . '/schema.sql';
    echo "Loading schema from $schemaPath...\n";
    $schemaSql = file_get_contents($schemaPath);
    $pdo->exec($schemaSql);
    echo "Schema created successfully.\n\n";

    // --- 3. Load and execute the seeds ---
    $seedsPath = __DIR__ . '/seeds.sql';
    echo "Loading seeds from $seedsPath...\n";
    $seedsSql = file_get_contents($seedsPath);
    $pdo->exec($seedsSql);
    echo "Database seeded successfully.\n\n";

    echo "✅ Database reset and seeding complete!\n";

} catch (PDOException | Exception $e) {
    die("❌ An error occurred: " . $e->getMessage() . "\n");
}