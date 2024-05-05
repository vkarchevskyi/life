<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\AbstractGame\Fields\AbstractField;
use App\Views\Console\Constants\ConsoleCommand;
use App\Views\Console\Views\ConsoleView;

class ConsoleController extends AbstractController
{
    protected int $refreshConsoleSpeedInMicroseconds = 50000;

    protected ConsoleView $view;

    protected AbstractField $field;


    public function __construct()
    {
        $this->view = new ConsoleView();
    }

    #[\Override] public function run(): void
    {
        $working = true;

        while ($working) {
            $fieldWasCreated = isset($this->field);

            do {
                $command = intval($this->view->getCommand($fieldWasCreated));

                if (ConsoleCommand::tryFrom($command)) {
                    $command = ConsoleCommand::from($command);
                    break;
                }

                $this->view->printIncorrectDataMessage();
            } while (true);

            if ($fieldWasCreated) {
                match ($command) {
                    ConsoleCommand::EXIT => $working = false,
                    ConsoleCommand::GET_FIELD => $this->view->printField($this->field),
                    ConsoleCommand::CREATE_FIELD => $this->createField(),
                    ConsoleCommand::PLAY => $this->play($this->getStepQuantity()),
                    ConsoleCommand::GET_FIELD_INFO => $this->view->printFieldInfo($this->field),
                    ConsoleCommand::REFRESH_SPEED => $this->setConsoleSpeedInMicroseconds(),
                };
            } else {
                match ($command) {
                    ConsoleCommand::EXIT => $working = false,
                    ConsoleCommand::CREATE_FIELD => $this->createField(),
                    default => null,
                };
            }
        }
    }

    #[\Override] protected function getFieldXSize(): int
    {
        while (true) {
            $xSize = $this->view->getFieldXSize();

            if (!is_numeric($xSize) || $xSize <= 0) {
                echo "Incorrect X size. X size must be a positive integer.\n";
            } else {
                break;
            }
        }

        return intval($xSize);
    }

    #[\Override] protected function getFieldYSize(): int
    {
        while (true) {
            $ySize = $this->view->getFieldYSize();

            if (!is_numeric($ySize) || $ySize <= 0) {
                echo "Incorrect Y size. Y size must be a positive integer.\n";
            } else {
                break;
            }
        }

        return intval($ySize);
    }

    #[\Override] protected function getConnectedBorders(): bool
    {
        $response = $this->view->getConnectedBorders();

        return !in_array($response, ['no', 'n', 'nope']);
    }

    #[\Override] protected function createField(): void
    {
        $fieldType = $this->view->getFieldType();

        $this->field = new $fieldType(
            $this->getFieldXSize(),
            $this->getFieldYSize(),
            $this->getConnectedBorders()
        );

        $this->field->generateField();
    }

    #[\Override] protected function play(int $stepQuantity): void
    {
        for ($i = 0; $i < $stepQuantity; $i++) {
            $this->view->clearConsole();
            $this->view->printField($this->field);
            usleep($this->refreshConsoleSpeedInMicroseconds);
            $this->field->nextStep();
        }
    }

    #[\Override] protected function getStepQuantity(): int
    {
        while (true) {
            $quantity = $this->view->getStepQuantity();

            if (!is_numeric($quantity) || $quantity < 0) {
                echo "Incorrect quantity. Steps quantity must be a positive integer.\n";
            } else {
                break;
            }
        }

        return intval($quantity);
    }

    protected function setConsoleSpeedInMicroseconds(): void
    {
        while (true) {
            $seconds = $this->view->getConsoleSpeed();

            if (!is_numeric($seconds) || $seconds < 0) {
                echo "Incorrect speed. Speed must be a positive integer.\n";
            } else {
                break;
            }
        }

        $this->refreshConsoleSpeedInMicroseconds = intval($seconds) * 1000000;// convert seconds to microseconds
    }
}
