<?php

declare(strict_types=1);

namespace App\Models\Forest\Cells;

class BearCell extends AbstractPredatorCell
{
    protected const int LIVING_DAYS = 10;

    public const int SATIETY = 2;

    public const int STRENGTH = 5;

    public const int PRIORITY = 3;

    public function __construct(int $x, int $y, int $livingDays = self::LIVING_DAYS)
    {
        parent::__construct($x, $y, true, $livingDays);

        $this->livingDays = $livingDays;
    }

    #[\Override] public function __toString(): string
    {
        return $this->alive ? '🐻' : $this->getDeadCell();
    }
}
