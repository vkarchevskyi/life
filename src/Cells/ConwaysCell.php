<?php

declare(strict_types=1);

namespace App\Cells;

class ConwaysCell extends AbstractCell
{
    #[\Override] final public function isAlive(): bool
    {
        return $this->alive;
    }

    #[\Override] final public function born(): static
    {
        $this->alive = true;

        return $this;
    }

    #[\Override] final public function kill(): static
    {
        $this->alive = false;

        return $this;
    }

    #[\Override] final public static function getDeadCell(): string
    {
        return " ";
    }

    #[\Override] final public static function getAliveCell(): string
    {
        return "*";
    }

    #[\Override] final public function __toString(): string
    {
        return $this->alive
            ? self::getAliveCell()
            : self::getDeadCell();
    }
}
