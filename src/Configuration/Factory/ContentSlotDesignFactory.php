<?php

namespace App\Configuration\Factory;

use App\Configuration\AbstractConfigurationFactory;
use App\Configuration\Design\ContentSlotDesign;
use App\Controller\Admin\Interfaces\AdminControllerInterface;
use App\Controller\Security\Interfaces\SecurityControllerInterface;
use App\Controller\User\Interfaces\UserControllerInterface;

/**
 * @method self setItem(string $name, mixed $value)
 * @method ContentSlotDesign getItem(string $name)
 * @method ContentSlotDesign[] getItems()
 */
class ContentSlotDesignFactory extends AbstractConfigurationFactory
{
    public static function getItemFqcn(): string
    {
        return ContentSlotDesign::class;
    }

    protected function configureItems(): void
    {
        $this->setItem(
            'TARGET_ADMIN',
            (new ContentSlotDesign())
                ->setTitle('Administration Area')
                ->setMarkerInterface(AdminControllerInterface::class)
        );

        $this->setItem(
            'TARGET_USER',
            (new ContentSlotDesign())
                ->setTitle('User Dashboard')
                ->setMarkerInterface(UserControllerInterface::class)
        );

        $this->setItem(
            'TARGET_SECURITY',
            (new ContentSlotDesign())
                ->setTitle('Security Pages')
                ->setMarkerInterface(SecurityControllerInterface::class)
        );

        $this->setItem(
            'TARGET_OTHERS',
            (new ContentSlotDesign())
                ->setTitle('Other Interfaces')
        );
    }

    /**
     * @param string $name
     * @param ContentSlotDesign $value
     * @return void
     */
    protected function normalizeItem(string $name, $value): void
    {
        $value->setName($name);
    }
}
