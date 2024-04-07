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
use Random\RandomException;

/**
 * @method array<AbstractForestCell> getNeighborhoods(int $x, int $y)
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

                if ($this->gameField[$y][$x] instanceof AbstractAnimalCell) {
                    /** @var AbstractAnimalCell $cell */
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

        foreach ($priorities as $priority) {
            foreach ($cellsCoordinates[$priority] as &$cellCoordinates) {
                $y = $cellCoordinates['y'];
                $x = $cellCoordinates['x'];

                $cell = &$this->gameField[$y][$x];

                if ($cell instanceof AbstractLiveCell) {
                    $cellCoordinates['cellToMove'] = $cell->findTheBestCellToMove($this);
                }

                unset($cell);
            }
        }

        foreach ($priorities as $priority) {
            foreach ($cellsCoordinates[$priority] as $cellCoordinates) {
                $y = $cellCoordinates['y'];
                $x = $cellCoordinates['x'];
                $cellToMove = $cellCoordinates['cellToMove'] ?? null;

                if (isset($cellToMove)) {
                    /** @var AbstractLiveCell $cell */
                    $cell = &$this->gameField[$y][$x];

                    $move = $cell->createMove($cellToMove);

                    $nextCell = $move->getNextCell();
                    $previousCell = $move->getPreviousCell();

                    $this->gameField[$nextCell->getY()][$nextCell->getX()] = $nextCell;

                    if (isset($previousCell)) {
                        $this->gameField[$previousCell->getY()][$previousCell->getX()] = $previousCell;
                    }

                    unset($cell);
                }
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

        for ($y = 0; $y < $this->ySize; $y++) {
            $this->gameField[$y] = [];

            for ($x = 0; $x < $this->xSize; $x++) {
                $cellType = random_int(1, 15);

                $forestCellType = match ($cellType) {
                    1 => BearCell::class,
                    2, 3, 4 => RabbitCell::class,
                    5, 6, 7, 8, => WolfCell::class,
                    9, 10, 11, 12, 13, 14 => PlantCell::class,
                    15 => WaterCell::class,
                    default => throw new RandomException('Incorrect generated number')
                };

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
