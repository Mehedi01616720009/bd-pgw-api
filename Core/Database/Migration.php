<?php

namespace Core\Database;

abstract class Migration
{
    /**
     * Run the migration
     */
    abstract public function up(): void;

    /**
     * Reverse the migration
     */
    abstract public function down(): void;
}
