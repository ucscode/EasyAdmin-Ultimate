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
    public const OPTION_OFFSET = 'option._name';

    abstract protected function buildPattern(): void;

    /**
     * @var array<ParameterBag> $options
     */
    private array $patterns = [];
    
    private OptionsResolver $resolver;

    public function __construct()
    {
        $this->resolver = new OptionsResolver();
        $this->configureOptions($this->resolver);
        $this->buildPattern();
    }

    /**
     * Add a new pattern or update an existing pattern
     * 
     * @param string $name  The name of the pattern
     * @param array|ParameterBag $options   The configuration options of the pattern
     */
    public function addPattern(string $name, array|ParameterBag $pattern = []): static
    {
        if($pattern instanceof ParameterBag) {
            $pattern = $pattern->all();
        }

        $unresolvedPattern = [self::OPTION_OFFSET => $name] + array_replace($this->patterns[$name] ?? [], $pattern);

        $this->patterns[$name] = new ParameterBag($this->resolver->resolve($unresolvedPattern));
        
        return $this;
    }

    public function removePattern(string $name): static
    {
        if(array_key_exists($name, $this->patterns)) {
            unset($this->patterns[$name]);
        }

        return $this;
    }

    public function getPattern(string $name): ?ParameterBag
    {
        return $this->patterns[$name] ?? null;
    }

    /**
     * @return ParameterBag[]
     */
    public function getPatterns(): array
    {
        return $this->patterns;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired(self::OPTION_OFFSET)
            ->setAllowedTypes(self::OPTION_OFFSET, 'string')
        ;
    }
}