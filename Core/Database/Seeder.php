<?php

namespace Core\Database;

abstract class Seeder
{
    /**
     * Run the seeder
     */
    abstract public function run(): void;

    /**
     * Call another seeder
     */
    protected function call(string $seederClass): void
    {
        $seeder = new $seederClass();
        $seeder->run();
        echo "Seeded: {$seederClass}\n";
    }
}
