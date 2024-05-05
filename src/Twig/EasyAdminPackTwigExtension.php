<?php

namespace App\Twig;

use App\Context\EasyAdminPackContext;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class EasyAdminPackTwigExtension extends AbstractExtension implements GlobalsInterface
{
    public function getGlobals(): array
    {
        return ['ea_pack' => new EasyAdminPackContext()];
    }
}