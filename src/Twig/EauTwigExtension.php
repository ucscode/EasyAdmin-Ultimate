<?php

namespace App\Twig;

use App\Context\EauContext;
use App\Service\ConfigurationService;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class EauTwigExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(protected ConfigurationService $configurationService)
    {

    }

    public function getGlobals(): array
    {
        return [
            'eau' => new EauContext($this->configurationService)
        ];
    }
}
