<?php

namespace App\Configuration;

use App\Component\Abstracts\AbstractPattern;
use App\Controller\Admin\Interfaces\AdminControllerInterface;
use App\Controller\Security\Interfaces\SecurityControllerInterface;
use App\Controller\User\Interfaces\UserControllerInterface;
use App\Utils\Traits\ConstantUtilsTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentSlotPattern extends AbstractPattern
{
    use ConstantUtilsTrait;
    
    protected function buildPattern(): void
    {
        $this->addPattern('TARGET_ADMIN', [
            'title' => 'Admin Interface',
            'intent' => AdminControllerInterface::class,
        ]);

        $this->addPattern('TARGET_USER', [
            'title' => 'User Interface',
            'intent' => UserControllerInterface::class,
        ]);

        $this->addPattern('TARGET_SECURITY', [
            'title' => 'Security Interface',
            'intent' => SecurityControllerInterface::class,
        ]);

        $this->addPattern('TARGET_OTHERS', [
            'title' => 'Any other interface',
            'intent' => null,
        ]);
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired(['title', 'intent'])
            ->setAllowedTypes('title', 'string')
            ->setAllowedTypes('intent', ['string', 'null'])
        ;
    }
}