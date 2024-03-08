<?php

declare(strict_types=1);

namespace App\Fields;

abstract class AbstractField
{
    protected const int START_X = 0;

    protected const int START_Y = 0;

    protected readonly int $xSize;

    protected readonly int $ySize;

    protected readonly bool $connectBorders;

    /* @var array<array> $gameField*/
    protected array $gameField;


    public function __construct(int $xSize, int $ySize, bool $connectBorders)
    {
        if ($xSize >= 0) {
            $this->xSize = $xSize;
        } else {
            exit(1);
        }

        if ($ySize >= 0) {
            $this->ySize = $ySize;
        } else {
            exit(1);
        }

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
}
