<?php

declare(strict_types=1);

namespace App\Models\Cells;

abstract class AbstractCell
{
    protected bool $alive;

    protected int $daysOfLive;

    public function __construct(bool $alive, int $daysOfLive = 0)
    {
        $this->alive = $alive;

        if ($daysOfLive < 0) {
            throw new \LogicException('Days of live cannot be less than zero.');
        }

        $this->daysOfLive = $daysOfLive;
    }

    abstract public function __toString(): string;

    abstract public function isAlive(): bool;

    abstract public function becomeAlive(): void;

    abstract public function becomeDead(): void;

    abstract public function getDeadCell(): string;

    abstract public function getAliveCell(): string;

    public function getDaysOfLive(): int
    {
        return $this->daysOfLive;
    }
}
