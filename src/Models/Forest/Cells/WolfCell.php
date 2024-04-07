<?php

declare(strict_types=1);

namespace App\Models\Forest\Cells;

class WolfCell extends AbstractPredatorCell
{
    protected const int LIVING_DAYS = 8;

    public const int SATIETY = 1;

    public const int STRENGTH = 3;

    public const int PRIORITY = 2;

    public function __construct(int $x, int $y, int $livingDays = self::LIVING_DAYS)
    {
        parent::__construct($x, $y, true, $livingDays);

        $this->livingDays = $livingDays;
    }

    #[\Override] public function __toString(): string
    {
        return $this->alive ? '🐺' : $this->getDeadCell();
    }
}
