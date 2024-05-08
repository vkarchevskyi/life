<?php

declare(strict_types=1);

namespace Test;

abstract class AppTest
{
    private function sendAnError(mixed $assertedValue, mixed $expectedValue): never
    {
        echo "Error! The following two objects are not the same!\n";
        var_dump($assertedValue);
        var_dump($expectedValue);

        exit(1);
    }

    protected function assertSame(mixed $assertedValue, mixed $expectedValue): void
    {
        if ($assertedValue !== $expectedValue) {
            $this->sendAnError($assertedValue, $expectedValue);
        }
    }

    protected function assertTrue(mixed $assertedValue): void
    {
        if ($assertedValue !== true) {
            $this->sendAnError($assertedValue, true);
        }
    }

    abstract public function run(): void;
}
