<?php

declare(strict_types=1);

namespace App\Views\Console\Constants;

enum ConsoleCommand: int
{
    case EXIT = 0;

    case CREATE_FIELD = 1;

    case GET_FIELD_INFO = 2;

    case GET_FIELD = 3;

    case PLAY = 4;

    case REFRESH_SPEED = 5;
}
