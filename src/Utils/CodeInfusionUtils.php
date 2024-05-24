<?php

namespace App\Utils;

use App\Utils\Traits\ConstantUtilsTrait;

final class CodeInfusionUtils
{
    use ConstantUtilsTrait;

    public const SLOT_HEADER = 'HEADER';
    public const SLOT_FOOTER = 'FOOTER';
    public const TARGET_ADMIN = 'TARGET_ADMIN';
    public const TARGET_USER = 'TARGET_USER';
    public const TARGET_AUTHENTICATION = 'TARGET_AUTHENTICATION';
    public const TARGET_OTHERS = 'TARGET_OTHERS';
}
