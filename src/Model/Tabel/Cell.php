<?php

namespace App\Model\Table;

class Cell
{
    protected ?string $value = null;

    public function __construct(protected ?string $label = null)
    {
        //
    }

    public static function new(string $label): static
    {
        return new static($label);
    }

    public function setLabel(?string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setValue(?string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }
}
