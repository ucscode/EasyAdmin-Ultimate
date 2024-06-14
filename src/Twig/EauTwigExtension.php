<?php

namespace App\Twig;

use App\Context\EauContext;
use App\Form\Extension\Affix\AffixResolver;
use App\Service\JsPayload;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;

class EauTwigExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(protected EauContext $eauContext, protected JsPayload $javascriptContext)
    {
        //
    }

    public function getGlobals(): array
    {
        return [
            'eau' => $this->eauContext,
            'js_payload' => base64_encode(json_encode($this->javascriptContext->all()))
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('_evaluate_widget_affix', fn ($value) => $this->evaluateWidgetAffix($value)),
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
            new TwigFilter('boolean_string', fn ($item) => is_bool($item) ? ($item ? 'true' : 'false') : $item),
            new TwigFilter('base64_encode', fn ($value) => base64_encode($value)),
            new TwigFilter('base64_decode', fn ($value, bool $strict = false) => base64_decode($value, $strict))
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

    protected function evaluateWidgetAffix($value): array
    {
        $affixResolver = new AffixResolver();
        return $affixResolver->resolveAffixTypes(new OptionsResolver(), $value);
    }
}
