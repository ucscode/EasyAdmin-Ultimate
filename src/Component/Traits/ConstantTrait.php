<?php

namespace App\Component\Traits;

use ReflectionClass;

trait ConstantTrait
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
     * Get all the current class constants as an array representing Choice Field Options
     *
     * This method fetches the current class constants and optionally filters them by a specified prefix.
     * The keys and values of the resulting array can be formatted using the provided callbacks.
     *
     * @param string|null $withPrefix   Get only choice starting with a particular prefix
     * @param callable $formatKey       Format the keys of the resulting array
     * @param callable $formatValue     Format the values of the resulting array
     * @return array
     */
    public static function getChoices(?string $withPrefix = null, ?callable $formatKey = null, ?callable $formatValue = null): array
    {
        $constants = self::getConstants();

        if($withPrefix) {
            $constants = array_filter(
                $constants,
                fn ($value) => preg_match(sprintf("/^%s/", $withPrefix), $value),
                ARRAY_FILTER_USE_KEY
            );
        }

        $arrayKeys = $formatKey ? array_map($formatKey, array_keys($constants)) : array_keys($constants);
        $arrayValues = $formatValue ? array_map($formatValue, array_values($constants)) : array_values($constants);

        return array_combine($arrayKeys, $arrayValues); // Display Value => Submit Value
    }
}
