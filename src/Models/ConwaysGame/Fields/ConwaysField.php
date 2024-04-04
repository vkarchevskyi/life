<?php

declare(strict_types=1);

namespace App\Models\ConwaysGame\Fields;

use App\Models\AbstractGame\Cells\AbstractCell;
use App\Models\AbstractGame\Fields\AbstractField;
use App\Models\ConwaysGame\Cells\AbstractConwaysCell;
use App\Models\ConwaysGame\Cells\DeadConwaysCell;
use App\Models\ConwaysGame\Cells\LiveConwaysCell;
use Random\RandomException;

class ConwaysField extends AbstractField
{
    /** @var array<array<AbstractConwaysCell>> $gameField */
    protected array $gameField;

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

                $updatedField[$y][$x] = $this->gameField[$y][$x]->getNextMoveCell($neighborsCount);
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
                $cellType = random_int(0, 1) ? LiveConwaysCell::class : DeadConwaysCell::class;

                $gameField[$y][$x] = new $cellType($x, $y);
            }
        }

        $this->gameField = $gameField;
    }

    /**
     * Return information about field's cells states and it's quantity
     *
     * @return array<string, int>
     */
    #[\Override] public function getFieldInformation(): array
    {
        $cellTypes = [
            'Dead' => 0,
            'Alive' => 0
        ];

        for ($y = 0; $y < $this->ySize; $y++) {
            for ($x = 0; $x < $this->xSize; $x++) {
                if ($this->gameField[$y][$x]->isAlive()) {
                    $cellTypes['Alive']++;
                } else {
                    $cellTypes['Dead']++;
                }
            }
        }

        return $cellTypes;
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
