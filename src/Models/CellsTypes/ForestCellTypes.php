<?php

declare(strict_types=1);

namespace App\Models\CellsTypes;

enum ForestCellTypes: string
{
    case WOLF = '🐺';

    case RABBIT = '🐰';

    case BEAR = '🐻';

    case PLANT = '🌱';

    case WATER = '🟦';
}
