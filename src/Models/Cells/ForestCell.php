<?php

declare(strict_types=1);

namespace App\Models\Cells;

use App\Models\CellsTypes\ForestCellTypes;

class ForestCell extends AbstractCell
{
    public const int BEAR_DAYS_LIVE = 10;

    public const int WOLF_DAYS_LIVE = 8;

    public const int RABBIT_DAYS_LIVE = 5;

    public const int PLANT_DAYS_LIVE = 6;

    public const int PLANT_DAYS_DIE = 3;

    protected ForestCellTypes $type;

    public function __construct(
        ForestCellTypes $type,
        bool $alive,
        int $x,
        int $y,
        ?int $livingDays = 0,
        ?int $deathDays = null
    ) {
        parent::__construct($alive, $x, $y, $livingDays, $deathDays);

        $this->type = $type;
    }

    #[\Override] public function isAlive(): bool
    {
        return $this->alive;
    }

    #[\Override] public function becomeAlive(): void
    {
        $this->alive = true;

        $this->livingDays = static::getDefaultLivingDays($this->type);
    }

    #[\Override] public function becomeDead(): void
    {
        $this->alive = false;

        $this->livingDays = static::getDefaultDeathDays($this->type);
    }

    #[\Override] public function getDeadCell(): string
    {
        return "  ";
    }

    #[\Override] public function getAliveCell(): string
    {
        return $this->type->value;
    }

    public static function getDefaultLivingDays(ForestCellTypes $type): ?int
    {
        return match ($type) {
            ForestCellTypes::WOLF => static::WOLF_DAYS_LIVE,
            ForestCellTypes::BEAR => static::BEAR_DAYS_LIVE,
            ForestCellTypes::RABBIT => static::RABBIT_DAYS_LIVE,
            ForestCellTypes::PLANT => static::PLANT_DAYS_LIVE,
            ForestCellTypes::WATER => null
        };
    }

    public static function getDefaultDeathDays(ForestCellTypes $type): ?int
    {
        return match ($type) {
            ForestCellTypes::PLANT => static::PLANT_DAYS_DIE,
            default => null
        };
    }

    public function isPredator(): bool
    {
        return $this->type === ForestCellTypes::BEAR || $this->type === ForestCellTypes::WOLF;
    }

    public function isPrey(): bool
    {
        return $this->type === ForestCellTypes::RABBIT;
    }

    public function getType(): ForestCellTypes
    {
        return $this->type;
    }

    public function decreaseLivingDays(): void
    {
        if (!isset($this->livingDays)) {
            return;
        }

        $this->livingDays--;

        if ($this->livingDays <= 0) {
            $this->alive = false;
            $this->type = ForestCellTypes::PLANT;
            $this->deathDays = static::getDefaultDeathDays($this->type);
        }
    }

    public function decreaseDeathDays(): void
    {
        if (!isset($this->deathDays)) {
            return;
        }

        $this->deathDays--;

        if ($this->deathDays <= 0) {
            $this->alive = true;
            $this->livingDays = static::getDefaultDeathDays($this->type);
        }
    }
}
