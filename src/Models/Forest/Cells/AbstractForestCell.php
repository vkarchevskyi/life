<?php

declare(strict_types=1);

namespace App\Models\Forest\Cells;

use App\Models\AbstractGame\Cells\AbstractCell;

abstract class AbstractForestCell extends AbstractCell
{
    public const int CHANCE_TO_SPAWN = -1;
}
