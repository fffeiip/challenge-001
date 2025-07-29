<?php

use PHPUnit\Framework\TestCase;

abstract class DatabaseTestCase extends TestCase
{
    protected static $pdo = null;

    public static function setUpBeforeClass(): void
    {
        if (self::$pdo !== null) {
            return;
        }

        // Get config from phpunit.xml's <php> section
        $config = [
            'host'      => $_ENV['DB_HOST'] ?? '127.0.0.1',
            'name'      => $_ENV['DB_DATABASE'] ?? 'challenge_db_test',
            'user'      => $_ENV['DB_USERNAME'] ?? 'root',
            'pass'      => $_ENV['DB_PASSWORD'] ?? '',
            'charset'   => 'utf8mb4',
        ];

        // Connect without dbname to create the database
        $tmpPdo = new PDO("mysql:host={$config['host']};charset={$config['charset']}", $config['user'], $config['pass']);
        $tmpPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $tmpPdo->exec("DROP DATABASE IF EXISTS `{$config['name']}`");
        $tmpPdo->exec("CREATE DATABASE `{$config['name']}` CHARACTER SET {$config['charset']} COLLATE utf8mb4_unicode_ci");

        // Connect to the new database
        $dsn = "mysql:host={$config['host']};dbname={$config['name']};charset={$config['charset']}";
        self::$pdo = new PDO($dsn, $config['user'], $config['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // Apply schema
        self::$pdo->exec(file_get_contents(__DIR__ . '/../db/schema.sql'));

        // Override the global Database connection getter
        Database::setTestConnection(self::$pdo);

    }

    protected function setUp(): void
    {
        // Reset data for each test
        self::$pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        self::$pdo->exec("TRUNCATE TABLE weapons;");
        self::$pdo->exec("TRUNCATE TABLE stores;");
        self::$pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
        self::$pdo->exec(file_get_contents(__DIR__ . '/../db/seeds.sql'));
    }

}
