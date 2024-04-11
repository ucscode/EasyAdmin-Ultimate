<?php

namespace App\Service;

class PrimaryTaskService
{
    public function keygen(int $length = 10, bool $includeSpecialChars = false): string
    {
        $characters = [
            ...range(0, 9), 
            ...range('a', 'z'), 
            ...range('A', 'Z')
        ];

        if($includeSpecialChars) {
            $specialChars = ['!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '[', ']', '{', '}', '/', ':', '.', ';', '|', '>', '~', '_', '-'];
            $characters = [...$characters, ...$specialChars];
        };

        $keyset = [];

        for($x = 0; $x < $length; $x++) {
            $randKey = array_rand($characters);
            $keyset[] = $characters[$randKey];
        };

        return implode($keyset);
    }

    public function truncateText(string $text, $length = 63): string
    {
        if (strlen($text) > $length) {
            $text = substr($text, 0, $length) . '&hellip;';
        }
        return $text;
    }
}