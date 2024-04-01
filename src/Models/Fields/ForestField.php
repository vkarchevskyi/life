<?php

declare(strict_types=1);

namespace App\Models\Fields;

use App\Models\Cells\ForestCell;
use App\Models\CellsTypes\ForestCellTypes;
use Random\RandomException;

class ForestField extends AbstractField
{
    protected const int BEAR_DAYS_LIVE = 4;

    protected const int WOLF_DAYS_LIVE = 3;

    protected const int RABBIT_DAYS_LIVE = 2;

    protected const int PLANT_DAYS_LIVE = 30;


    public function __construct(int $xSize, int $ySize, bool $connectBorders)
    {
        parent::__construct($xSize, $ySize, $connectBorders);
    }

    #[\Override] public function nextStep(): void
    {
        // TODO: create
    }

    /**
     * @throws RandomException
     */
    #[\Override] public function generateField(): void
    {
        $gameField = [];

        for ($y = 0; $y < $this->ySize; $y++) {
            $gameField[$y] = [];

            for ($x = 0; $x < $this->xSize; $x++) {
                $cellType = random_int(1, 13);

                $forestCellType = match ($cellType) {
                    1 => ForestCellTypes::BEAR_CHAR,
                    2, 3 => ForestCellTypes::RABBIT_CHAR,
                    4 => ForestCellTypes::WOLF_CHAR,
                    5, 6, 7, 8, 9, 10, 11, 12 => ForestCellTypes::PLANT,
                    13 => ForestCellTypes::WATER,
                    default => throw new RandomException('Incorrect generated number')
                };

                $alive = !($forestCellType === ForestCellTypes::PLANT) || random_int(0, 1);

                $gameField[$y][$x] = new ForestCell(
                    $forestCellType,
                    $alive,
                    $this->getLivingDays($forestCellType)
                );
            }
        }

        $this->gameField = $gameField;
    }

    protected function getLivingDays(ForestCellTypes $cellType): ?int
    {
        return match ($cellType) {
            ForestCellTypes::WOLF_CHAR => self::WOLF_DAYS_LIVE,
            ForestCellTypes::BEAR_CHAR => self::BEAR_DAYS_LIVE,
            ForestCellTypes::RABBIT_CHAR => self::RABBIT_DAYS_LIVE,
            ForestCellTypes::PLANT => self::PLANT_DAYS_LIVE,
            ForestCellTypes::WATER => null
        };
    }
}