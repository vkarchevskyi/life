<?php

declare(strict_types=1);

namespace App\Models\AbstractGame\Cells;

abstract class AbstractCell
{
    protected bool $alive;

    protected int $x;

    protected int $y;

    public function __construct(bool $alive, int $x, int $y)
    {
        $this->alive = $alive;
        $this->x = $x;
        $this->y = $y;
    }

    abstract public function __toString(): string;

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function isAlive(): bool
    {
        return $this->alive;
    }
}
