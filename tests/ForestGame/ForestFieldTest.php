<?php

declare(strict_types=1);

namespace Test\ForestGame;

use App\Models\Forest\Cells\BearCell;
use App\Models\Forest\Cells\PlantCell;
use App\Models\Forest\Cells\RabbitCell;
use App\Models\Forest\Cells\WaterCell;
use App\Models\Forest\Cells\WolfCell;
use App\Models\Forest\Fields\ForestField;
use Test\AppTest;

class ForestFieldTest extends AppTest
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

    /**
     * 1. Plants are growing
     * 2. Plants are dying
     * 3. 2x2 Rabbit eat plant, another rabbit created,
     * 4. 2x2 bear eat rabbit
     * 5. 2x2 bear eat wolf
     * 6. 2x2 wolf eat rabbit
     * 7. 3x3 rabbit around water
     * 8. bear die without food
     * 9. wolf die without food
     * 10. rabbit die without food
     */

    protected function testPlantGrowing(): void
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

        echo "Test on plants growing successfully completed\n";
    }

    protected function testPlantDying(): void
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

        echo "Test on plants dying successfully completed\n";
    }

    protected function testRabbitReproduction(): void
    {
        $this->initFieldWithAlivePlants(2, 2);

        $this->field->setCell(1, 1, new RabbitCell(1, 1, 5));
        $this->assertSame($this->field->getQuantityOfCells(RabbitCell::class, true), 1);

        $this->field->nextStep();
        $this->assertSame($this->field->getQuantityOfCells(RabbitCell::class, true), 2);

        echo "Test on rabbits reproduction successfully completed\n";
    }

    protected function testBearHunting(): void
    {
        $this->initFieldWithAlivePlants(2, 1);

        $this->field->setCell(1, 0, new RabbitCell(1, 0, 5));
        $this->field->setCell(0, 0, new BearCell(0, 0, 5));
        $this->assertSame($this->field->getQuantityOfCells(RabbitCell::class, true), 1);
        $this->assertSame($this->field->getQuantityOfCells(BearCell::class, true), 1);

        $this->field->nextStep();
        $this->assertSame($this->field->getQuantityOfCells(RabbitCell::class, true), 0);
        $this->assertSame($this->field->getQuantityOfCells(BearCell::class, true), 1);

        echo "Test on bears hunting successfully completed\n";
    }

    protected function testBearSurviving(): void
    {
        $this->initFieldWithAlivePlants(2, 1);

        $this->field->setCell(1, 0, new WolfCell(1, 0, 5));
        $this->field->setCell(0, 0, new BearCell(0, 0, 5));
        $this->assertSame($this->field->getQuantityOfCells(WolfCell::class, true), 1);
        $this->assertSame($this->field->getQuantityOfCells(BearCell::class, true), 1);

        $this->field->nextStep();
        $this->assertSame($this->field->getQuantityOfCells(WolfCell::class, true), 0);
        $this->assertSame($this->field->getQuantityOfCells(BearCell::class, true), 1);

        echo "Test on bears surviving successfully completed\n";
    }

    protected function testWolfHunting(): void
    {
        $this->initFieldWithAlivePlants(2, 1);

        $this->field->setCell(1, 0, new RabbitCell(1, 0, 5));
        $this->field->setCell(0, 0, new WolfCell(0, 0, 5));
        $this->assertSame($this->field->getQuantityOfCells(RabbitCell::class, true), 1);
        $this->assertSame($this->field->getQuantityOfCells(WolfCell::class, true), 1);

        $this->field->nextStep();
        $this->assertSame($this->field->getQuantityOfCells(RabbitCell::class, true), 0);
        $this->assertSame($this->field->getQuantityOfCells(WolfCell::class, true), 1);

        echo "Test on wolfs hunting successfully completed\n";
    }

    protected function testStaticWaterBlock(): void
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

        echo "Test of static water block successfully completed\n";
    }

    protected function testBearStarving(): void
    {
        $this->initFieldWithAlivePlants(2, 2);

        $this->field->setCell(1, 0, new BearCell(1, 0, 2));

        $this->field->nextStep();

        $this->assertSame($this->field->getQuantityOfCells(BearCell::class, true), 1);
        $this->field->nextStep();
        $this->assertSame($this->field->getQuantityOfCells(BearCell::class, true), 0);

        echo "Test on bears starving successfully completed\n";
    }

    protected function testWolfStarving(): void
    {
        $this->initFieldWithAlivePlants(2, 2);

        $this->field->setCell(1, 0, new WolfCell(1, 0, 2));

        $this->field->nextStep();
        $this->assertSame($this->field->getQuantityOfCells(WolfCell::class, true), 1);
        $this->field->nextStep();
        $this->assertSame($this->field->getQuantityOfCells(WolfCell::class, true), 0);

        echo "Test on wolfs starving successfully completed\n";
    }

    protected function testRabbitStarving(): void
    {
        $this->initFieldWithAlivePlants(2, 2);

        $this->field->setCell(1, 0, new RabbitCell(1, 0, 2));

        $this->field->nextStep();
        $this->assertSame($this->field->getQuantityOfCells(RabbitCell::class, true), 1);
        $this->field->nextStep();
        $this->assertSame($this->field->getQuantityOfCells(RabbitCell::class, true), 1);
        $this->field->nextStep();
        $this->assertSame($this->field->getQuantityOfCells(RabbitCell::class, true), 0);

        echo "Test on rabbits starving successfully completed\n";
    }

    public function run(): void
    {
        $methodNames = [
            'testPlantGrowing',
            'testPlantDying',
            'testRabbitReproduction',
            'testBearHunting',
            'testBearSurviving',
            'testWolfHunting',
            'testStaticWaterBlock',
            'testBearStarving',
            'testWolfStarving',
            'testRabbitStarving',
        ];

        echo "Start forest field test...\n";

        foreach ($methodNames as $methodName) {
            $this->{$methodName}();
        }

        echo "\n";
    }
}
