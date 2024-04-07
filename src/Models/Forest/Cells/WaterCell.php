<?php

declare(strict_types=1);

namespace App\Models\Forest\Cells;

class WaterCell extends AbstractStaticCell
{
    #[\Override] public function __toString(): string
    {
        return '🟦';
    }
}
