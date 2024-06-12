<?php

namespace App\Form\Extension\Affix;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AffixResolver
{
    public const AFFIX_KEY = 'affix';
    protected const SCALAR_VALUES = ['string', 'int', 'float', 'null'];

    // resolve "affix"
    public function resolveAffix(OptionsResolver $resolver): OptionsResolver
    {
        return $resolver
            ->setDefault(self::AFFIX_KEY, null)
            ->setAllowedTypes(self::AFFIX_KEY, ['array', 'null'])
            ->setNormalizer(
                self::AFFIX_KEY,
                fn (Options $options, ?array $value) => $this->resolveAffixTypes(new OptionsResolver(), $value ?? [])
            )
        ;
    }

    /**
     * - affix[prepend]
     * - affix[append]
     */
    public function resolveAffixTypes(OptionsResolver $resolver, array $affix): array
    {
        foreach(['prepend', 'append'] as $key) {
            $affix[$key] ??= null;

            $resolver
                ->setDefault($key, null)
                ->setAllowedTypes($key, array_merge(self::SCALAR_VALUES, ['array']))
                ->setNormalizer($key, fn (Options $options, mixed $value) => $this->normalizeAffixType($value))
            ;
        }

        return $resolver->resolve($affix);
    }

    /**
     * $key = 'prepend' or 'append'
     *
     * - affix[$key]['type']
     * - affix[$key]['value']
     */
    public function resolveAffixTypeOptions(OptionsResolver $resolver, array $values): array
    {
        $resolver
            ->setDefaults([
                'type' => 'text',
            ])
            ->setRequired('value')
            ->setAllowedTypes('type', 'string')
            ->setAllowedValues('type', ['text', 'icon', 'button'])
            ->setAllowedTypes('value', array_merge(self::SCALAR_VALUES, ['array']))
            ->setNormalizer('value', fn (Options $options, mixed $value) => $this->normalizeAffixTypeOptions($value, $options))
        ;

        return $resolver->resolve($values);
    }

    public function resolveButtonOptions(OptionsResolver $resolver, array $value): array
    {
        $resolver
            ->setDefaults([
                'icon' => null,
                'label' => null,
                'attributes' => [],
            ])
            ->setAllowedTypes('icon', ['string', 'null'])
            ->setAllowedTypes('label', self::SCALAR_VALUES)
            ->setAllowedTypes('attributes', 'array')
            ->setNormalizer('attributes', function (Options $options, array $value): array {
                return $value + [
                    'type' => 'button',
                    'class' => 'btn btn-secondary'
                ];
            })
            ->setNormalizer('label', function (Options $options, ?string $value): ?string {
                return $value ?? ($options['icon'] ? null : 'BUTTON');
            })
        ;

        return $resolver->resolve($value);
    }

    protected function normalizeAffixType($value): ?array
    {
        if($value !== null) {
            is_array($value) ?: $value = [
                'type' => 'text',
                'value' => $value,
            ];
            return $this->resolveAffixTypeOptions(new OptionsResolver(), $value);
        }
        return $value;
    }

    protected function normalizeAffixTypeOptions(mixed $value, Options $options): string|array
    {
        if($options['type'] === 'button') {
            is_array($value) ?: $value = [
                'label' => $value,
                'attributes' => [
                    'value' => $value
                ]
            ];
            return $this->resolveButtonOptions(new OptionsResolver(), $value);
        }
        return $value;
    }
}
