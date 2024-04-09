<?php

namespace App\Enum;

enum ModeEnum: int
{
    const RESTRICTED = 0;
    const EXECUTE = 1;
    const WRITE = 2;
    const WRITE_EXECUTE = 3;
    const READ = 4;
    const READ_EXECUTE = 5;
    const READ_WRITE = 6;
    const READ_WRITE_EXECUTE = 7;
}