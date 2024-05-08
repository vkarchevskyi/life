<?php

declare(strict_types=1);

namespace Test\ConwaysGame;

use App\Models\ConwaysGame\Cells\DeadConwaysCell;
use App\Models\ConwaysGame\Cells\LiveConwaysCell;
use App\Models\ConwaysGame\Fields\ConwaysField;
use Test\AppTest;

class ConwayFieldTest extends AppTest
{
    protected const int FIELD_X_SIZE = 3;

    protected const int FIELD_Y_SIZE = 3;

    protected ConwaysField $field;

    protected function init(): void
    {
        $this->field = new ConwaysField(self::FIELD_X_SIZE, self::FIELD_Y_SIZE, false);

        for ($i = 0; $i < self::FIELD_Y_SIZE; $i++) {
            for ($j = 0; $j < self::FIELD_X_SIZE; $j++) {
                $this->field->setCell($j, $i, new DeadConwaysCell($j, $i));
            }
        }
    }

    protected function testDying(): void
    {
        $this->field->setCell(1, 0, new LiveConwaysCell(1, 0));
        $this->field->setCell(0, 1, new LiveConwaysCell(0, 1));
        $this->field->setCell(0, 2, new LiveConwaysCell(0, 2));

        $this->assertSame($this->field->getFieldInformation()['Alive'], 3);
        $this->field->nextStep();
        $this->assertSame($this->field->getFieldInformation()['Alive'], 2);
        $this->field->nextStep();
        $this->assertSame($this->field->getFieldInformation()['Alive'], 0);

        echo "Test on dying successfully completed\n";
    }

    protected function testRepeat(): void
    {
        $this->field->setCell(1, 0, new LiveConwaysCell(1, 0));
        $this->field->setCell(1, 1, new LiveConwaysCell(1, 1));
        $this->field->setCell(1, 2, new LiveConwaysCell(1, 2));

        $this->assertSame($this->field->getFieldInformation()['Alive'], 3);
        $this->field->nextStep();

        $this->assertTrue($this->field->getCell(1, 0) instanceof DeadConwaysCell);
        $this->assertTrue($this->field->getCell(1, 2) instanceof DeadConwaysCell);
        $this->assertTrue($this->field->getCell(0, 1) instanceof LiveConwaysCell);
        $this->assertTrue($this->field->getCell(1, 1) instanceof LiveConwaysCell);
        $this->assertTrue($this->field->getCell(2, 1) instanceof LiveConwaysCell);
        $this->assertSame($this->field->getFieldInformation()['Alive'], 3);

        $this->field->nextStep();

        $this->assertTrue($this->field->getCell(1, 0) instanceof LiveConwaysCell);
        $this->assertTrue($this->field->getCell(1, 2) instanceof LiveConwaysCell);
        $this->assertTrue($this->field->getCell(0, 1) instanceof DeadConwaysCell);
        $this->assertTrue($this->field->getCell(1, 1) instanceof LiveConwaysCell);
        $this->assertTrue($this->field->getCell(2, 1) instanceof DeadConwaysCell);
        $this->assertSame($this->field->getFieldInformation()['Alive'], 3);

        $this->field->nextStep();

        $this->assertTrue($this->field->getCell(1, 0) instanceof DeadConwaysCell);
        $this->assertTrue($this->field->getCell(1, 2) instanceof DeadConwaysCell);
        $this->assertTrue($this->field->getCell(0, 1) instanceof LiveConwaysCell);
        $this->assertTrue($this->field->getCell(1, 1) instanceof LiveConwaysCell);
        $this->assertTrue($this->field->getCell(2, 1) instanceof LiveConwaysCell);
        $this->assertSame($this->field->getFieldInformation()['Alive'], 3);

        echo "Test on repetition successfully completed\n";
    }

    protected function testStable(): void
    {
        $this->field->setCell(0, 0, new LiveConwaysCell(0, 0));
        $this->field->setCell(0, 1, new LiveConwaysCell(0, 1));
        $this->field->setCell(1, 0, new LiveConwaysCell(1, 0));
        $this->field->setCell(1, 1, new LiveConwaysCell(1, 1));

        $this->assertSame($this->field->getFieldInformation()['Alive'], 4);
        $this->field->nextStep();

        $this->assertTrue($this->field->getCell(0, 0) instanceof LiveConwaysCell);
        $this->assertTrue($this->field->getCell(0, 1) instanceof LiveConwaysCell);
        $this->assertTrue($this->field->getCell(1, 0) instanceof LiveConwaysCell);
        $this->assertTrue($this->field->getCell(1, 1) instanceof LiveConwaysCell);
        $this->assertSame($this->field->getFieldInformation()['Alive'], 4);

        echo "Test on stable figure successfully completed\n";
    }

    public function run(): void
    {
        $methodNames = [
            'testStable',
            'testDying',
            'testRepeat',
        ];

        echo "Start conways field test...\n";

        foreach ($methodNames as $methodName) {
            $this->init();
            $this->{$methodName}();
        }
        
        echo "\n";
    }
}
