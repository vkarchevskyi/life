<?php

declare(strict_types=1);

namespace App\Views\Console;

use App\Models\Fields\AbstractField;
use App\Models\Fields\ConwaysField;
use App\Models\Fields\ForestField;
use App\Views\AbstractView;

class ConsoleView extends AbstractView
{
    /**
     * @return string
     */
    #[\Override] public function getX(): string
    {
        return readline('Select x size of field: ');
    }

    /**
     * @return string
     */
    #[\Override] public function getY(): string
    {
        return readline('Select y size of field: ');
    }

    /**
     * @return string
     */
    #[\Override] public function getConnectedBorders(): string
    {
        return readline('Should borders be connected (y/n): ');
    }

    /**
     * @return string
     */
    #[\Override] public function getCommand(): string
    {
        echo "1. Create field.\n";
        echo "2. Print field information.\n";
        echo "3. Print field.\n";
        echo "4. Start game simulation.\n";
        echo "0. Exit.\n";

        return readline('Enter the command: ');
    }

    /**
     * @return string
     */
    #[\Override] public function getStepQuantity(): string
    {
        return readline('Enter the quantity of steps: ');
    }

    /**
     * @return string
     */
    #[\Override] public function getFieldType(): string
    {
        do {
            echo "1. Conway's life\n";
            echo "2. Forest\n";

            $fieldType = intval(readline('Select the type of game: '));

            switch ($fieldType) {
                case 1:
                    return ConwaysField::class;
                case 2:
                    return ForestField::class;
                default:
                    break;
            }

            echo "Incorrect data. Please try again.\n";
        } while (true);
    }

    /**
     * @return void
     */
    public function printIncorrectDataMessage(): void
    {
        echo "Incorrect data. Please try again.\n";
    }

    /**
     * @param AbstractField $field
     * @return void
     */
    public function printField(AbstractField $field): void
    {
        $xSize = $field->getXSize();
        $ySize = $field->getYSize();

        for ($y = 0; $y < $ySize; $y++) {
            for ($x = 0; $x < $xSize; $x++) {
                echo $field->getCell($x, $y);
            }

            echo PHP_EOL;
        }
    }

    /**
     * Print information about field's cells types or states and it's quantity
     *
     * @param AbstractField $field
     * @return void
     */
    public function printFieldInfo(AbstractField $field): void
    {
        $cellTypes = $field->getFieldInformation();

        $cellTypes['TOTAL'] = array_reduce($cellTypes, function (int $total, int $quantity) {
            $total += $quantity;
            return $total;
        }, 0);

        foreach ($cellTypes as $cellType => $cellsQuantity) {
            echo "$cellType: $cellsQuantity\n";
        }
    }

    /**
     * Cross-platform method to clear console
     *
     * @return void
     */
    public function clearConsole(): void
    {
        if (PHP_OS_FAMILY === 'Windows') {
            echo EscapeCodes::CLEAR_TERMINAL->value;
        } else {
            system("clear");
        }
    }
}
