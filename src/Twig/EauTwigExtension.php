<?php

namespace App\Twig;

use App\Context\EauContext;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;

class EauTwigExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(protected EauContext $eauContext)
    {

    }

    public function getGlobals(): array
    {
        return [
            'eau' => $this->eauContext,
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('_simple_widget_concat_context', fn ($value) => $this->simpleWidgetConcatContext($value)),
            new TwigFunction('dirname', fn (string $path, int $levels = 1) => dirname($path, $levels)),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('is_int', fn ($item) => is_int($item)),
            new TwigFilter('is_float', fn ($item) => is_float($item)),
            new TwigFilter('is_array', fn ($item) => is_array($item)),
            new TwigFilter('is_object', fn ($item) => is_object($item)),
            new TwigFilter('is_scalar', fn ($item) => is_scalar($item)),
            new TwigFilter('is_boolean', fn ($item) => is_bool($item)),
            new TwigFilter('html_attributes', fn (iterable $item) => $this->iterableToHtmlAttributes($item)),
            new TwigFilter('boolean_as_string', fn ($item) => is_bool($item) ? ($item ? 'true' : 'false') : $item),
        ];
    }

    protected function iterableToHtmlAttributes(iterable $item): string
    {
        $aligner = [];

        foreach($item as $key => $value) {
            is_string($value) ?: $value = json_encode($value);
            $value = htmlentities($value);
            $aligner[] = sprintf('%s="%s"', $key, $value);
        }

        return implode(" ", $aligner);
    }

    protected function simpleWidgetConcatContext(string|array|null $item): ?array
    {
        if (!is_array($item)) {
            $item = ['value' => is_scalar($item) && !is_bool($item) ? $item : null];
        }

        $item['type'] = strtolower($item['type'] ?? 'text');

        if ($item['value'] === null) {
            return null;
        }

        switch ($item['type']) {
            case 'button':
                $value = is_scalar($item['value']) ? ['label' => $item['value']] : $item['value'];
                $value['icon'] ??= null;
                $value['label'] ??= ($value['icon'] ? null : 'BUTTON');
                $value['attributes'] = ($value['attributes'] ?? []) + [
                    'type' => 'button',
                    'class' => 'btn btn-secondary'
                ];
                $item['value'] = $value;
                break;
            default:
                $item['value'] = is_scalar($item['value']) && !is_bool($item['value']) ? $item['value'] : null;
        };

        return $item['value'] !== null ? $item : null;
    }

}
