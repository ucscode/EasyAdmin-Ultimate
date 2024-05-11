<?php

namespace App\Controller\Base\Traits;

use App\Context\EauContext;
use App\Service\ConfigurationService;
use Symfony\Component\HttpFoundation\RequestStack;

trait BaseControllerTrait
{
    protected string $favicon = 'https://static.thenounproject.com/png/5265761-200.png';

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            ConfigurationService::class => '?' . ConfigurationService::class,
            RequestStack::class => '?' . RequestStack::class,
            EauContext::class => '?' . EauContext::class,
        ]);
    }
}
