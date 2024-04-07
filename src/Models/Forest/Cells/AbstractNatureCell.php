<?php

declare(strict_types=1);

namespace App\Models\Forest\Cells;

abstract class AbstractNatureCell extends AbstractLiveCell
{
    protected const int DEATH_DAYS = -1;

    public const bool EATABLE = true;

    protected int $deathDays;

    public function __construct(
        int $x,
        int $y,
        bool $alive,
        int $livingDays = self::LIVING_DAYS,
        int $deathDays = self::DEATH_DAYS,
    ) {
        parent::__construct($x, $y, $alive, $livingDays);

        $this->deathDays = $deathDays;
    }
}