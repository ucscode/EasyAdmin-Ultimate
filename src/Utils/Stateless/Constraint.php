<?php

namespace App\Utils\Stateless;

use App\Exceptions\InvalidArrayValueException;

class Constraint
{
    /**
     * @param array $array                  The array to test
     * @param string|object $expectedType   The expected element that each object must contain
     *
     * @return bool                         if all elements are of the same type
     */
    public static function isArrayOf(array $array, string|object $expectedType): bool
    {
        foreach($array as $item) {
            if(!($item instanceof $expectedType || gettype($item) === $expectedType)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Same as isArrayOf but throws exception rather than return a value
     *
     * @throws InvalidArrayValueException  if array contain invalid element
     */
    public static function assertIsArrayOf(array $array, string|object $expectedType): void
    {
        if(!self::isArrayOf($array, $expectedType)) {
            throw new InvalidArrayValueException(
                sprintf(InvalidArrayValueException::INVALID_ELEMENT_LOGIC_MESSAGE, $expectedType)
            );
        }
    }
}
