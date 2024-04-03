<?php

declare(strict_types=1);

namespace App\Models\Fields;

use App\Models\Cells\AbstractCell;
use App\Models\Cells\ConwaysCell;
use Random\RandomException;

class ConwaysField extends AbstractField
{
    /**
     * Make next step of simulation in current field.
     * If dead cell has 3 neighbors, it becomes alive.
     * If live cell has 2 or 3 neighbors, it stays alive.
     * Otherwise, live cell become dead cell and dead cell remains dead.
     *
     * @return void
     */
    #[\Override] public function nextStep(): void
    {
        $updatedField = [];

        for ($y = 0; $y < $this->ySize; $y++) {
            for ($x = 0; $x < $this->xSize; $x++) {
                $neighborsCount = $this->calculateNeighbors($x, $y);

                /* @var AbstractCell $cell */
                $cell = $this->gameField[$y][$x];

                if (!$cell->isAlive() && $neighborsCount === 3) {
                    $updatedField[$y][$x] = new ConwaysCell(true, $x, $y, 0);
                } elseif ($cell->isAlive() && $neighborsCount >= 2 && $neighborsCount <= 3) {
                    $updatedField[$y][$x] = new ConwaysCell(true, $x, $y, $cell->getLivingDays() + 1);
                } else {
                    $updatedField[$y][$x] = new ConwaysCell(false, $x, $y);
                }
            }
        }

        $this->gameField = $updatedField;
    }

    /**
     * Randomly generate game field
     *
     * @return void
     * @throws RandomException
     */
    #[\Override] public function generateField(): void
    {
        $gameField = [];

        for ($y = 0; $y < $this->ySize; $y++) {
            $gameField[$y] = [];

            for ($x = 0; $x < $this->xSize; $x++) {
                $gameField[$y][$x] = new ConwaysCell((bool)random_int(0, 1), $x, $y);
            }
        }

        $this->gameField = $gameField;
    }

    /**
     * Calculate the quantity of alive cells around cell with given coordinates.
     *
     * @param int $x
     * @param int $y
     * @return int
     */
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
