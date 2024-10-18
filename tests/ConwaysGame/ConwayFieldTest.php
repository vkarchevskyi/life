<?php

declare(strict_types=1);

namespace Test\ConwaysGame;

use App\Models\ConwaysGame\Cells\DeadConwaysCell;
use App\Models\ConwaysGame\Cells\LiveConwaysCell;
use App\Models\ConwaysGame\Fields\ConwaysField;
use PHPUnit\Framework\TestCase;

class ConwayFieldTest extends TestCase
{
    protected const int FIELD_X_SIZE = 3;

    protected const int FIELD_Y_SIZE = 3;

    protected ConwaysField $field;

    protected function setUp(): void
    {
        $this->field = new ConwaysField(self::FIELD_X_SIZE, self::FIELD_Y_SIZE, false);

        for ($i = 0; $i < self::FIELD_Y_SIZE; $i++) {
            for ($j = 0; $j < self::FIELD_X_SIZE; $j++) {
                $this->field->setCell($j, $i, new DeadConwaysCell($j, $i));
            }
        }
    }

    public function testDyingFeature(): void
    {
        $this->field->setCell(1, 0, new LiveConwaysCell(1, 0));
        $this->field->setCell(0, 1, new LiveConwaysCell(0, 1));
        $this->field->setCell(0, 2, new LiveConwaysCell(0, 2));

        $this->assertSame($this->field->getFieldInformation()['Alive'], 3);
        $this->field->nextStep();
        $this->assertSame($this->field->getFieldInformation()['Alive'], 2);
        $this->field->nextStep();
        $this->assertSame($this->field->getFieldInformation()['Alive'], 0);
    }

    public function testRepeatPattern(): void
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
    }

    public function testStablePattern(): void
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
    }
}
