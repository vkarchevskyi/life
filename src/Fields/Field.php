<?php

declare(strict_types=1);

namespace App\Fields;

use App\Console\EscapeCodes;
use Random\RandomException;

class Field extends AbstractField
{
    protected const string ALIVE_CELL = "*";

    protected const string DEAD_CELL = " ";

    public function __construct(int $xSize, int $ySize, bool $connectBorders)
    {
        parent::__construct($xSize, $ySize, $connectBorders);
    }

    #[\Override] final public function nextStep(): void
    {
        $newArr = [];

        for ($y = 0; $y < $this->ySize; $y++) {
            for ($x = 0; $x < $this->xSize; $x++) {
                $neighborsCount = $this->calculateNeighbors($x, $y);

                if ($this->gameField[$y][$x] === self::DEAD_CELL && $neighborsCount === 3) {
                    $newArr[$y][$x] = self::ALIVE_CELL;
                } elseif (
                    $this->gameField[$y][$x] === self::ALIVE_CELL
                    && $neighborsCount >= 2
                    && $neighborsCount <= 3
                ) {
                    $newArr[$y][$x] = self::ALIVE_CELL;
                } else {
                    $newArr[$y][$x] = self::DEAD_CELL;
                }
            }
        }

        $this->gameField = $newArr;
    }

    /**
     * @throws RandomException
     */
    #[\Override] final public function generateField(): void
    {
        $generatedGameField = [];

        $allVariants = [self::DEAD_CELL, self::ALIVE_CELL];
        $variantsQuantity = count($allVariants);

        for ($y = 0; $y < $this->ySize; $y++) {
            $generatedGameField[$y] = [];

            for ($x = 0; $x < $this->xSize; $x++) {
                $generatedGameField[$y][$x] = $allVariants[random_int(0, $variantsQuantity - 1)];
            }
        }

        $this->gameField = $generatedGameField;
    }

    #[\Override] final protected function calculateNeighbors(int $x, int $y): int
    {
        $top = $this->getTopCoordinate($y);
        $bottom = $this->getBottomCoordinate($y);

        $left = $this->getLeftCoordinate($x);
        $right = $this->getRightCoordinate($x);

        $neighbors = [
            $this->gameField[$top][$left],
            $this->gameField[$top][$x],
            $this->gameField[$top][$right],
            $this->gameField[$y][$left],
            $this->gameField[$y][$right],
            $this->gameField[$bottom][$left],
            $this->gameField[$bottom][$x],
            $this->gameField[$bottom][$right]
        ];

        return array_reduce($neighbors, function ($carry, $item) {
            $carry += ($item === self::ALIVE_CELL) ? 1 : 0;
            return $carry;
        }, 0);
    }

    final public function printField(): void
    {
        echo EscapeCodes::RED->value;

        for ($y = 0; $y < $this->ySize; $y++) {

            for ($x = 0; $x < $this->xSize; $x++) {
                echo $this->gameField[$y][$x];
            }

            echo "\n";
        }

        echo EscapeCodes::RESET->value;
    }

    #[\Override] final protected function getTopCoordinate(int $y): int
    {
        return $y - 1 >= self::START_Y ? $y - 1 : $this->ySize - 1;
    }

    #[\Override] final protected function getBottomCoordinate(int $y): int
    {
        return $y + 1 < $this->ySize ? $y + 1 : self::START_Y;
    }

    #[\Override] final protected function getLeftCoordinate(int $x): int
    {
        return $x - 1 >= self::START_X ? $x - 1 : $this->xSize - 1;
    }

    #[\Override] final protected function getRightCoordinate(int $x): int
    {
        return $x + 1 < $this->xSize ? $x + 1 : self::START_X;
    }
}
