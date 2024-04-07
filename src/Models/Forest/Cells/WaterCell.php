<?php

declare(strict_types=1);

namespace App\Models\Forest\Cells;

class WaterCell extends AbstractStaticCell
{
    public const int CHANCE_TO_SPAWN = 1;

    #[\Override] public function __toString(): string
    {
        return '🟦';
    }
}
