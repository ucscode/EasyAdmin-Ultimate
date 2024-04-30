<?php

namespace App\Service;

class KeyGenerationService
{
    protected const SPECIAL_CHARS = ['!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '[', ']', '{', '}', '/', ':', '.', ';', '|', '>', '~', '_', '-'];

    protected array $characters;

    public function __construct()
    {
        $this->characters = [
            ...array_map('strval', range('0', '9')), 
            ...range('a', 'z'), 
            ...range('A', 'Z')
        ];
    }

    public function setCharacters(string|array $characters): self
    {
        $this->characters = $this->moderateCharacters($characters);

        return $this;
    }

    public function addCharacters(string|array $characters): self
    {
        $this->characters = $this->moderateCharacters([
            ...$this->characters, 
            ...$this->moderateCharacters($characters)
        ]);

        return $this;
    }

    public function removeCharacters(string|array $characters): self
    {
        $this->characters = array_diff($this->characters, $this->moderateCharacters($characters));

        return $this;
    }

    public function applySpecialCharacters(bool $include = true): self
    {
        $include ? $this->addCharacters(self::SPECIAL_CHARS) : $this->removeCharacters(self::SPECIAL_CHARS);

        return $this;
    }

    public function generateKey(int $length = 10): string
    {
        $collection = [];
        
        for ($index = 0; $index < abs($length); $index++) {
            $randomIndex = array_rand($this->characters);
            $collection[] = $this->characters[$randomIndex];
        }

        return implode($collection);
    }

    /**
     * @param string|string[] $characters
     */
    protected function moderateCharacters(string|array $characters): array
    {
        if(is_string($characters)) {
            $characters = str_split($characters);
        }

        $filteredCharacters = [];
        
        foreach($characters as $char) {
            if(is_numeric($char)) {
                $char = strval(abs(intval($char)));
            }

            if(is_string($char)) {
                if(strlen($char) !== 1) {
                    $charArray = str_split($char);
                    foreach($charArray as $char) {
                        $filteredCharacters[] = trim($char);
                    }
                    continue;
                }
                $filteredCharacters[] = trim($char);
            }
        }

        $filteredCharacters = array_filter($filteredCharacters, fn($char) => strlen($char) === 1);

        return array_values(array_unique($filteredCharacters));
    }
}