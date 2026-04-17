<?php

namespace Core\Database;

class ForeignKey
{
    private Blueprint $blueprint;
    private string $column;
    private string $references;
    private string $on;
    private string $onDelete = 'CASCADE';
    private string $onUpdate = 'CASCADE';

    public function __construct(Blueprint $blueprint, string $column)
    {
        $this->blueprint = $blueprint;
        $this->column = $column;
    }

    /**
     * Set reference column
     */
    public function references(string $column): self
    {
        $this->references = $column;
        return $this;
    }

    /**
     * Set reference table
     */
    public function on(string $table): self
    {
        $this->on = $table;
        return $this;
    }

    /**
     * Set on delete action
     */
    public function onDelete(string $action): self
    {
        $this->onDelete = $action;
        return $this;
    }

    /**
     * Set on update action
     */
    public function onUpdate(string $action): self
    {
        $this->onUpdate = $action;
        return $this;
    }

    /**
     * Cascade on delete
     */
    public function cascadeOnDelete(): self
    {
        $this->onDelete = 'CASCADE';
        return $this;
    }

    /**
     * Restrict on delete
     */
    public function restrictOnDelete(): self
    {
        $this->onDelete = 'RESTRICT';
        return $this;
    }

    /**
     * Set null on delete
     */
    public function nullOnDelete(): self
    {
        $this->onDelete = 'SET NULL';
        return $this;
    }

    /**
     * Finalize the foreign key
     */
    public function __destruct()
    {
        if (isset($this->references) && isset($this->on)) {
            $this->blueprint->addForeignKey(
                $this->column,
                $this->references,
                $this->on,
                $this->onDelete,
                $this->onUpdate
            );
        }
    }
}
