<?php

namespace Core\Database;

class DB
{
    /**
     * Create new query builder instance
     */
    public static function table(string $table): QueryBuilder
    {
        $pdo = Connection::getInstance();
        $builder = new QueryBuilder($pdo);
        return $builder->table($table);
    }

    /**
     * Execute raw query
     */
    public static function raw(string $sql, array $bindings = []): array
    {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetchAll();
    }

    /**
     * Begin transaction
     */
    public static function beginTransaction(): void
    {
        Connection::getInstance()->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public static function commit(): void
    {
        Connection::getInstance()->commit();
    }

    /**
     * Rollback transaction
     */
    public static function rollback(): void
    {
        Connection::getInstance()->rollBack();
    }
}
