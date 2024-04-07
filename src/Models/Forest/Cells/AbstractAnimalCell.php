<?php

declare(strict_types=1);

namespace App\Models\Forest\Cells;

abstract class AbstractAnimalCell extends AbstractLiveCell
{
    public const int SATIETY = -1;

    public const int PRIORITY = -1;
}
