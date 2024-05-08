<?php

use Test\ConwaysGame\ConwayFieldTest;
use Test\ForestGame\ForestFieldTest;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

(new ConwayFieldTest())->run();
(new ForestFieldTest())->run();
