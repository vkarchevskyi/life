<?php

declare(strict_types=1);

namespace App\Models\Forest\Cells;

use App\Models\Forest\Fields\ForestField;
use App\Models\Forest\Moves\CellCoords;
use App\Models\Forest\Moves\CellMove;
use Random\RandomException;

abstract class AbstractPreyCell extends AbstractAnimalCell
{
    /**
     * Find cells, where prey will be in safe, than find the cell with plant to breed.
     * Otherwise, move to any direction. If there are no available moves, just do nothing.
     *
     * @param ForestField $field
     * @return CellCoords
     * @throws RandomException
     */
    #[\Override] public function findTheBestCellToMove(ForestField $field): CellCoords
    {
        /** @var array<AbstractForestCell> $neighbors */
        $neighbors = $field->getNeighborhoods($this->getX(), $this->getY());
        $neighborsCount = count($neighbors);

        /** @var array<AbstractNatureCell> $possibleCellsToMove */
        $possibleCellsToMove = array_values(
            array_filter(
                $neighbors,
                function (AbstractForestCell $neighbor) {
                    return $neighbor instanceof AbstractNatureCell;
                },
            )
        );

        for ($i = 0; $i < $neighborsCount; $i++) {
            if ($neighbors[$i] instanceof AbstractPredatorCell) {
                $predatorNeighbors = $field->getNeighborhoods($neighbors[$i]->getX(), $neighbors[$i]->getY());
                $possibleCellsToMove = array_values(
                    array_filter(
                        $possibleCellsToMove,
                        function (AbstractNatureCell $cell) use ($predatorNeighbors) {
                            foreach ($predatorNeighbors as $predatorNeighbor) {
                                return !(
                                    $predatorNeighbor->getX() === $cell->getX()
                                    && $predatorNeighbor->getY() === $cell->getY()
                                );
                            }

                            return false;
                        }
                    )
                );
            }
        }

        for ($i = 0; $i < count($possibleCellsToMove); $i++) {
            if ($possibleCellsToMove[$i]->isAlive() && $possibleCellsToMove[$i]::EATABLE) {
                return new CellCoords($possibleCellsToMove[$i]->getX(), $possibleCellsToMove[$i]->getY());
            }
        }

        for ($i = 0; $i < $neighborsCount; $i++) {
            if ($neighbors[$i] instanceof AbstractNatureCell && $neighbors[$i]->isAlive() && $neighbors[$i]::EATABLE) {
                return new CellCoords($neighbors[$i]->getX(), $neighbors[$i]->getY());
            }
        }

        $neighbors = array_values(
            array_filter(
                $neighbors,
                function (AbstractForestCell $neighbor) {
                    return $neighbor instanceof AbstractNatureCell;
                },
            )
        );

        $neighborsCount = count($neighbors);

        if ($neighborsCount) {
            $randomNeighbor = $neighbors[random_int(0, $neighborsCount - 1)];
            return new CellCoords($randomNeighbor->getX(), $randomNeighbor->getY());
        }

        return new CellCoords($this->x, $this->y);
    }

    /**
     * Make move for prey. If prey move to the cell with alive plant, create a child
     * in previous prey position. Otherwise, change type of previous cell to dead plant
     *
     * @param AbstractForestCell $cellToMove
     * @return CellMove
     */
    #[\Override] public function createMove(AbstractForestCell $cellToMove): CellMove
    {
        $newY = $cellToMove->getY();
        $newX = $cellToMove->getX();

        $livingDays = $this->getLivingDays() - 1;

        if ($livingDays <= 0) {
            return new CellMove(ForestField::createEmptyCell($this->x, $this->y));
        }

        $nextCell = new static($newX, $newY, $livingDays);

        if (
            $cellToMove instanceof AbstractNatureCell
            && $cellToMove::EATABLE
            && $cellToMove->isAlive()
        ) {
            $previousCell = new static($this->x, $this->y);
        } elseif ($this->x !== $cellToMove->getX() || $this->y !== $cellToMove->getY()) {
            $previousCell = ForestField::createEmptyCell($this->x, $this->y);
        } else {
            $previousCell = null;
        }

        return new CellMove($nextCell, $previousCell);
    }
}
