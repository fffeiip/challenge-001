<?php

/**
 * Application Bootstrap File
 *
 * This file is the single entry point for setting up the application environment.
 * It handles configuration, autoloading, and core initializations.
 */

// Set a constant for the project root directory
define('PROJECT_ROOT', dirname(__DIR__));

// Load Composer's autoloader
require_once PROJECT_ROOT . '/vendor/autoload.php';

// Load environment variables from .env file if it exists
if (file_exists(PROJECT_ROOT . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(PROJECT_ROOT);
    $dotenv->load();
}

// Load Database Configuration and make it available globally.
$GLOBALS['db_config'] = require PROJECT_ROOT . '/config/database.php';

// Start the session only for web requests. This is needed for flash messages and CSRF protection.
if (php_sapi_name() !== 'cli') {
    session_start();
}
