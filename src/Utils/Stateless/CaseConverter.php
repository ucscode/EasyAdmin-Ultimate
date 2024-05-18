<?php

namespace App\Utils\Stateless;

class CaseConverter
{
    public const CASE_CAMEL = 1;
    public const CASE_SNAKE = 2;
    public const CASE_SENTENCE = 3;

    public static function toCamelCase(string $chars): string
    {
        $capitalized = ucwords(str_replace('_', ' ', $chars));
        return lcfirst(str_replace(' ', '', $capitalized));
    }

    public static function toSnakeCase(string $chars): string
    {
        $snakeCase = preg_replace('/([a-z])([A-Z])/', "\\1_\\2", $chars);
        return strtolower($snakeCase);
    }

    public static function toSentenceCase(string $chars): string
    {
        // Replace underscores with spaces
        $sentenceCase = str_replace('_', ' ', $chars);
        $sentenceCase = preg_replace('/(?<=\\w)(?=[A-Z])/', " $1", $sentenceCase);
        return $sentenceCase;
    }

    public static function recursiveCaseConversion(array|string $chars, int $toCase): string|array
    {
        if(!is_array($chars)) {
            return match($toCase) {
                self::CASE_CAMEL => self::toCamelCase($chars),
                self::CASE_SNAKE => self::toSnakeCase($chars),
                self::CASE_SENTENCE => self::toSentenceCase($chars),
            };
        }

        return array_map(fn (string $char) => self::recursiveCaseConversion($char, $toCase), $chars);
    }
}
