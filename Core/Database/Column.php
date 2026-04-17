<?php

namespace Core\Database;

class Column
{
    private string $name;
    private string $type;
    private bool $isNullable = false;
    private $defaultValue = null;
    private bool $hasDefault = false;
    private bool $isUnsigned = false;
    private bool $isUnique = false;
    private ?string $comment = null;

    public function __construct(string $name, string $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * Make column nullable
     */
    public function nullable(): self
    {
        $this->isNullable = true;
        return $this;
    }

    /**
     * Set default value
     */
    public function default($value): self
    {
        $this->defaultValue = $value;
        $this->hasDefault = true;
        return $this;
    }

    /**
     * Make column unsigned
     */
    public function unsigned(): self
    {
        $this->isUnsigned = true;
        return $this;
    }

    /**
     * Make column unique
     */
    public function unique(): self
    {
        $this->isUnique = true;
        return $this;
    }

    /**
     * Add comment
     */
    public function comment(string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * Generate SQL
     */
    public function toSql(): string
    {
        $sql = "`{$this->name}` {$this->type}";

        if ($this->isUnsigned) {
            $sql .= " UNSIGNED";
        }

        if (!$this->isNullable) {
            $sql .= " NOT NULL";
        } else {
            $sql .= " NULL";
        }

        if ($this->hasDefault) {
            if ($this->defaultValue === null) {
                $sql .= " DEFAULT NULL";
            } elseif (is_string($this->defaultValue)) {
                $sql .= " DEFAULT '{$this->defaultValue}'";
            } elseif (is_bool($this->defaultValue)) {
                $sql .= " DEFAULT " . ($this->defaultValue ? '1' : '0');
            } else {
                $sql .= " DEFAULT {$this->defaultValue}";
            }
        }

        if ($this->isUnique) {
            $sql .= " UNIQUE";
        }

        if ($this->comment !== null) {
            $sql .= " COMMENT '{$this->comment}'";
        }

        return $sql;
    }
}
