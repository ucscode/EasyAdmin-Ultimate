<?php

namespace App\Component\Abstracts;

use Symfony\Component\HttpFoundation\ParameterBag;
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
    protected const OPTION_OFFSET = 'option._name';

    abstract protected function buildPattern(): void;

    /**
     * @var array<ParameterBag> $options
     */
    protected array $options = [];
    
    protected OptionsResolver $resolver;

    public function __construct()
    {
        $this->resolver = new OptionsResolver();
        $this->configureOptions($this->resolver);
        $this->buildPattern();
    }

    public function setPattern(string $name, array|ParameterBag $options = []): static
    {
        if($options instanceof ParameterBag) {
            $options = $options->all();
        }

        $options[self::OPTION_OFFSET] = $name;

        $this->options[$name] = new ParameterBag($this->resolver->resolve($options));
        
        return $this;
    }

    public function getPattern(string $name): ?ParameterBag
    {
        return $this->options[$name] ?? null;
    }

    /**
     * @return ParameterBag[]
     */
    public function getPatterns(): array
    {
        return $this->options;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired(self::OPTION_OFFSET)
            ->setAllowedTypes(self::OPTION_OFFSET, 'string')
        ;
    }
}