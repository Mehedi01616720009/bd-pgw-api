<?php

use Core\Routing\Router;

require_once __DIR__ . '/../app.php';

// Load routes
require_once __DIR__ . '/../routes.php';

define('BASE_PATH', dirname(__DIR__));

// Dispatch the router
$router = new Router();
$router->dispatch();
