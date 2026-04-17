<?php

namespace Core\Database;

class Migrator
{
    private static string $migrationPath = __DIR__ . '/../../Database/Migrations/';
    private static string $migrationsTable = 'migrations';

    /**
     * Run all pending migrations
     */
    public static function run(): void
    {
        self::createMigrationsTable();

        $files = glob(self::$migrationPath . '*.php');
        sort($files);

        $executed = self::getExecutedMigrations();

        foreach ($files as $file) {
            $migrationName = basename($file, '.php');

            if (in_array($migrationName, $executed)) {
                echo "Skipped: {$migrationName}\n";
                continue;
            }

            require_once $file;

            $className = self::getClassName($migrationName);

            if (class_exists($className)) {
                $migration = new $className();
                $migration->up();

                self::logMigration($migrationName);
                echo "Migrated: {$migrationName}\n";
            }
        }

        echo "Migration completed!\n";
    }

    /**
     * Rollback last batch
     */
    public static function rollback(): void
    {
        $batch = self::getLastBatch();

        if ($batch === 0) {
            echo "Nothing to rollback.\n";
            return;
        }

        $migrations = self::getMigrationsByBatch($batch);

        foreach (array_reverse($migrations) as $migration) {
            $file = self::$migrationPath . $migration . '.php';

            if (file_exists($file)) {
                require_once $file;

                $className = self::getClassName($migration);

                if (class_exists($className)) {
                    $instance = new $className();
                    $instance->down();

                    self::removeMigration($migration);
                    echo "Rolled back: {$migration}\n";
                }
            }
        }

        echo "Rollback completed!\n";
    }

    /**
     * Reset all migrations
     */
    public static function reset(): void
    {
        $migrations = self::getAllMigrations();

        foreach (array_reverse($migrations) as $migration) {
            $file = self::$migrationPath . $migration . '.php';

            if (file_exists($file)) {
                require_once $file;

                $className = self::getClassName($migration);

                if (class_exists($className)) {
                    $instance = new $className();
                    $instance->down();

                    echo "Rolled back: {$migration}\n";
                }
            }
        }

        DB::raw("TRUNCATE TABLE " . self::$migrationsTable);
        echo "All migrations reset!\n";
    }

    /**
     * Refresh migrations (reset + run)
     */
    public static function refresh(): void
    {
        self::reset();
        self::run();
    }

    /**
     * Create migrations table
     */
    private static function createMigrationsTable(): void
    {
        if (!Schema::hasTable(self::$migrationsTable)) {
            Schema::create(self::$migrationsTable, function (Blueprint $table) {
                $table->id();
                $table->string('migration');
                $table->integer('batch');
                $table->timestamps();
            });
        }
    }

    /**
     * Get executed migrations
     */
    private static function getExecutedMigrations(): array
    {
        $results = DB::table(self::$migrationsTable)
            ->select('migration')
            ->get();

        return array_map(fn($row) => $row->migration, $results);
    }

    /**
     * Log migration
     */
    private static function logMigration(string $migration): void
    {
        $batch = self::getLastBatch() + 1;

        DB::table(self::$migrationsTable)->insert([
            'migration' => $migration,
            'batch' => $batch
        ]);
    }

    /**
     * Remove migration log
     */
    private static function removeMigration(string $migration): void
    {
        DB::table(self::$migrationsTable)
            ->where('migration', $migration)
            ->delete();
    }

    /**
     * Get last batch number
     */
    private static function getLastBatch(): int
    {
        $result = DB::raw("SELECT MAX(batch) as batch FROM " . self::$migrationsTable);
        return (int) ($result[0]->batch ?? 0);
    }

    /**
     * Get migrations by batch
     */
    private static function getMigrationsByBatch(int $batch): array
    {
        $results = DB::table(self::$migrationsTable)
            ->where('batch', $batch)
            ->select('migration')
            ->get();

        return array_map(fn($row) => $row->migration, $results);
    }

    /**
     * Get all migrations
     */
    private static function getAllMigrations(): array
    {
        $results = DB::table(self::$migrationsTable)
            ->select('migration')
            ->get();

        return array_map(fn($row) => $row->migration, $results);
    }

    /**
     * Extract class name from migration filename
     */
    private static function getClassName(string $filename): string
    {
        // Format: 2024_01_01_000000_create_users_table
        $parts = explode('_', $filename);

        // Remove timestamp parts (first 4 elements: year, month, day, time)
        $classParts = array_slice($parts, 4);

        // Convert to PascalCase
        $className = implode('', array_map('ucfirst', $classParts));

        return $className;
    }
}
