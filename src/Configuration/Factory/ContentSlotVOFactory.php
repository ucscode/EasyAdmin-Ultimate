<?php

namespace App\Configuration\Factory;

use App\Configuration\AbstractConfigurationPattern;
use App\Configuration\ValueObject\ContentSlotVO;
use App\Controller\Admin\Interfaces\AdminControllerInterface;
use App\Controller\Security\Interfaces\SecurityControllerInterface;
use App\Controller\User\Interfaces\UserControllerInterface;

/**
 * @method self setItem(string $name, mixed $value)
 * @method ContentSlotVO getItem(string $name)
 * @method ContentSlotVO[] getItems()
 */
class ContentSlotVOFactory extends AbstractConfigurationPattern
{
    public static function getItemFqcn(): string
    {
        return ContentSlotVO::class;
    }

    protected function configureItems(): void
    {
        $this->setItem(
            'TARGET_ADMIN',
            (new ContentSlotVO())
                ->setTitle('Admin Interface')
                ->setMarkerInterface(AdminControllerInterface::class)
        );

        $this->setItem(
            'TARGET_USER',
            (new ContentSlotVO())
                ->setTitle('User Interface')
                ->setMarkerInterface(UserControllerInterface::class)
        );

        $this->setItem(
            'TARGET_SECURITY',
            (new ContentSlotVO())
                ->setTitle('Security Interface')
                ->setMarkerInterface(SecurityControllerInterface::class)
        );

        $this->setItem(
            'TARGET_OTHERS',
            (new ContentSlotVO())
                ->setTitle('Other Interfaces')
        );
    }

    /**
     * @param string $name
     * @param ContentSlotVO $value
     * @return void
     */
    protected function normalizeItem(string $name, $value): void
    {
        $value->setName($name);
    }
}