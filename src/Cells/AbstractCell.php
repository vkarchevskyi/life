<?php

declare(strict_types=1);

namespace App\Cells;

abstract class AbstractCell
{
    public function __construct(protected bool $alive)
    {
    }

    abstract public function __toString(): string;

    abstract public function isAlive(): bool;

    abstract public function becomeAlive(): void;

    abstract public function becomeDead(): void;

    abstract public static function getDeadCell(): string;

    abstract public static function getAliveCell(): string;
}
