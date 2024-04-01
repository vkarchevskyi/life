<?php

declare(strict_types=1);

namespace App\Views\Console;

use App\Models\Commands\ConsoleCommand;
use App\Models\Fields\AbstractField;
use App\Models\Fields\ConwaysField;
use App\Models\Fields\ForestField;
use App\Views\AbstractView;

class ConsoleView extends AbstractView
{
    public function clearConsole(): void
    {
        if (PHP_OS_FAMILY === 'Windows') {
            echo EscapeCodes::CLEAR_TERMINAL->value;
        } else {
            system("clear");
        }
    }

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

    public function printFieldInfo(AbstractField $field): void
    {
        // TODO: implement this method
    }

    #[\Override] public function getX(): int
    {
        do {
            $xSize = readline('Select x size of field: ');
        } while (!is_numeric($xSize) || $xSize <= 0);

        return intval($xSize);
    }

    #[\Override] public function getY(): int
    {
        do {
            $ySize = readline('Select y size of field: ');
        } while (!is_numeric($ySize) || $ySize <= 0);

        return intval($ySize);
    }

    #[\Override] public function getConnectedBorders(): bool
    {
        $response = readline('Should borders be connected (y/n): ');

        return !in_array($response, ['no', 'n', 'nope']);
    }

    #[\Override] public function getCommand(): int
    {
        do {
            echo "1. Create field.\n";
            echo "2. Print field information.\n";
            echo "3. Print field.\n";
            echo "4. Start game simulation.\n";
            echo "0. Exit.\n";

            $command = intval(readline('Enter the command: '));

            if (ConsoleCommand::tryFrom($command)) {
                return ConsoleCommand::from($command)->value;
            }

            echo "Incorrect data. Please try again.\n";
        } while (true);
    }

    #[\Override] public function getStepQuantity(): int
    {
        do {
            $quantity = readline('Enter the quantity of steps: ');
        } while (!is_numeric($quantity) || $quantity < 0);

        return intval($quantity);
    }

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
}
