<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\AbstractGame\Fields\AbstractField;
use App\Views\Console\Constants\ConsoleCommand;
use App\Views\Console\Views\ConsoleView;

class ConsoleController extends AbstractController
{
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

    #[\Override] protected function getX(): int
    {
        do {
            $xSize = $this->view->getX();
        } while (!is_numeric($xSize) || $xSize <= 0);

        return intval($xSize);
    }

    #[\Override] protected function getY(): int
    {
        do {
            $ySize = $this->view->getY();
        } while (!is_numeric($ySize) || $ySize <= 0);

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
            $this->getX(),
            $this->gety(),
            $this->getConnectedBorders()
        );

        $this->field->generateField();
    }

    #[\Override] protected function play(int $stepQuantity): void
    {
        for ($i = 0; $i < $stepQuantity; $i++) {
            $this->view->clearConsole();
            $this->view->printField($this->field);
            usleep(50000);
            $this->field->nextStep();
        }
    }

    #[\Override] protected function getStepQuantity(): int
    {
        do {
            $quantity = $this->view->getStepQuantity();
        } while (!is_numeric($quantity) || $quantity < 0);

        return intval($quantity);
    }
}
