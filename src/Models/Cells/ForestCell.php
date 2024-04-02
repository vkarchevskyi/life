<?php

declare(strict_types=1);

namespace App\Models\Cells;

use App\Models\CellsTypes\ForestCellTypes;

class ForestCell extends AbstractCell
{
    public readonly ForestCellTypes $type;

    public function __construct(ForestCellTypes $type, bool $alive, int $x, int $y, ?int $daysOfLive = 0)
    {
        parent::__construct($alive, $daysOfLive, $x, $y);

        $this->type = $type;
    }

    #[\Override] public function isAlive(): bool
    {
        return $this->alive;
    }

    #[\Override] public function becomeAlive(): void
    {
        $this->alive = true;
    }

    #[\Override] public function becomeDead(): void
    {
        $this->alive = false;
    }

    #[\Override] public function getDeadCell(): string
    {
        return " ";
    }

    #[\Override] public function getAliveCell(): string
    {
        return $this->type->value;
    }
}