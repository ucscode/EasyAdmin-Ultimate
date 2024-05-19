<?php

namespace App\Component\Abstracts;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * AbstractPattern class for defining and managing configuration patterns.
 *
 * This abstract class provides a base for creating configuration patterns with
 * default options and validation using Symfony's OptionsResolver. It ensures
 * that all options are validated and resolved consistently across the application.
 *
 * @package App\Component
 */
abstract class AbstractPattern
{
    abstract protected function configureOptions(OptionsResolver $resolver): void;
    abstract protected function createDefaultOptions(): array;

    protected array $options = [];
    protected OptionsResolver $resolver;

    public function __construct()
    {
        $this->resolver = new OptionsResolver();
        $this->configureOptions($this->resolver);
        $this->options = $this->resolver->resolve($this->createDefaultOptions());
    }

    public function set(string $name, mixed $value): static
    {
        $this->options[$name] = $value;
        $this->options = $this->all();
        return $this;
    }

    public function get(string $name): mixed
    {
        return $this->options[$name] ?? null;
    }

    public function all(): array
    {
        return $this->resolver->resolve($this->options);
    }
}