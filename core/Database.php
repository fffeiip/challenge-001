<?php
namespace Core;

use PDO;
use PDOException;

class Database
{
    private $pdo;
    private static $instance;

    private function __construct(array $config)
    {
        try {
            if ($config['driver'] === 'sqlite') {
                $dsn = "sqlite:" . $config['database'];
                $this->pdo = new PDO($dsn);
            } else {
                $dsn = sprintf(
                    "%s:host=%s;dbname=%s;charset=%s",
                    $config['driver'],
                    $config['host'],
                    $config['database'],
                    $config['charset']
                );
                $this->pdo = new PDO($dsn, $config['username'], $config['password']);
            }
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
        }
    }

    /**
     * Returns the singleton Database instance.
     */
    public static function getInstance(): Database
    {
        if (! self::$instance) {
            $config = require __DIR__ . '/../config/database.php';
            // ensure directory exists
            if ($config['driver'] === 'sqlite') {
                $dir = dirname($config['database']);
                if (! is_dir($dir)) mkdir($dir, 0755, true);
            }
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }
}