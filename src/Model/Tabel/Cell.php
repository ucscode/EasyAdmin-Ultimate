<?php

namespace App\Model\Table;

class Cell
{
    public const TYPE_COLUMN = 'column';
    public const TYPE_DATA = 'data';

    protected ?string $value = null;
    protected string $type;

    public function __construct(protected ?string $label = null)
    {
    }

    public static function new(string $label): static
    {
        return new self($label);
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

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
