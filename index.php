<?php

use App\Console\EscapeCodes;
use App\Fields\Field;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$field = new Field(200, 50, true);

try {
    $field->generateField();
} catch (\Random\RandomException $e) {
    exit(1);
}


for ($i = 0; $i < 1000; $i++) {
    echo EscapeCodes::CLEAR_TERMINAL->value;
    $field->printField();
    usleep(50000);
    $field->nextStep();
}
