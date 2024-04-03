<?php

declare(strict_types=1);

namespace App\Models\Fields;

use App\Models\Cells\ForestCell;
use App\Models\CellsTypes\ForestCellTypes;
use Random\RandomException;

/**
 * @method array<ForestCell> getNeighborhoods(int $x, int $y)
 * */
class ForestField extends AbstractField
{
    /**
     * Make next step of simulation in current field
     *
     * @return void
     * @throws RandomException
     */
    #[\Override] public function nextStep(): void
    {
        $cellsCoords = [];

        for ($y = 0; $y < $this->ySize; $y++) {
            for ($x = 0; $x < $this->xSize; $x++) {
                $typeName = $this->gameField[$y][$x]->getType()->name;

                if (!isset($cellsCoords[$typeName])) {
                    $cellsCoords[$typeName] = [];
                }

                $cellsCoords[$typeName][] = ['x' => $x, 'y' => $y];
            }
        }

        $priorities = [
            ForestCellTypes::RABBIT,
            ForestCellTypes::WOLF,
            ForestCellTypes::BEAR,
            ForestCellTypes::PLANT,
            ForestCellTypes::WATER,
        ];

        foreach ($priorities as $priority) {
            foreach ($cellsCoords[$priority->name] ?? [] as $cellCoords) {
                $x = $cellCoords['x'];
                $y = $cellCoords['y'];
                $cell = $this->gameField[$y][$x];

                switch ($priority) {
                    case ForestCellTypes::RABBIT:
                        $cellToMove = $this->findTheBestCellToMoveForRabbit($cell);
                        $this->makeMoveForRabbit($cell, $cellToMove);
                        break;
                    case ForestCellTypes::WOLF:
                        $cellToMove = $this->findTheBestCellToMoveForWolf($cell);
                        $this->makeMoveForWolf($cell, $cellToMove);
                        break;
                    case ForestCellTypes::BEAR:
                        $cellToMove = $this->findTheBestCellToMoveForBear($cell);
                        $this->makeMoveForBear($cell, $cellToMove);
                        break;
                    default:
                        break;
                }
            }
        }

        for ($y = 0; $y < $this->ySize; $y++) {
            for ($x = 0; $x < $this->xSize; $x++) {
                /** @var ForestCell $cell */
                $cell = &$this->gameField[$y][$x];

                if ($cell->isAlive()) {
                    $cell->decreaseLivingDays();
                } else {
                    $cell->decreaseDeathDays();
                }

                unset($cell);
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
        $gameField = [];

        for ($y = 0; $y < $this->ySize; $y++) {
            $gameField[$y] = [];

            for ($x = 0; $x < $this->xSize; $x++) {
                $cellType = random_int(1, 15);

                $forestCellType = match ($cellType) {
                    1 => ForestCellTypes::BEAR,
                    2, 3, 4 => ForestCellTypes::RABBIT,
                    5, 6, 7, 8, => ForestCellTypes::WOLF,
                    9, 10, 11, 12, 13, 14 => ForestCellTypes::PLANT,
                    15 => ForestCellTypes::WATER,
                    default => throw new RandomException('Incorrect generated number')
                };

                $alive = !($forestCellType === ForestCellTypes::PLANT) || random_int(0, 1);

                $gameField[$y][$x] = new ForestCell(
                    $forestCellType,
                    $alive,
                    $x,
                    $y,
                    ForestCell::getDefaultLivingDays($forestCellType),
                    ForestCell::getDefaultDeathDays($forestCellType)
                );
            }
        }

        $this->gameField = $gameField;
    }

    /**
     * Find cells, where rabbit will be in safe, than find the cell with plant to breed.
     * Otherwise, move to any direction. If there are no available moves, just do nothing.
     *
     * @param ForestCell $cell
     * @return ForestCell
     * @throws RandomException
     */
    private function findTheBestCellToMoveForRabbit(ForestCell $cell): ForestCell
    {
        /** @var array<ForestCell> $neighbors */
        $neighbors = array_values(
            array_filter(
                $this->getNeighborhoods($cell->getX(), $cell->getY()),
                fn(ForestCell $neighbor) => $neighbor->getType() === ForestCellTypes::PLANT,
            )
        );

        $neighborsCount = count($neighbors);
        $possibleCellsToMove = $neighbors;

        for ($i = 0; $i < $neighborsCount; $i++) {
            if ($neighbors[$i]->isPredator()) {
                $predatorNeighbors = $this->getNeighborhoods($neighbors[$i]->getX(), $neighbors[$i]->getY());

                $possibleCellsToMove = array_filter(
                    $possibleCellsToMove,
                    function (ForestCell $cell) use ($predatorNeighbors) {
                        foreach ($predatorNeighbors as $predatorNeighbor) {
                            return !(
                                $predatorNeighbor->getX() === $cell->getX()
                                && $predatorNeighbor->getY() === $cell->getY()
                            );
                        }

                        return false;
                    }
                );
            }
        }

        for ($i = 0; $i < count($possibleCellsToMove); $i++) {
            if ($possibleCellsToMove[$i]->getType() === ForestCellTypes::PLANT && $possibleCellsToMove[$i]->isAlive()) {
                return $possibleCellsToMove[$i];
            }
        }

        if ($neighborsCount) {
            return $neighbors[random_int(0, $neighborsCount - 1)];
        }

        return $cell;
    }

    /**
     * Make move for rabbit. If rabbit move to the cell with alive plant, create a child
     * in previous rabbit position. Otherwise, change type of previous cell to dead plant
     *
     * @param ForestCell $rabbitCell
     * @param ForestCell $cellToMove
     * @return void
     */
    private function makeMoveForRabbit(ForestCell $rabbitCell, ForestCell $cellToMove): void
    {
        $newY = $cellToMove->getY();
        $newX = $cellToMove->getX();

        $this->gameField[$newY][$newX] = new ForestCell(
            $rabbitCell->getType(),
            $rabbitCell->isAlive(),
            $newX,
            $newY,
            $rabbitCell->getLivingDays(),
        );

        if ($cellToMove->getType() === ForestCellTypes::PLANT && $cellToMove->isAlive()) {
            $oldX = $rabbitCell->getX();
            $oldY = $rabbitCell->getY();

            $this->gameField[$oldY][$oldX] = new ForestCell(
                ForestCellTypes::RABBIT,
                true,
                $oldX,
                $oldY,
                ForestCell::getDefaultLivingDays(ForestCellTypes::RABBIT),
            );
        } else {
            $this->makeDeadPlant($rabbitCell->getX(), $rabbitCell->getY());
        }
    }

    /**
     * First of all, find cell, where wolf will eat rabbit.
     * If there are not such moves, just make random move on cell with plant type.
     *
     * @param ForestCell $cell
     * @return ForestCell
     * @throws RandomException
     */
    private function findTheBestCellToMoveForWolf(ForestCell $cell): ForestCell
    {
        /** @var array<ForestCell> $neighbors */
        $neighbors = array_values(
            array_filter(
                $this->getNeighborhoods($cell->getX(), $cell->getY()),
                function (ForestCell $neighbor) {
                    return $neighbor->getType() !== ForestCellTypes::WATER
                        && $neighbor->getType() !== ForestCellTypes::BEAR;
                },
            )
        );

        $neighborsCount = count($neighbors);

        for ($i = 0; $i < $neighborsCount; $i++) {
            if ($neighbors[$i]->isPrey()) {
                return $neighbors[$i];
            }
        }

        if ($neighborsCount) {
            return $neighbors[random_int(0, $neighborsCount - 1)];
        }

        return $cell;
    }

    /**
     * If type of $cellToMove is rabbit, increase livingDays on 2 and move.
     * Otherwise, just make move and change type of previous cell to dead plant.
     *
     * @param ForestCell $wolfCell
     * @param ForestCell $cellToMove
     * @return void
     */
    private function makeMoveForWolf(ForestCell $wolfCell, ForestCell $cellToMove): void
    {
        $newY = $cellToMove->getY();
        $newX = $cellToMove->getX();

        $this->gameField[$newY][$newX] = new ForestCell(
            $wolfCell->getType(),
            $wolfCell->isAlive(),
            $newX,
            $newY,
            $wolfCell->getLivingDays() + ($cellToMove->isPrey() ? 2 : 0),
        );

        $this->makeDeadPlant($wolfCell->getX(), $wolfCell->getY());
    }

    /**
     * First of all, find cell, where wolf will eat rabbit. If there are no such cells, find cell,
     * where bear will eat wolf. Otherwise, just make random move on cell with plant type.
     *
     * @param ForestCell $cell
     * @return ForestCell
     * @throws RandomException
     */
    private function findTheBestCellToMoveForBear(ForestCell $cell): ForestCell
    {
        /** @var array<ForestCell> $neighbors */
        $neighbors = array_values(
            array_filter(
                $this->getNeighborhoods($cell->getX(), $cell->getY()),
                fn(ForestCell $neighbor) => $neighbor->getType() !== ForestCellTypes::WATER,
            )
        );

        $neighborsCount = count($neighbors);

        for ($i = 0; $i < $neighborsCount; $i++) {
            if ($neighbors[$i]->isPrey() || $neighbors[$i]->getType() === ForestCellTypes::WOLF) {
                return $neighbors[$i];
            }
        }

        if ($neighborsCount) {
            return $neighbors[random_int(0, $neighborsCount - 1)];
        }

        return $cell;
    }

    /**
     * If type of $cellToMove is rabbit, increase livingDays on 2 and move.
     * If type of $cellToMove is wolf, increase livingDays on 1 and move.
     * Otherwise, just make move and change type of previous cell to dead plant.
     *
     * @param ForestCell $bearCell
     * @param ForestCell $cellToMove
     * @return void
     */
    private function makeMoveForBear(ForestCell $bearCell, ForestCell $cellToMove): void
    {
        $newY = $cellToMove->getY();
        $newX = $cellToMove->getX();

        $addLivingDays = 0;

        if ($cellToMove->isPrey()) {
            $addLivingDays = 2;
        } elseif ($cellToMove->getType() === ForestCellTypes::WOLF) {
            $addLivingDays = 1;
        }

        $this->gameField[$newY][$newX] = new ForestCell(
            $bearCell->getType(),
            $bearCell->isAlive(),
            $newX,
            $newY,
            $bearCell->getLivingDays() + $addLivingDays,
        );

        $this->makeDeadPlant($bearCell->getX(), $bearCell->getY());
    }

    private function makeDeadPlant(int $x, int $y): void
    {
        $this->gameField[$y][$x] = new ForestCell(
            ForestCellTypes::PLANT,
            false,
            $x,
            $y,
            ForestCell::getDefaultLivingDays(ForestCellTypes::PLANT),
            ForestCell::getDefaultDeathDays(ForestCellTypes::PLANT)
        );
    }

    #[\Override] public function getFieldInformation(): array
    {
        $cellTypes = [];

        for ($y = 0; $y < $this->ySize; $y++) {
            for ($x = 0; $x < $this->xSize; $x++) {
                /** @var ForestCell $cell */
                $cell = &$this->gameField[$y][$x];
                $typeName = ucfirst(strtolower($cell->getType()->name)) . ($cell->isAlive() ? "_ALIVE" : "_DEAD");

                if (!isset($cellTypes[$typeName])) {
                    $cellTypes[$typeName] = 0;
                }

                $cellTypes[$typeName]++;

                unset($cell);
            }
        }

        return $cellTypes;
    }
}
