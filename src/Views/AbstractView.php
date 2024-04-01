<?php

declare(strict_types=1);

namespace App\Views;

abstract class AbstractView
{
    abstract public function getX(): int;

    abstract public function getY(): int;

    abstract public function getConnectedBorders(): bool;

    abstract public function getCommand(): int;

    abstract public function getStepQuantity(): int;

    abstract public function getFieldType(): string;
}
