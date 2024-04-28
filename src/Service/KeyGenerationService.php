<?php

namespace App\Service;

class KeyGenerationService
{
    public function generateKey(int $length = 10, bool $includeSpecialChars = false): string
    {
        $characters = [...range('0', '9'), ...range('a', 'z'), ...range('A', 'Z')];

        if ($includeSpecialChars) {
            $specialChars = ['!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '[', ']', '{', '}', '/', ':', '.', ';', '|', '>', '~', '_', '-'];
            $characters = [...$characters, ...$specialChars];
        }

        $keyset = [];
        
        for ($x = 0; $x < $length; $x++) {
            $randKey = array_rand($characters);
            $keyset[] = $characters[$randKey];
        }

        return implode($keyset);
    }
}