<?php

declare(strict_types=1);

namespace App\Models\Forest\Cells;

class RabbitCell extends AbstractPreyCell
{
    protected const int LIVING_DAYS = 6;

    public const int SATIETY = 3;

    public const int PRIORITY = 1;

    public const int CHANCE_TO_SPAWN = 6;

    public function __construct(int $x, int $y, int $livingDays = self::LIVING_DAYS)
    {
        parent::__construct($x, $y, true, $livingDays);
    }

    #[\Override] public function __toString(): string
    {
        return $this->alive ? '🐰' : $this->getDeadCell();
    }
}
