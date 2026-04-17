<?php

namespace Core\Database;

use PDO;

class Schema
{
    private static PDO $pdo;

    /**
     * Create table
     */
    public static function create(string $table, callable $callback): void
    {
        self::$pdo = Connection::getInstance();

        $blueprint = new Blueprint($table);
        $callback($blueprint);

        $sql = $blueprint->toSql();
        self::$pdo->exec($sql);
    }

    /**
     * Drop table if exists
     */
    public static function dropIfExists(string $table): void
    {
        self::$pdo = Connection::getInstance();
        self::$pdo->exec("DROP TABLE IF EXISTS `{$table}`");
    }

    /**
     * Check if table exists
     */
    public static function hasTable(string $table): bool
    {
        self::$pdo = Connection::getInstance();
        $stmt = self::$pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Rename table
     */
    public static function rename(string $from, string $to): void
    {
        self::$pdo = Connection::getInstance();
        self::$pdo->exec("RENAME TABLE `{$from}` TO `{$to}`");
    }

    /**
     * Add column to existing table
     */
    public static function table(string $table, callable $callback): void
    {
        self::$pdo = Connection::getInstance();

        $blueprint = new Blueprint($table, false);
        $callback($blueprint);

        $alterStatements = $blueprint->toAlterSql();
        foreach ($alterStatements as $sql) {
            self::$pdo->exec($sql);
        }
    }
}
