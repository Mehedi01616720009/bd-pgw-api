<?php

use Core\Support\Config;

return [
    'name' => Config::env('APP_NAME', 'PHP MVC Framework'),
    'url' => Config::env('APP_URL', 'http://localhost:8000/'),
    'public_url' => Config::env('PUBLIC_URL', 'http://localhost:8000/public/'),
    'subdirectory' => Config::env('APP_SUBDIRECTORY', ''),
    'timezone' => Config::env('APP_TIMEZONE', 'Asia/Dhaka'),
    'debug' => Config::env('APP_DEBUG', true),
    'environment' => Config::env('APP_ENV', 'local'), // local, production
    'logo' => Config::env('APP_LOGO', 'http://localhost:8000/uploads/logo.png'),
];
