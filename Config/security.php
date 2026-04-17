<?php

use Core\Support\Config;

return [
    'csrf' => [
        'enabled' => Config::env('CSRF_ENABLED', true),
        'token_name' => '_csrf_token',
        'token_length' => 32,
        'expire_time' => 3600, // 1 hour in seconds
    ],

    'session' => [
        'lifetime' => 120, // minutes
        'secure' => Config::env('SESSION_SECURE', false),
        'http_only' => true,
        'same_site' => 'Lax', // Lax, Strict, None
    ],
];
