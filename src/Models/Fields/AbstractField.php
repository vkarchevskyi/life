<?php

declare(strict_types=1);

namespace App\Models\Fields;

use App\Models\Cells\AbstractCell;

abstract class AbstractField
{
    protected const int DEFAULT_X_SIZE = 10;

    protected const int DEFAULT_Y_SIZE = 10;

    protected const int START_X = 0;

    protected const int START_Y = 0;

    protected readonly int $xSize;

    protected readonly int $ySize;

    protected readonly bool $connectBorders;

    /* @var array<array> $gameField*/
    protected array $gameField;


    public function __construct(
        int $xSize = self::DEFAULT_X_SIZE,
        int $ySize = self::DEFAULT_Y_SIZE,
        bool $connectBorders = true
    ) {
        if ($xSize <= 0) {
            throw new \LogicException('xSize variable cannot be equal or less than zero.');
        }
        if ($ySize <= 0) {
            throw new \LogicException('ySize variable cannot be equal or less than zero.');
        }

        $this->xSize = $xSize;
        $this->ySize = $ySize;

        $this->connectBorders = $connectBorders;
    }

    abstract public function nextStep(): void;

    abstract public function calculateStep(int $step): void;

    abstract public function generateField(): void;

    abstract protected function calculateNeighbors(int $x, int $y): int;

    abstract protected function getTopCoordinate(int $y): int;

    abstract protected function getBottomCoordinate(int $y): int;

    abstract protected function getLeftCoordinate(int $x): int;

    abstract protected function getRightCoordinate(int $x): int;

    public function getXSize(): int
    {
        return $this->xSize;
    }

    public function getYSize(): int
    {
        return $this->ySize;
    }

    public function getCell(int $x, int $y): AbstractCell
    {
        if (isset($this->gameField[$y][$x])) {
            return $this->gameField[$y][$x];
        }

        throw new \LogicException('This coordinates don\'t matches with game field');
    }
}
