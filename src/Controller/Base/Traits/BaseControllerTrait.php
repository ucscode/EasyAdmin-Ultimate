<?php

namespace App\Controller\Base\Traits;

use App\Service\ConfigurationService;

trait BaseControllerTrait
{
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            ConfigurationService::class => '?' . ConfigurationService::class
        ]);
    }
}