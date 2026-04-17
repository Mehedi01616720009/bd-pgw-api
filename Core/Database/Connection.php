<?php

namespace Core\Database;

use PDO;
use PDOException;
use Core\Support\Config;
use Core\Support\CONSTANT;

class Connection
{
    private static ?PDO $instance = null;

    /**
     * Get PDO connection instance (Singleton)
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$instance = self::connect();
        }
        return self::$instance;
    }

    /**
     * Create new PDO connection
     */
    private static function connect(): PDO
    {
        try {
            $host = Config::get(CONSTANT::DATABASE_HOST);
            $user = Config::get(CONSTANT::DATABASE_USER);
            $pass = Config::get(CONSTANT::DATABASE_PASS);
            $dbName = Config::get(CONSTANT::DATABASE_NAME);
            $charset = Config::get(CONSTANT::DATABASE_CHARSET);

            $dsn = "mysql:host={$host};charset={$charset}";
            $conn = new PDO($dsn, $user, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

            // Create database if not exists
            $conn->exec("CREATE DATABASE IF NOT EXISTS {$dbName}");
            $conn->exec("USE {$dbName}");

            return $conn;
        } catch (PDOException $e) {
            die("Database Connection Failed: " . $e->getMessage());
        }
    }

    /**
     * Close connection
     */
    public static function close(): void
    {
        self::$instance = null;
    }
}
