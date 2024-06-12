<?php

namespace App\Utility\Table;

class Cell
{
    /**
     * @var string[] This stores HTML attributes for <tr> element
     */
    protected array $attributes = [];

    /**
     * @var mixed[] This stores a random values that may be persisted within the application
     */
    protected array $metas = [];

    /**
     * @var bool Whether to add cell to table
     */
    protected bool $hidden = false;

    /**
     * The constructor saves the original value into the metadata.
     *
     * For safety and to prevent unexpected behavior, it is advised not to change this original value.
     * Many configurators may update the $value property, but they will always rely on the original
     * metadata value for data retrieval and persistence.
     *
     * @param ?string $value The original value to be stored in the metadata.
     */
    public function __construct(protected ?string $value = null)
    {
        $this->setMeta('value', $value);
    }

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

    public function setAttribute(string $name, mixed $value): static
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    public function getAttribute(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }

    public function removeAttribute(string $name): static
    {
        if(array_key_exists($name, $this->attributes)) {
            unset($this->attributes[$name]);
        }

        return $this;
    }

    public function setMetas(array $metas): static
    {
        $this->metas = $metas;

        return $this;
    }

    public function getMetas(): array
    {
        return $this->metas;
    }

    public function setMeta(string $name, mixed $value): static
    {
        $this->metas[$name] = $value;

        return $this;
    }

    public function getMeta(string $name): mixed
    {
        return $this->metas[$name] ?? null;
    }

    public function removeMeta(string $name): static
    {
        if(array_key_exists($name, $this->metas)) {
            unset($this->metas[$name]);
        }

        return $this;
    }

    public function setHidden(bool $hidden): static
    {
        $this->hidden = $hidden;

        return $this;
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }
}
