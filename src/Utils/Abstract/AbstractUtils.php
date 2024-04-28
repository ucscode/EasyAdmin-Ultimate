<?php

namespace App\Utils\Abstract;

use ReflectionClass;

abstract class AbstractUtils
{
    /**
     * Get all constants declared in the inheriting class
     *
     * @return array - defined constants
     */
    public static function getConstants(): array
    {
        return (new ReflectionClass(static::class))->getConstants();
    }

    /**
     * Get all or limited constants as Choice Field Options
     *
     * This accepts a prefix to filter constants and then return it as choices to be selected
     *
     * @return array
     */
    public static function getChoices(?string $prefix = null, bool $displayKey = false): array
    {
        $constants = self::getConstants();

        if($prefix) {
            $regexp = sprintf("/^%s/", $prefix);
            $constants = array_filter($constants, fn ($constantValue) => preg_match($regexp, $constantValue), ARRAY_FILTER_USE_KEY);
            $mapper = array_map(
                fn ($constantValue) => preg_replace($regexp, '', $constantValue),
                $displayKey ? array_keys($constants) : array_values($constants)
            );
        }

        $mapper = array_map(
            fn ($value) => trim(str_replace('_', ' ', $value)),
            $mapper ?? ($displayKey ? array_keys($constants) : array_values($constants))
        );

        return array_combine($mapper, $constants); // Display Value => Submitted Value
    }
}
