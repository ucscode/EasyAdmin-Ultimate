<?php

namespace App\Utils\Stateless;

use App\Utils\Stateless\Abstracts\AbstractUtils;

final class CodeInfusionUtils extends AbstractUtils
{
    public const SLOT_HEADER = 'HEADER';
    public const SLOT_FOOTER = 'FOOTER';
    public const TARGET_ADMIN = 'TARGET_ADMIN';
    public const TARGET_USER = 'TARGET_USER';
    public const TARGET_AUTHENTICATION = 'TARGET_AUTHENTICATION';
    public const TARGET_OTHERS = 'TARGET_OTHERS';
}
