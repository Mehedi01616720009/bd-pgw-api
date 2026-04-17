<?php

namespace Core\Database;

class Blueprint
{
    private string $table;
    private array $columns = [];
    private bool $creating = true;

    public function __construct(string $table, bool $creating = true)
    {
        $this->table = $table;
        $this->creating = $creating;
    }

    /**
     * Auto-increment ID
     */
    public function id(string $name = 'id'): self
    {
        $this->columns[] = "`{$name}` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY";
        return $this;
    }

    /**
     * String column
     */
    public function string(string $name, int $length = 255): Column
    {
        $column = new Column($name, "VARCHAR({$length})");
        $this->columns[] = $column;
        return $column;
    }

    /**
     * Text column
     */
    public function text(string $name): Column
    {
        $column = new Column($name, "TEXT");
        $this->columns[] = $column;
        return $column;
    }

    /**
     * Integer column
     */
    public function integer(string $name): Column
    {
        $column = new Column($name, "INT");
        $this->columns[] = $column;
        return $column;
    }

    /**
     * Big integer column
     */
    public function bigInteger(string $name): Column
    {
        $column = new Column($name, "BIGINT");
        $this->columns[] = $column;
        return $column;
    }

    /**
     * Tiny integer column
     */
    public function tinyInteger(string $name): Column
    {
        $column = new Column($name, "TINYINT");
        $this->columns[] = $column;
        return $column;
    }

    /**
     * Boolean column
     */
    public function boolean(string $name): Column
    {
        $column = new Column($name, "TINYINT(1)");
        $this->columns[] = $column;
        return $column;
    }

    /**
     * Decimal column
     */
    public function decimal(string $name, int $precision = 8, int $scale = 2): Column
    {
        $column = new Column($name, "DECIMAL({$precision}, {$scale})");
        $this->columns[] = $column;
        return $column;
    }

    /**
     * Float column
     */
    public function float(string $name): Column
    {
        $column = new Column($name, "FLOAT");
        $this->columns[] = $column;
        return $column;
    }

    /**
     * Double column
     */
    public function double(string $name): Column
    {
        $column = new Column($name, "DOUBLE");
        $this->columns[] = $column;
        return $column;
    }

    /**
     * Date column
     */
    public function date(string $name): Column
    {
        $column = new Column($name, "DATE");
        $this->columns[] = $column;
        return $column;
    }

    /**
     * DateTime column
     */
    public function dateTime(string $name): Column
    {
        $column = new Column($name, "DATETIME");
        $this->columns[] = $column;
        return $column;
    }

    /**
     * Timestamp column
     */
    public function timestamp(string $name): Column
    {
        $column = new Column($name, "TIMESTAMP");
        $column->nullable();
        return $column;
    }

    /**
     * Enum column
     */
    public function enum(string $name, array $values): Column
    {
        $valuesList = "'" . implode("','", $values) . "'";
        $column = new Column($name, "ENUM({$valuesList})");
        $this->columns[] = $column;
        return $column;
    }

    /**
     * JSON column
     */
    public function json(string $name): Column
    {
        $column = new Column($name, "JSON");
        $this->columns[] = $column;
        return $column;
    }

    /**
     * Created at and updated at timestamps
     */
    public function timestamps(): self
    {
        $this->columns[] = "`createdAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $this->columns[] = "`updatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        return $this;
    }

    /**
     * Soft deletes
     */
    public function softDeletes(): self
    {
        $column = new Column('deletedAt', "TIMESTAMP");
        $column->nullable();
        $this->columns[] = $column;
        return $this;
    }

    /**
     * Foreign key
     */
    public function foreign(string $column): ForeignKey
    {
        return new ForeignKey($this, $column);
    }

    /**
     * Add index
     */
    public function index(string $column, string | null $name = null): self
    {
        $indexName = $name ?? "{$this->table}_{$column}_index";
        $this->columns[] = "INDEX `{$indexName}` (`{$column}`)";
        return $this;
    }

    /**
     * Add unique index
     */
    public function unique(string $column, string | null $name = null): self
    {
        $indexName = $name ?? "{$this->table}_{$column}_unique";
        $this->columns[] = "UNIQUE INDEX `{$indexName}` (`{$column}`)";
        return $this;
    }

    /**
     * Generate CREATE TABLE SQL
     */
    public function toSql(): string
    {
        $columns = [];

        foreach ($this->columns as $column) {
            if ($column instanceof Column) {
                $columns[] = $column->toSql();
            } else {
                $columns[] = $column;
            }
        }

        $sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` (\n";
        $sql .= "    " . implode(",\n    ", $columns);
        $sql .= "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        return $sql;
    }

    /**
     * Generate ALTER TABLE SQL statements
     */
    public function toAlterSql(): array
    {
        $statements = [];

        foreach ($this->columns as $column) {
            if ($column instanceof Column) {
                $statements[] = "ALTER TABLE `{$this->table}` ADD COLUMN " . $column->toSql();
            }
        }

        return $statements;
    }

    /**
     * Add foreign key constraint
     */
    public function addForeignKey(string $column, string $references, string $on, string $onDelete = 'CASCADE', string $onUpdate = 'CASCADE'): void
    {
        $constraintName = "{$this->table}_{$column}_foreign";
        $this->columns[] = "CONSTRAINT `{$constraintName}` FOREIGN KEY (`{$column}`) REFERENCES `{$on}`(`{$references}`) ON DELETE {$onDelete} ON UPDATE {$onUpdate}";
    }
}
