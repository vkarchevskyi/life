<?php

declare(strict_types=1);

namespace App\Models\AbstractGame\Cells;

abstract class AbstractCell
{
    protected int $x;

    protected int $y;

    public function __construct(int $x, int $y)
    {
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
}
