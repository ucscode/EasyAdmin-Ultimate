<?php

namespace App\Utility\Table;

class Cell
{
    protected array $attributes = [];
    protected array $parameters = [];

    public function __construct(protected ?string $value = null)
    {}

    public static function new(?string $value = null): static
    {
        return new static($value);
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

    public function setAttributes(array $attributes): static
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function set(string $name, mixed $value): static
    {
        $this->parameters[$name] = $value;

        return $this;
    }

    public function get(string $name): mixed
    {
        return $this->parameters[$name] ?? null;
    }

    public function remove(string $name): static
    {
        if(array_key_exists($name, $this->parameters)) {
            unset($this->parameters[$name]);
        }

        return $this;
    }

    public function all(): array
    {
        return $this->parameters;
    }
}
