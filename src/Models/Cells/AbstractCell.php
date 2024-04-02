<?php

declare(strict_types=1);

namespace App\Models\Cells;

abstract class AbstractCell
{
    public function __construct(
        protected bool $alive,
        protected int $x,
        protected int $y,
        protected ?int $livingDays = 0,
        protected ?int $deathDays = null,
    ) {
    }

    abstract public function isAlive(): bool;

    abstract public function becomeAlive(): void;

    abstract public function becomeDead(): void;

    abstract public function getDeadCell(): string;

    abstract public function getAliveCell(): string;

    public function getLivingDays(): int
    {
        return $this->livingDays;
    }

    public function __toString(): string
    {
        return $this->alive
            ? $this->getAliveCell()
            : $this->getDeadCell();
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }
}
