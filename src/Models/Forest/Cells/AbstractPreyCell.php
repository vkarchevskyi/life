<?php

declare(strict_types=1);

namespace App\Models\Forest\Cells;

use App\Models\Forest\Fields\ForestField;
use App\Models\Forest\Moves\CellMove;
use Random\RandomException;

abstract class AbstractPreyCell extends AbstractAnimalCell
{
    /**
     * Find cells, where prey will be in safe, than find the cell with plant to breed.
     * Otherwise, move to any direction. If there are no available moves, just do nothing.
     *
     * @param ForestField $field
     * @return AbstractForestCell
     * @throws RandomException
     */
    #[\Override] public function findTheBestCellToMove(ForestField $field): AbstractForestCell
    {
        /** @var array<AbstractNatureCell> $neighbors */
        $neighbors = array_values(
            array_filter(
                $field->getNeighborhoods($this->getX(), $this->getY()),
                function (AbstractForestCell $neighbor) {
                    return $neighbor instanceof AbstractNatureCell && $neighbor::EATABLE;
                },
            )
        );

        $neighborsCount = count($neighbors);
        $possibleCellsToMove = $neighbors;

        for ($i = 0; $i < $neighborsCount; $i++) {
            if ($neighbors[$i] instanceof AbstractPredatorCell) {
                $predatorNeighbors = $field->getNeighborhoods($neighbors[$i]->getX(), $neighbors[$i]->getY());

                $possibleCellsToMove = array_filter(
                    $possibleCellsToMove,
                    function (AbstractForestCell $cell) use ($predatorNeighbors) {
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
            if ($possibleCellsToMove[$i]->isAlive()) {
                return $possibleCellsToMove[$i];
            }
        }

        if ($neighborsCount) {
            return $neighbors[random_int(0, $neighborsCount - 1)];
        }

        return $this;
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
            $previousCell = new static(
                $this->x,
                $this->y
            );
        } else {
            $previousCell = ForestField::createEmptyCell($this->x, $this->y);
        }

        return new CellMove($nextCell, $previousCell);
    }
}
