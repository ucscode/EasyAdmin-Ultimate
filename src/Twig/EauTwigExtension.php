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
