<?php

declare(strict_types=1);

namespace App\Fields;

use App\Cells\AbstractCell;
use App\Cells\ConwaysCell;
use App\Console\EscapeCodes;
use Random\RandomException;

class ConwaysField extends AbstractField
{
    public function __construct(int $xSize, int $ySize, bool $connectBorders)
    {
        parent::__construct($xSize, $ySize, $connectBorders);
    }

    public function printField(): void
    {
        echo EscapeCodes::RED->value;

        for ($y = 0; $y < $this->ySize; $y++) {
            for ($x = 0; $x < $this->xSize; $x++) {
                echo $this->gameField[$y][$x];
            }

            echo PHP_EOL;
        }

        echo EscapeCodes::RESET->value;
    }

    #[\Override] public function nextStep(): void
    {
        $newArr = [];

        for ($y = 0; $y < $this->ySize; $y++) {
            for ($x = 0; $x < $this->xSize; $x++) {
                $neighborsCount = $this->calculateNeighbors($x, $y);

                /* @var AbstractCell $cell */
                $cell = $this->gameField[$y][$x];

                if (!$cell->isAlive() && $neighborsCount === 3) {
                    $newArr[$y][$x] = new ConwaysCell(true);
                } elseif ($cell->isAlive() && $neighborsCount >= 2 && $neighborsCount <= 3) {
                    $newArr[$y][$x] = new ConwaysCell(true);
                } else {
                    $newArr[$y][$x] = new ConwaysCell(false);
                }
            }
        }

        $this->gameField = $newArr;
    }

    /**
     * @throws RandomException
     */
    #[\Override] public function generateField(): void
    {
        $generatedGameField = [];

        for ($y = 0; $y < $this->ySize; $y++) {
            $generatedGameField[$y] = [];

            for ($x = 0; $x < $this->xSize; $x++) {
                $generatedGameField[$y][$x] = new ConwaysCell((bool)random_int(0, 1));
            }
        }

        $this->gameField = $generatedGameField;
    }

    /**
     * @throws \LogicException
     */
    #[\Override] public function calculateStep(int $step): void
    {
        if ($step <= 0) {
            throw new \LogicException('Step can be only positive number');
        }

        for ($i = 0; $i < $step; $i++) {
            $this->nextStep();
        }
    }

    #[\Override] protected function calculateNeighbors(int $x, int $y): int
    {
        /* @var array<AbstractCell> $neighbors */
        $neighbors = $this->connectBorders
            ? $this->getNeighborsOnConnectedBoard($x, $y)
            : $this->getNeighborsOnNonConnectedBoard($x, $y);

        /* @var AbstractCell $cell */
        return array_reduce($neighbors, function ($counter, $cell) {
            $counter += (int)$cell->isAlive();
            return $counter;
        }, 0);
    }

    #[\Override] protected function getTopCoordinate(int $y): int
    {
        return $y - 1 >= self::START_Y ? $y - 1 : $this->ySize - 1;
    }

    #[\Override] protected function getBottomCoordinate(int $y): int
    {
        return $y + 1 < $this->ySize ? $y + 1 : self::START_Y;
    }

    #[\Override] protected function getLeftCoordinate(int $x): int
    {
        return $x - 1 >= self::START_X ? $x - 1 : $this->xSize - 1;
    }

    #[\Override] protected function getRightCoordinate(int $x): int
    {
        return $x + 1 < $this->xSize ? $x + 1 : self::START_X;
    }

    private function getNeighborsOnConnectedBoard(int $x, int $y): array
    {
        $top = $this->getTopCoordinate($y);
        $bottom = $this->getBottomCoordinate($y);

        $left = $this->getLeftCoordinate($x);
        $right = $this->getRightCoordinate($x);

        return [
            $this->gameField[$top][$left],
            $this->gameField[$top][$x],
            $this->gameField[$top][$right],
            $this->gameField[$y][$left],
            $this->gameField[$y][$right],
            $this->gameField[$bottom][$left],
            $this->gameField[$bottom][$x],
            $this->gameField[$bottom][$right]
        ];
    }

    private function getNeighborsOnNonConnectedBoard(int $x, int $y): array
    {
        $neighbors = [];

        $top = $this->getTopCoordinate($y);
        $bottom = $this->getBottomCoordinate($y);
        $left = $this->getLeftCoordinate($x);
        $right = $this->getRightCoordinate($x);

        $useTop = $y - 1 >= self::START_Y;
        $useBottom = $y + 1 < $this->ySize;
        $useLeft = $x - 1 >= self::START_X;
        $useRight = $x + 1 < $this->xSize;

        if ($useTop) {
            $neighbors[] = $this->gameField[$top][$x];

            if ($useLeft) {
                $neighbors[] = $this->gameField[$top][$left];
            }

            if ($useRight) {
                $neighbors[] = $this->gameField[$top][$right];
            }
        }

        if ($useBottom) {
            $neighbors[] = $this->gameField[$bottom][$x];

            if ($useLeft) {
                $neighbors[] = $this->gameField[$bottom][$left];
            }

            if ($useRight) {
                $neighbors[] = $this->gameField[$bottom][$right];
            }
        }

        if ($useLeft) {
            $neighbors[] = $this->gameField[$y][$left];
        }

        if ($useRight) {
            $neighbors[] = $this->gameField[$y][$right];
        }

        return $neighbors;
    }
}
