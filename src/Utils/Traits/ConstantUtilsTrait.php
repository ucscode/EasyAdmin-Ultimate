<?php

namespace App\Utils\Traits;

use ReflectionClass;

trait ConstantUtilsTrait
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
     * @param string|null $havingPrefix Get only choice starting with a particular prefix
     * @param bool $constantNameAsKey  Whether to use the constant name or value as array keys
     * @return array
     */
    public static function getChoices(?string $havingPrefix = null, bool $constantNameAsKey = false): array
    {
        $constants = self::getConstants();

        if($havingPrefix) {
            $regexp = sprintf("/^%s/", $havingPrefix);
            $constants = array_filter($constants, fn ($constantValue) => preg_match($regexp, $constantValue), ARRAY_FILTER_USE_KEY);
            $mapper = array_map(
                fn ($constantValue) => preg_replace($regexp, '', $constantValue),
                $constantNameAsKey ? array_keys($constants) : array_values($constants)
            );
        }

        $mapper = array_map(
            fn ($value) => trim(str_replace('_', ' ', $value)),
            $mapper ?? ($constantNameAsKey ? array_keys($constants) : array_values($constants))
        );

        return array_combine($mapper, $constants); // Display Value => Submitted Value
    }
}
