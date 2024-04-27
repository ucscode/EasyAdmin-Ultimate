<?php

namespace App\Enum;

enum ModeEnum: int
{
    case READ = 4;
    case WRITE = 2;
    case EXECUTE = 1;
    case NO_PERMISSION = 0;
}
