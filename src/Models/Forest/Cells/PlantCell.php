<?php

declare(strict_types=1);

namespace App\Models\Forest\Cells;

use App\Models\Forest\Fields\ForestField;
use App\Models\Forest\Moves\CellMove;

class PlantCell extends AbstractNatureCell
{
    protected const int DEATH_DAYS = 2;

    protected const int LIVING_DAYS = 6;

    public const bool EATABLE = true;

    public function __construct(
        int $x,
        int $y,
        bool $alive,
        int $livingDays = self::LIVING_DAYS,
        int $deathDays = self::DEATH_DAYS
    ) {
        parent::__construct($x, $y, $alive, $livingDays, $deathDays);
    }

    #[\Override] public function __toString(): string
    {
        return $this->alive ? '🌱' : $this->getDeadCell();
    }

    #[\Override] public function findTheBestCellToMove(ForestField $field): AbstractForestCell
    {
        return $this;
    }

    #[\Override] public function createMove(AbstractLiveCell $cellToMove): CellMove
    {
        if ($this->alive) {
            $this->livingDays--;

            if ($this->livingDays <= 0) {
                $this->alive = false;
                $this->deathDays = self::DEATH_DAYS;
            }
        } else {
            $this->deathDays--;

            if ($this->deathDays <= 0) {
                $this->alive = true;
                $this->livingDays = self::LIVING_DAYS;
            }
        }

        return new CellMove($this);
    }
}
