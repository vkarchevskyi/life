<?php

declare(strict_types=1);

namespace App\Models\Forest\Cells;

use App\Models\Forest\Fields\ForestField;
use App\Models\Forest\Moves\CellCoords;
use App\Models\Forest\Moves\CellMove;

abstract class AbstractLiveCell extends AbstractForestCell
{
    protected const int LIVING_DAYS = -1;

    protected bool $alive;

    protected int $livingDays;

    public function __construct(int $x, int $y, bool $alive, int $livingDays = self::LIVING_DAYS)
    {
        parent::__construct($x, $y);

        $this->alive = $alive;
        $this->livingDays = $livingDays;
    }

    abstract public function findTheBestCellToMove(ForestField $field): CellCoords;

    abstract public function createMove(AbstractLiveCell $cellToMove): CellMove;

    public function getDeadCell(): string
    {
        return "  ";
    }

    public function isAlive(): bool
    {
        return $this->alive;
    }

    public function getLivingDays(): int
    {
        return $this->livingDays;
    }
}