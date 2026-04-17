<?php

use Core\Support\Config;

return [
    'host' => Config::env('DB_HOST', 'localhost'),
    'user' => Config::env('DB_USER', 'root'),
    'pass' => Config::env('DB_PASS', '12345678'),
    'db_name' => Config::env('DB_NAME', 'pgw'),
    'charset' => Config::env('DB_CHARSET', 'utf8mb4'),
    'collation' => Config::env('DB_COLLATION', 'utf8mb4_unicode_ci'),
];
