<?php
/**
 * Initialization script:
 * - Ensures SQLite file and directory exist
 * - Runs all migrations
 */
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../core/Database.php';
require __DIR__ . '/../core/MigrationInterface.php';

use Core\MigrationInterface;

// Create DB file and directory if missing
$config = require __DIR__ . '/../config/database.php';
$dbDir = dirname($config['database']);
if (! is_dir($dbDir)) {
    mkdir($dbDir, 0755, true);
}
if (! file_exists($config['database'])) {
    touch($config['database']);
    echo "Created SQLite database at {$config['database']}\n";
}

// Run migrations
$files = glob(__DIR__ . '/../app/Migrations/*.php');
foreach ($files as $file) {
    $classFile = pathinfo($file, PATHINFO_FILENAME);
    // Derive migration class name
    $migrationName = preg_replace('/^\d+_/', '', $classFile);
    $fqcn = 'App\\Migrations\\' . str_replace(' ', '', ucwords(str_replace('_', ' ', $migrationName)));
    if (! class_exists($fqcn, false)) {
        require $file;
        echo "Loaded migration: {$classFile}\n";
    } else {
        echo "Skipping duplicate migration: {$fqcn}\n";
    }
    /** @var MigrationInterface $migration */
    $migration = new $fqcn();
    $migration->down(); // Ensure down method is called first to reset state (this drops tables)
    $migration->up();
    echo "Migrated: {$fqcn}\n";
}

// Run seed script
require __DIR__ . '/../app/Migrations/seed/seed.php';

echo "Initialization complete.\n";