<?php

namespace App\Twig;

use App\Context\EasyAdminPackContext;
use App\Service\ConfigurationService;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class EasyAdminPackTwigExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(protected ConfigurationService $configurationService)
    {

    }

    public function getGlobals(): array
    {
        return ['ea_pack' => new EasyAdminPackContext($this->configurationService)];
    }
}
