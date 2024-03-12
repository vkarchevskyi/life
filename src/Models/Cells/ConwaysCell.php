<?php

declare(strict_types=1);

namespace App\Models\Cells;

use App\Views\Console\EscapeCodes;

class ConwaysCell extends AbstractCell
{
    #[\Override] public function isAlive(): bool
    {
        return $this->alive;
    }

    #[\Override] public function becomeAlive(): void
    {
        $this->alive = true;
    }

    #[\Override] public function becomeDead(): void
    {
        $this->alive = false;
    }

    #[\Override] public function getDeadCell(): string
    {
        return " ";
    }

    #[\Override] public function getAliveCell(): string
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

    #[\Override] public function __toString(): string
    {
        return $this->alive
            ? $this->getAliveCell()
            : $this->getDeadCell();
    }
}
