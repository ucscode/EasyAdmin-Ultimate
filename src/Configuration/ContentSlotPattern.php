<?php

namespace App\Configuration;

use App\Component\Abstracts\AbstractPattern;
use App\Component\Traits\ConstantTrait;
use App\Controller\Admin\Interfaces\AdminControllerInterface;
use App\Controller\Security\Interfaces\SecurityControllerInterface;
use App\Controller\User\Interfaces\UserControllerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentSlotPattern extends AbstractPattern
{
    use ConstantTrait;

    public const ACCESS_TITLE = 'title';
    public const ACCESS_PARENT_FQCN = 'ancestor';

    protected function buildPattern(): void
    {
        # Slot content will be rendered only if the current controller is:

        # An instance of AdminControllerInterface

        $this->addPattern('TARGET_ADMIN', [
            self::ACCESS_TITLE => 'Admin Interface',
            self::ACCESS_PARENT_FQCN => AdminControllerInterface::class,
        ]);

        # An instance of UserControllerInterface

        $this->addPattern('TARGET_USER', [
            self::ACCESS_TITLE => 'User Interface',
            self::ACCESS_PARENT_FQCN => UserControllerInterface::class,
        ]);

        # An instance of SecurityControllerInterface

        $this->addPattern('TARGET_SECURITY', [
            self::ACCESS_TITLE => 'Security Interface',
            self::ACCESS_PARENT_FQCN => SecurityControllerInterface::class,
        ]);

        # Not an instance of any of the pattern above

        $this->addPattern('TARGET_OTHERS', [
            self::ACCESS_TITLE => 'Other Interface',
            self::ACCESS_PARENT_FQCN => null,
        ]);

        // You can add more pattern here
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired([
                self::ACCESS_TITLE,
                self::ACCESS_PARENT_FQCN
            ])
            ->setAllowedTypes(self::ACCESS_TITLE, 'string')
            ->setAllowedTypes(self::ACCESS_PARENT_FQCN, ['string', 'null'])
        ;
    }
}
