<?php

namespace App\Configuration;

use Webmozart\Assert\Assert;

abstract class AbstractConfigurationPattern
{
    abstract static function getItemFqcn(): string;
    abstract protected function configureItems(): void;

    private array $items = [];

    public function __construct()
    {
        $this->configureItems();

        foreach($this->items as $key => $value) {
            $this->normalizeItem($key, $value);
        }
    }

    public function setItem(string $name, mixed $value): static
    {
        Assert::isInstanceOf($value, static::getItemFqcn());

        $this->items[$name] = $value;

        return $this;
    }

    public function getItem(string $name): mixed
    {
        return $this->items[$name] ?? null;

        return $this;
    }

    public function removeItem(string $name): static
    {
        if(array_key_exists($name, $this->items)) {
            unset($this->items[$name]);
        }

        return $this;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    protected function normalizeItem(string $name, $value): void
    {}
}