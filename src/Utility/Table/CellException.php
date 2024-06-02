<?php

namespace App\Utility\Table;

use InvalidArgumentException;

class CellException extends InvalidArgumentException
{
    public const ERROR_PARAMETER_MESSAGE = 'Invalid value for cell parameter "%s". Expected: "%s", but received: "%s".';
}