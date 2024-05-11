<?php

namespace App\Twig;

use App\Context\EauContext;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;

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

    public function getFilters(): array
    {
        return [
            new TwigFilter('html_attributes', fn (iterable $item) => $this->iterableToHtmlAttributes($item)),
            new TwigFilter('is_array', fn ($item) => is_array($item)),
            new TwigFilter('is_object', fn ($item) => is_object($item)),
            new TwigFilter('is_boolean', fn ($item) => is_bool($item)),
            new TwigFilter('is_int', fn ($item) => is_int($item)),
            new TwigFilter('is_float', fn ($item) => is_float($item)),
            new TwigFilter('is_scalar', fn ($item) => is_scalar($item)),
            new TwigFilter('bool_string', fn ($item) => is_bool($item) ? ($item ? 'true' : 'false') : $item),
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
}
