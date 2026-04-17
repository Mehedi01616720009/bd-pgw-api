#!/usr/bin/env php
<?php

require_once __DIR__ . '/app.php';

use Core\Database\Migrator;

$command = $argv[1] ?? 'help';

switch ($command) {
    case 'migrate':
        echo "Running migrations...\n";
        Migrator::run();
        break;

    case 'rollback':
        echo "Rolling back migrations...\n";
        Migrator::rollback();
        break;

    case 'reset':
        echo "Resetting all migrations...\n";
        Migrator::reset();
        break;

    case 'refresh':
        echo "Refreshing migrations...\n";
        Migrator::refresh();
        break;

    case 'help':
    default:
        echo "Available commands:\n";
        echo "  php migrate.php migrate  - Run all pending migrations\n";
        echo "  php migrate.php rollback - Rollback last batch\n";
        echo "  php migrate.php reset    - Reset all migrations\n";
        echo "  php migrate.php refresh  - Reset and re-run all migrations\n";
        break;
}
