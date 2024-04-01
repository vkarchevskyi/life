<?php

declare(strict_types=1);

namespace App\Models\CellsTypes;

enum ForestCellTypes: string
{
    case WOLF_CHAR = '🐺';

    case RABBIT_CHAR = '🐰';

    case BEAR_CHAR = '🐻';

    case PLANT = '🌱';

    case WATER = '🟦';
}
