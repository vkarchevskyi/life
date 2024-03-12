<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Commands\ConsoleCommand;
use App\Models\Fields\AbstractField;
use App\Models\Fields\ConwaysField;
use App\Views\Console\ConsoleView;

class ConsoleController extends AbstractController
{
    protected ConsoleView $view;

    protected ?AbstractField $field;


    public function __construct()
    {
        $this->view = new ConsoleView();
        $this->field = null;
    }

    #[\Override] public function run(): void
    {
        $working = true;

        do {
            $command = ConsoleCommand::from($this->view->getCommand());

            match ($command) {
                ConsoleCommand::EXIT => $working = false,
                ConsoleCommand::GET_FIELD => $this->view->printField($this->field),
                ConsoleCommand::CREATE_FIELD => $this->createField(),
                ConsoleCommand::PLAY => $this->play($this->view->getStepQuantity()),
                ConsoleCommand::GET_FIELD_INFO => $this->view->printFieldInfo($this->field),
            };

        } while ($working);
    }

    #[\Override] protected function getX(): int
    {
        return $this->view->getX();
    }

    #[\Override] protected function getY(): int
    {
        return $this->view->getY();
    }

    #[\Override] protected function getConnectedBorders(): bool
    {
        return $this->view->getConnectedBorders();
    }

    #[\Override] protected function createField(): void
    {
        $this->field = new ConwaysField(
            $this->getX(),
            $this->gety(),
            $this->getConnectedBorders()
        );

        try {
            $this->field->generateField();
        } catch (\Random\RandomException $e) {
            echo $e->getMessage();
            exit(1);
        }
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
}
