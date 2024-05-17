<?php

namespace App\Exceptions;

use LogicException;

class InvalidArrayValueException extends LogicException
{
    public const INVALID_ELEMENT_TYPE_MESSAGE = 'Array contains element of invalid type or class';
    public const INVALID_ELEMENT_LOGIC_MESSAGE = 'Array must contain only elements of type %s';
}