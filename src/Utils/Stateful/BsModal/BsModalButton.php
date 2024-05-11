<?php

namespace App\Utils\Stateful\BsModal;

class BsModalButton
{
    protected array $attributes = [];
    protected ?string $label = 'Close';

    public function setAttributes(array $attributes): static
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function clearAttributes(): static
    {
        $this->attributes = [];

        return $this;
    }

    public function addAttribute(string $key, mixed $value): static
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    public function getAttribute(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }

    public function hasAttribute(string $key): bool
    {
        return array_key_exists($key, $this->attributes);
    }

    public function removeAttribute(string $key): static
    {
        if($this->hasAttribute($key)) {
            unset($this->attributes[$key]);
        }

        return $this;
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
}