<?php

declare(strict_types=1);

namespace App\Models\Forest\Moves;

use App\Models\Forest\Cells\AbstractForestCell;

class CellMove
{
    private AbstractForestCell $nextCell;

    private ?AbstractForestCell $previousCell;

    public function __construct(AbstractForestCell $nextCell, ?AbstractForestCell $previousCell = null)
    {
        $this->nextCell = $nextCell;
        $this->previousCell = $previousCell;
    }

    public function getNextCell(): AbstractForestCell
    {
        return $this->nextCell;
    }

    public function getPreviousCell(): ?AbstractForestCell
    {
        return $this->previousCell;
    }
}
