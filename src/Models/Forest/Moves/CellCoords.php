<?php

declare(strict_types=1);

namespace App\Models\Forest\Moves;

readonly class CellCoords
{
    public function __construct(public int $x, public int $y)
    {
    }
}