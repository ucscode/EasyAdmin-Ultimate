<?php

namespace App\Model\Modal;

class ModalButton
{
    protected ?string $name;
    protected array $attributes;
    protected ?string $label;

    public function __construct(?string $name = null, ?string $label = null, array $attributes = [])
    {
        $this->name = $name;
        $this->label = $label ?? ($name ?? 'close');

        $this->attributes = $attributes + [
            'type' => 'button',
            'class' => 'btn btn-primary text-capitalize',
            'data-bs-dismiss' => 'modal',
            'data-bs-toggle' => 'modal',
        ];
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
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
