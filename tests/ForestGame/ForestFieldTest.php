<?php

declare(strict_types=1);

namespace Test\ForestGame;

use App\Models\Forest\Cells\BearCell;
use App\Models\Forest\Cells\PlantCell;
use App\Models\Forest\Cells\RabbitCell;
use App\Models\Forest\Cells\WaterCell;
use App\Models\Forest\Cells\WolfCell;
use App\Models\Forest\Fields\ForestField;
use PHPUnit\Framework\TestCase;

class ForestFieldTest extends TestCase
{
    protected ForestField $field;

    private function initFieldWithAlivePlants(int $xSize, int $ySize): void
    {
        $this->field = new ForestField($xSize, $ySize, false);

        for ($i = 0; $i < $ySize; $i++) {
            for ($j = 0; $j < $xSize; $j++) {
                $this->field->setCell($j, $i, new PlantCell($j, $i, true));
            }
        }
    }

    public function testPlantGrowing(): void
    {
        $xSize = 3;
        $ySize = 3;
        $cellsQuantity = $xSize * $ySize;

        $this->field = new ForestField($xSize, $ySize, false);

        for ($i = 0; $i < $ySize; $i++) {
            for ($j = 0; $j < $xSize; $j++) {
                $this->field->setCell($j, $i, new PlantCell($j, $i, false, 10, 2));
            }
        }

        $this->assertSame($this->field->getQuantityOfCells(PlantCell::class, false), $cellsQuantity);
        $this->field->nextStep();

        $this->assertSame($this->field->getQuantityOfCells(PlantCell::class, false), $cellsQuantity);
        $this->field->nextStep();

        $this->assertSame($this->field->getQuantityOfCells(PlantCell::class, true), $cellsQuantity);
    }

    public function testPlantDying(): void
    {
        $xSize = 3;
        $ySize = 3;
        $cellsQuantity = $xSize * $ySize;

        $this->field = new ForestField($xSize, $ySize, false);

        for ($i = 0; $i < $ySize; $i++) {
            for ($j = 0; $j < $xSize; $j++) {
                $this->field->setCell($j, $i, new PlantCell($j, $i, true, 2, 10));
            }
        }

        $this->assertSame($this->field->getQuantityOfCells(PlantCell::class, true), $cellsQuantity);
        $this->field->nextStep();

        $this->assertSame($this->field->getQuantityOfCells(PlantCell::class, true), $cellsQuantity);
        $this->field->nextStep();

        $this->assertSame($this->field->getQuantityOfCells(PlantCell::class, false), $cellsQuantity);
    }

    public function testRabbitReproduction(): void
    {
        $this->initFieldWithAlivePlants(2, 2);

        $this->field->setCell(1, 1, new RabbitCell(1, 1, 5));
        $this->assertSame($this->field->getQuantityOfCells(RabbitCell::class, true), 1);

        $this->field->nextStep();
        $this->assertSame($this->field->getQuantityOfCells(RabbitCell::class, true), 2);
    }

    public function testBearHunting(): void
    {
        $this->initFieldWithAlivePlants(2, 1);

        $this->field->setCell(1, 0, new RabbitCell(1, 0, 5));
        $this->field->setCell(0, 0, new BearCell(0, 0, 5));
        $this->assertSame($this->field->getQuantityOfCells(RabbitCell::class, true), 1);
        $this->assertSame($this->field->getQuantityOfCells(BearCell::class, true), 1);

        $this->field->nextStep();
        $this->assertSame($this->field->getQuantityOfCells(RabbitCell::class, true), 0);
        $this->assertSame($this->field->getQuantityOfCells(BearCell::class, true), 1);
    }

    public function testBearSurviving(): void
    {
        $this->initFieldWithAlivePlants(2, 1);

        $this->field->setCell(1, 0, new WolfCell(1, 0, 5));
        $this->field->setCell(0, 0, new BearCell(0, 0, 5));
        $this->assertSame($this->field->getQuantityOfCells(WolfCell::class, true), 1);
        $this->assertSame($this->field->getQuantityOfCells(BearCell::class, true), 1);

        $this->field->nextStep();
        $this->assertSame($this->field->getQuantityOfCells(WolfCell::class, true), 0);
        $this->assertSame($this->field->getQuantityOfCells(BearCell::class, true), 1);
    }

    public function testWolfHunting(): void
    {
        $this->initFieldWithAlivePlants(2, 1);

        $this->field->setCell(1, 0, new RabbitCell(1, 0, 5));
        $this->field->setCell(0, 0, new WolfCell(0, 0, 5));
        $this->assertSame($this->field->getQuantityOfCells(RabbitCell::class, true), 1);
        $this->assertSame($this->field->getQuantityOfCells(WolfCell::class, true), 1);

        $this->field->nextStep();
        $this->assertSame($this->field->getQuantityOfCells(RabbitCell::class, true), 0);
        $this->assertSame($this->field->getQuantityOfCells(WolfCell::class, true), 1);
    }

    public function testStaticWaterBlock(): void
    {
        $xSize = 3;
        $ySize = 3;

        $this->field = new ForestField($xSize, $ySize, false);

        for ($i = 0; $i < $ySize; $i++) {
            for ($j = 0; $j < $xSize; $j++) {
                $this->field->setCell($j, $i, new WaterCell($j, $i));
            }
        }

        $this->field->setCell(1, 1, new RabbitCell(1, 1, 5));

        $this->field->nextStep();

        $this->assertTrue($this->field->getCell(1, 1) instanceof RabbitCell);
    }

    public function testBearStarving(): void
    {
        $this->initFieldWithAlivePlants(2, 2);

        $this->field->setCell(1, 0, new BearCell(1, 0, 2));

        $this->field->nextStep();

        $this->assertSame($this->field->getQuantityOfCells(BearCell::class, true), 1);
        $this->field->nextStep();
        $this->assertSame($this->field->getQuantityOfCells(BearCell::class, true), 0);
    }

    public function testWolfStarving(): void
    {
        $this->initFieldWithAlivePlants(2, 2);

        $this->field->setCell(1, 0, new WolfCell(1, 0, 2));

        $this->field->nextStep();
        $this->assertSame($this->field->getQuantityOfCells(WolfCell::class, true), 1);
        $this->field->nextStep();
        $this->assertSame($this->field->getQuantityOfCells(WolfCell::class, true), 0);
    }

    public function testRabbitStarving(): void
    {
        $xSize = 2;
        $ySize = 2;

        $this->field = new ForestField($xSize, $ySize, false);

        for ($i = 0; $i < $ySize; $i++) {
            for ($j = 0; $j < $xSize; $j++) {
                $this->field->setCell($j, $i, new PlantCell($j, $i, false, 10, 2));
            }
        }

        $this->field->setCell(1, 0, new RabbitCell(1, 0, 2));

        $this->field->nextStep();
        $this->assertSame($this->field->getQuantityOfCells(RabbitCell::class, true), 1);
        $this->field->nextStep();
        $this->assertSame($this->field->getQuantityOfCells(RabbitCell::class, true), 0);
    }
}
