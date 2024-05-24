<?php

namespace App\Utils;

use App\Controller\Admin\Interfaces\AdminControllerInterface;
use App\Controller\Security\Interfaces\SecurityControllerInterface;
use App\Controller\User\Interfaces\UserControllerInterface;
use App\Utils\Traits\ConstantUtilsTrait;

final class ContentSlotUtils
{
    use ConstantUtilsTrait;

    # @Slot Names
    public const SLOT_HEADER = 'HEADER';
    public const SLOT_FOOTER = 'FOOTER';

    # @Slot Target
    public const TARGET_ADMIN = 'TARGET_ADMIN';
    public const TARGET_USER = 'TARGET_USER';
    public const TARGET_SECURITY = 'TARGET_SECURITY';
    public const TARGET_OTHERS = 'TARGET_OTHERS';

    /**
     * Retrieve the mapping of targeted names to their corresponding dashboard interfaces.
     *
     * This method returns an associative array where the keys are target constants, and the values are
     * the respective interface classes that a dashboard controller should implement.
     *
     * The slot will be activated if the dashboard controller implements the interface mapped to the target.
     * If the target is null, the slot will be activated only when the target does not match any of its sibling interfaces.
     */
    public static function getMappers(): array
    {
        return [
            self::TARGET_ADMIN => AdminControllerInterface::class,
            self::TARGET_USER => UserControllerInterface::class,
            self::TARGET_SECURITY => SecurityControllerInterface::class,
            self::TARGET_OTHERS => null,
        ];
    }
}
