<?php

declare(strict_types=1);

namespace App\Models\ConwaysGame\Cells;

class DeadConwaysCell extends AbstractConwaysCell
{
    public function __construct(int $x, int $y)
    {
        parent::__construct(false, $x, $y);
    }

    public function __toString(): string
    {
        return " ";
    }

    #[\Override] public function getNextMoveCell(int $neighborsCount): AbstractConwaysCell
    {
        if ($neighborsCount === 3) {
            return new LiveConwaysCell($this->x, $this->y);
        }

        return new DeadConwaysCell($this->x, $this->y);
    }
}