<?php

declare(strict_types=1);

namespace App\Models\Fields;

use App\Models\Cells\AbstractCell;
use App\Models\Cells\ConwaysCell;
use Random\RandomException;

class ConwaysField extends AbstractField
{
    public function __construct(int $xSize, int $ySize, bool $connectBorders)
    {
        parent::__construct($xSize, $ySize, $connectBorders);
    }

    #[\Override] public function nextStep(): void
    {
        $updatedField = [];

        for ($y = 0; $y < $this->ySize; $y++) {
            for ($x = 0; $x < $this->xSize; $x++) {
                $neighborsCount = $this->calculateNeighbors($x, $y);

                /* @var AbstractCell $cell */
                $cell = $this->gameField[$y][$x];

                if (!$cell->isAlive() && $neighborsCount === 3) {
                    $updatedField[$y][$x] = new ConwaysCell(true, 0);
                } elseif ($cell->isAlive() && $neighborsCount >= 2 && $neighborsCount <= 3) {
                    $updatedField[$y][$x] = new ConwaysCell(true, $cell->getDaysOfLive() + 1);
                } else {
                    $updatedField[$y][$x] = new ConwaysCell(false);
                }
            }
        }

        $this->gameField = $updatedField;
    }

    /**
     * @throws RandomException
     */
    #[\Override] public function generateField(): void
    {
        $gameField = [];

        for ($y = 0; $y < $this->ySize; $y++) {
            $gameField[$y] = [];

            for ($x = 0; $x < $this->xSize; $x++) {
                $gameField[$y][$x] = new ConwaysCell((bool)random_int(0, 1));
            }
        }

        $this->gameField = $gameField;
    }

    protected function calculateNeighbors(int $x, int $y): int
    {
        $neighbors = $this->getNeighborhoods($x, $y);

        /* @var AbstractCell $cell */
        return array_reduce($neighbors, function ($counter, $cell) {
            $counter += (int)$cell->isAlive();
            return $counter;
        }, 0);
    }
}
