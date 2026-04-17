<?php

// Autoload classes
spl_autoload_register(function ($className) {
    $file = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

use Core\Security\CSRF;
use Core\Support\Config;
use Core\Support\CONSTANT;

// Load helpers
require_once __DIR__ . '/Core/Support/helpers.php';

// Load configuration
Config::load();

// Set timezone
date_default_timezone_set(Config::get(CONSTANT::APP_TIMEZONE));

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token if not exists
if (!isset($_SESSION['_csrf_token'])) {
    CSRF::generateToken();
}

// Error handling
if (Config::get(CONSTANT::APP_DEBUG, false)) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Set error handler
set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// Set exception handler
set_exception_handler(function ($exception) {
    if (Config::get(CONSTANT::APP_DEBUG, false)) {
        echo "<h1>Exception</h1>";
        echo "<p><strong>Message:</strong> " . $exception->getMessage() . "</p>";
        echo "<p><strong>File:</strong> " . $exception->getFile() . "</p>";
        echo "<p><strong>Line:</strong> " . $exception->getLine() . "</p>";
        echo "<pre>" . $exception->getTraceAsString() . "</pre>";
    } else {
        abort(500, 'Internal Server Error');
    }
});
