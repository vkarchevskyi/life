<?php

declare(strict_types=1);

namespace App\Models\Forest\Cells;

use App\Models\Forest\Fields\ForestField;
use App\Models\Forest\Moves\CellCoords;
use App\Models\Forest\Moves\CellMove;
use Random\RandomException;

abstract class AbstractPredatorCell extends AbstractAnimalCell
{
    public const int STRENGTH = -1;

    /**
     * First of all, find cell, where predator will eat prey. If there are no such cells, find cell,
     * where predator will eat other weaker predator. Otherwise, just make random move on cell with plant type.
     *
     * @param ForestField $field
     * @return CellCoords
     * @throws RandomException
     */
    #[\Override] public function findTheBestCellToMove(ForestField $field): CellCoords
    {
        /** @var array<AbstractForestCell> $neighbors */
        $neighbors = array_values(
            array_filter(
                $field->getNeighborhoods($this->getX(), $this->getY()),
                function (AbstractForestCell $neighbor) {
                    return $neighbor instanceof AbstractNatureCell
                        || $neighbor instanceof AbstractPreyCell
                        || $neighbor instanceof AbstractPredatorCell && static::STRENGTH > $neighbor::STRENGTH;
                },
            )
        );

        $neighborsCount = count($neighbors);

        for ($i = 0; $i < $neighborsCount; $i++) {
            if ($neighbors[$i] instanceof AbstractPreyCell) {
                return new CellCoords($neighbors[$i]->getX(), $neighbors[$i]->getY());
            }
        }

        for ($i = 0; $i < $neighborsCount; $i++) {
            if ($neighbors[$i] instanceof AbstractPredatorCell) {
                return new CellCoords($neighbors[$i]->getX(), $neighbors[$i]->getY());
            }
        }

        if ($neighborsCount) {
            $randomNeighbor = $neighbors[random_int(0, $neighborsCount - 1)];
            return new CellCoords($randomNeighbor->getX(), $randomNeighbor->getY());
        }

        return new CellCoords($this->x, $this->y);
    }

    /**
     * If type of $cellToMove is prey, increase livingDays on satiety points.
     * Otherwise, just make move and change type of previous cell to dead plant.
     *
     * @param AbstractForestCell $cellToMove
     * @return CellMove
     */
    #[\Override] public function createMove(AbstractForestCell $cellToMove): CellMove
    {
        $newY = $cellToMove->getY();
        $newX = $cellToMove->getX();

        $daysModifier = $cellToMove instanceof AbstractAnimalCell ? $cellToMove::SATIETY : 0;

        $livingDays = $this->getLivingDays() + $daysModifier - 1;

        if ($livingDays <= 0) {
            return new CellMove(ForestField::createEmptyCell($this->x, $this->y));
        }

        $nextCell = new static($newX, $newY, $livingDays);
        $previousCell = ForestField::createEmptyCell($this->x, $this->y);

        return new CellMove($nextCell, $previousCell);
    }
}
