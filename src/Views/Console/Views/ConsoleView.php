<?php

declare(strict_types=1);

namespace App\Views\Console\Views;

use App\Models\AbstractGame\Fields\AbstractField;
use App\Models\ConwaysGame\Fields\ConwaysField;
use App\Models\Forest\Fields\ForestField;
use App\Views\AbstractView;
use App\Views\Console\Constants\EscapeCodes;

class ConsoleView extends AbstractView
{
    /**
     * @return string
     */
    #[\Override] public function getFieldXSize(): string
    {
        return readline('Select x size of field: ');
    }

    /**
     * @return string
     */
    #[\Override] public function getFieldYSize(): string
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
     * @param bool $fieldWasCreated
     * @return string
     */
    #[\Override] public function getCommand(bool $fieldWasCreated): string
    {
        echo "1. Create field.\n";

        if ($fieldWasCreated) {
            echo "2. Print field information.\n";
            echo "3. Print field.\n";
            echo "4. Start game simulation.\n";
            echo "5. Change refresh console speed.\n";
        }

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

            $this->printIncorrectDataMessage();
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
     * @param array $cellTypes
     * @return void
     */
    public function printFieldInfo(array $cellTypes): void
    {
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

    public function getConsoleSpeed(): string
    {
        return readline("Enter the refresh screen speed in seconds: ");
    }
}
