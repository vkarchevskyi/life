<?php

declare(strict_types=1);

namespace App\Models\ConwaysGame\Cells;

use App\Models\AbstractGame\Cells\AbstractCell;

abstract class AbstractConwaysCell extends AbstractCell
{
    protected bool $alive;

    public function __construct(bool $alive, int $x, int $y)
    {
        parent::__construct($x, $y);

        $this->alive = $alive;
    }

    abstract public function getNextMoveCell(int $neighborsCount): AbstractConwaysCell;

    public function isAlive(): bool
    {
        return $this->alive;
    }
}
