<?php

declare(strict_types=1);

namespace App\Views;

abstract class AbstractView
{
    abstract public function getX(): string;

    abstract public function getY(): string;

    abstract public function getConnectedBorders(): string;

    abstract public function getCommand(bool $fieldWasCreated): string;

    abstract public function getStepQuantity(): string;

    abstract public function getFieldType(): string;
}
