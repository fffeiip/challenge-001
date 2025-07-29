<?php

class Database
{
    private static $testInstance = null;
    private static $instance = null;

    
    private function __construct()
    {
    }

  
    private function __clone()
    {
    }

    /**
     * Allows injecting a PDO connection for testing purposes.
     */
    public static function setTestConnection(PDO $pdo): void
    {
        self::$testInstance = $pdo;
    }
   
    public static function getConnection(): PDO
    {
        if (self::$testInstance !== null) {
            return self::$testInstance;
        }
        if (self::$instance === null) {
            // Get credentials from global config loaded in bootstrap.php
            $config = $GLOBALS['db_config'];
            
            $host = $config['host'];
            $db   = $config['name'];
            $user = $config['user'];
            $pass = $config['pass'];
            $charset = $config['charset'];
            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on error
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch associative arrays
                PDO::ATTR_EMULATE_PREPARES   => false,                  // Use native prepared statements
            ];

            self::$instance = new PDO($dsn, $user, $pass, $options);
        }

        return self::$instance;
    }
}
