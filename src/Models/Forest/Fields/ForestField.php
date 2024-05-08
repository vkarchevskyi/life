<?php

declare(strict_types=1);

namespace App\Models\Forest\Fields;

use App\Models\AbstractGame\Fields\AbstractField;
use App\Models\Forest\Cells\AbstractAnimalCell;
use App\Models\Forest\Cells\AbstractForestCell;
use App\Models\Forest\Cells\AbstractLiveCell;
use App\Models\Forest\Cells\AbstractNatureCell;
use App\Models\Forest\Cells\AbstractStaticCell;
use App\Models\Forest\Cells\BearCell;
use App\Models\Forest\Cells\PlantCell;
use App\Models\Forest\Cells\RabbitCell;
use App\Models\Forest\Cells\WaterCell;
use App\Models\Forest\Cells\WolfCell;
use App\Models\Forest\Moves\CellCoords;
use Random\RandomException;

/**
 * @method array<AbstractForestCell> getNeighborhoods(int $x, int $y)
 * @method setCell(int $x, int $y, AbstractForestCell $cell)
 * */
class ForestField extends AbstractField
{
    protected const array CELLS = [
        BearCell::class,
        RabbitCell::class,
        WolfCell::class,
        PlantCell::class,
        WaterCell::class,
    ];

    /** @var array<array<AbstractForestCell>> $gameField */
    protected array $gameField;

    /**
     * Make next step of simulation in current field
     *
     * @return void
     */
    #[\Override] public function nextStep(): void
    {
        /** @var array<int, array> $cellsCoordinates */
        $cellsCoordinates = [];

        for ($y = 0; $y < $this->ySize; $y++) {
            for ($x = 0; $x < $this->xSize; $x++) {
                $cell = &$this->gameField[$y][$x];
                $priority = AbstractAnimalCell::PRIORITY;

                if ($cell instanceof AbstractAnimalCell) {
                    $priority = $cell::PRIORITY;
                }

                if (!isset($cellsCoordinates[$priority])) {
                    $cellsCoordinates[$priority] = [];
                }

                $cellsCoordinates[$priority][] = ['x' => $x, 'y' => $y];

                unset($cell);
            }
        }

        $priorities = array_filter(array_keys($cellsCoordinates), function (int $key): bool {
            return $key >= 0;
        });

        sort($priorities, SORT_NUMERIC);

        $priorities[] = AbstractAnimalCell::PRIORITY;

        /**
         * @var array<array> $bestMoves
         */
        $bestMoves = [];

        foreach ($priorities as $priority) {
            foreach ($cellsCoordinates[$priority] as $cellCoordinates) {
                $y = $cellCoordinates['y'];
                $x = $cellCoordinates['x'];

                $cell = &$this->gameField[$y][$x];

                if ($cell instanceof AbstractLiveCell) {
                    $cellToMove = $cell->findTheBestCellToMove($this);
                    $bestMoves[] = [
                        'cell' => $this->gameField[$y][$x],
                        'cellToMoveCoords' => $cellToMove
                    ];
                } else {
                    unset($cell);
                }
            }
        }

        foreach ($bestMoves as $bestMove) {
            /** @var CellCoords $cellCoords*/
            $cellCoords = $bestMove['cellToMoveCoords'];

            /** @var AbstractLiveCell $cell  */
            $cell = $bestMove['cell'];

            if ($cell !== $this->gameField[$cell->getY()][$cell->getX()]) {
                continue;
            }

            $move = $cell->createMove(
                $this->gameField[$cellCoords->y][$cellCoords->x]
            );

            $nextCell = $move->getNextCell();
            $previousCell = $move->getPreviousCell();

            $this->gameField[$nextCell->getY()][$nextCell->getX()] = $nextCell;

            if (isset($previousCell)) {
                $this->gameField[$previousCell->getY()][$previousCell->getX()] = $previousCell;
            }
        }
    }

    /**
     * Randomly generate game field
     *
     * @return void
     * @throws RandomException
     */
    #[\Override] public function generateField(): void
    {
        $this->gameField = [];

        $counter = 0;
        $maxChance = 0;
        $probabilities = [];

        /** @var AbstractForestCell $cell */
        foreach (static::CELLS as $cell) {
            if ($cell::CHANCE_TO_SPAWN < 0) {
                throw new \LogicException(
                    "Please add CHANCE_TO_SPAWN constant with positive value to every cells you use\n"
                );
            }

            $maxChance += $cell::CHANCE_TO_SPAWN;

            for ($i = 0; $i < $cell::CHANCE_TO_SPAWN; $i++) {
                $probabilities[$counter++] = $cell;
            }
        }

        for ($y = 0; $y < $this->ySize; $y++) {
            $this->gameField[$y] = [];

            for ($x = 0; $x < $this->xSize; $x++) {
                $randomClassIndex = random_int(0, $maxChance - 1);

                $forestCellType = $probabilities[$randomClassIndex] ?? null;

                if (!isset($forestCellType)) {
                    throw new RandomException("Incorrect generated number.\n");
                }

                if (is_a($forestCellType, AbstractStaticCell::class, true)) {
                    $this->gameField[$y][$x] = new $forestCellType($x, $y);
                } elseif (is_a($forestCellType, AbstractNatureCell::class, true)) {
                    $this->gameField[$y][$x] = new $forestCellType($x, $y, (bool)random_int(0, 1));
                } elseif (is_a($forestCellType, AbstractAnimalCell::class, true)) {
                    $this->gameField[$y][$x] = new $forestCellType($x, $y);
                } else {
                    throw new \LogicException("Unexpected cell type in forest field generation.\n");
                }
            }
        }
    }

    /**
     * Return information about field's cells types and it's quantity
     *
     * @return array<string, int>
     */
    #[\Override] public function getFieldInformation(): array
    {
        $cellTypes = [];

        for ($y = 0; $y < $this->ySize; $y++) {
            for ($x = 0; $x < $this->xSize; $x++) {
                $cell = &$this->gameField[$y][$x];

                /** @var array<string> $cellNamespaces */
                $cellNamespaces = explode('\\', get_class($cell)) ?? [];
                $typeName = ucfirst(end($cellNamespaces));

                if ($cell instanceof AbstractLiveCell) {
                    $typeName .= $cell->isAlive() ? "_ALIVE" : "_DEAD";
                }

                if (!isset($cellTypes[$typeName])) {
                    $cellTypes[$typeName] = 0;
                }

                $cellTypes[$typeName]++;

                unset($cell);
            }
        }

        return $cellTypes;
    }

    public function getQuantityOfCells(string $classType, ?bool $isAlive = null): int
    {
        $counter = 0;

        for ($y = 0; $y < $this->ySize; $y++) {
            for ($x = 0; $x < $this->xSize; $x++) {
                $cell = &$this->gameField[$y][$x];

                if (is_a($cell, $classType)) {
                    if (isset($isAlive) && $cell instanceof AbstractLiveCell && $isAlive !== $cell->isAlive()) {
                        unset($cell);
                        break;
                    }

                    $counter++;
                }

                unset($cell);
            }
        }

        return $counter;
    }

    /**
     * Make a dead plant cell in field with given coordinates
     *
     * @param int $x
     * @param int $y
     * @return AbstractForestCell
     */
    public static function createEmptyCell(int $x, int $y): AbstractForestCell
    {
        return new PlantCell($x, $y, false);
    }
}
