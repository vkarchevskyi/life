<?php

declare(strict_types=1);

namespace App\Cells;

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

    #[\Override] public static function getDeadCell(): string
    {
        return " ";
    }

    #[\Override] public static function getAliveCell(): string
    {
        return "*";
    }

    #[\Override] public function __toString(): string
    {
        return $this->alive
            ? self::getAliveCell()
            : self::getDeadCell();
    }
}
