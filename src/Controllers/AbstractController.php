<?php

declare(strict_types=1);

namespace App\Controllers;

abstract class AbstractController
{
    abstract protected function run(): void;

    abstract protected function getFieldXSize(): int;

    abstract protected function getFieldYSize(): int;

    abstract protected function getConnectedBorders(): bool;

    abstract protected function createField(): void;

    abstract protected function play(int $stepQuantity): void;

    abstract protected function getStepQuantity(): int;
}
