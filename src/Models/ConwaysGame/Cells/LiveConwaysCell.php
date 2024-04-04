<?php

declare(strict_types=1);

namespace App\Models\ConwaysGame\Cells;

use App\Views\Console\Constants\EscapeCodes;

class LiveConwaysCell extends AbstractConwaysCell
{
    protected int $daysOfLive;

    public function __construct(int $x, int $y, int $daysOfLive = 0)
    {
        parent::__construct(true, $x, $y);

        $this->daysOfLive = $daysOfLive;
    }

    #[\Override] public function getNextMoveCell(int $neighborsCount): AbstractConwaysCell
    {
        $y = $this->getY();
        $x = $this->getX();

        if ($neighborsCount >= 2 && $neighborsCount <= 3) {
            return new LiveConwaysCell($x, $y, $this->getDaysOfLive() + 1);
        }

        return new DeadConwaysCell($x, $y);
    }

    #[\Override] public function __toString(): string
    {
        $startColor = EscapeCodes::RED;

        if ($this->getDaysOfLive() > 0) {
            $startColor = EscapeCodes::GREEN;
        }

        if ($this->getDaysOfLive() >= 3) {
            $startColor = EscapeCodes::MAGENTA;
        }

        return $startColor->value . "*" . EscapeCodes::RESET->value;
    }

    public function getDaysOfLive(): int
    {
        return $this->daysOfLive;
    }
}