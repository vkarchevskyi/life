<?php

declare(strict_types=1);

namespace App\Models\ConwaysGame\Cells;

use App\Models\AbstractGame\Cells\AbstractCell;

abstract class AbstractConwaysCell extends AbstractCell
{
    abstract public function getNextMoveCell(int $neighborsCount): AbstractConwaysCell;
}
