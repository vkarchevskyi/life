<?php

declare(strict_types=1);

namespace App\Models\Commands;

enum ConsoleCommand: int
{
    case EXIT = 0;

    case CREATE_FIELD = 1;

    case GET_FIELD_INFO = 2;

    case GET_FIELD = 3;

    case PLAY = 4;
}
