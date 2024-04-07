<?php

declare(strict_types=1);

namespace App\Models\AbstractGame\Fields;

use App\Models\AbstractGame\Cells\AbstractCell;

abstract class AbstractField
{
    protected const int DEFAULT_X_SIZE = 10;

    protected const int DEFAULT_Y_SIZE = 10;

    protected const int START_X = 0;

    protected const int START_Y = 0;

    protected const array CELLS = [];

    protected readonly int $xSize;

    protected readonly int $ySize;

    protected readonly bool $connectBorders;

    /** @var array<array<AbstractCell>> $gameField */
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

    abstract public function generateField(): void;

    /**
     * @return array<string, int>
     */
    abstract public function getFieldInformation(): array;

    /**
     * @return int
     */
    public function getXSize(): int
    {
        return $this->xSize;
    }

    /**
     * @return int
     */
    public function getYSize(): int
    {
        return $this->ySize;
    }

    /**
     * @param int $x
     * @param int $y
     * @return AbstractCell
     * @throws \LogicException
     */
    public function getCell(int $x, int $y): AbstractCell
    {
        if (isset($this->gameField[$y][$x])) {
            return $this->gameField[$y][$x];
        }

        throw new \LogicException('This coordinates don\'t matches with game field');
    }

    /**
     * @param int $step
     * @return void
     * @throws \LogicException
     */
    public function calculateStep(int $step): void
    {
        if ($step <= 0) {
            throw new \LogicException('Step can be only positive number');
        }

        for ($i = 0; $i < $step; $i++) {
            $this->nextStep();
        }
    }

    /**
     * @param int $x
     * @param int $y
     * @return array<AbstractCell>
     */
    public function getNeighborhoods(int $x, int $y): array
    {
        return $this->connectBorders
            ? $this->getNeighborsOnConnectedBoard($x, $y)
            : $this->getNeighborsOnNonConnectedBoard($x, $y);
    }

    /**
     * @param int $y
     * @return int
     */
    private function getTopCoordinate(int $y): int
    {
        return $y - 1 >= self::START_Y ? $y - 1 : $this->ySize - 1;
    }

    /**
     * @param int $y
     * @return int
     */
    private function getBottomCoordinate(int $y): int
    {
        return $y + 1 < $this->ySize ? $y + 1 : self::START_Y;
    }

    /**
     * @param int $x
     * @return int
     */
    private function getLeftCoordinate(int $x): int
    {
        return $x - 1 >= self::START_X ? $x - 1 : $this->xSize - 1;
    }

    /**
     * @param int $x
     * @return int
     */
    private function getRightCoordinate(int $x): int
    {
        return $x + 1 < $this->xSize ? $x + 1 : self::START_X;
    }

    /**
     * @param int $x
     * @param int $y
     * @return array
     */
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

    /**
     * @param int $x
     * @param int $y
     * @return array
     */
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
