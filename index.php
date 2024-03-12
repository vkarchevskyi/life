<?php

use App\Controllers\ConsoleController;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

(new ConsoleController())->run();
