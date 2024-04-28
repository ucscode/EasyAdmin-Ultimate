<?php

namespace App\Utils;

use App\Utils\Abstract\AbstractUtils;

final class CodeInfusionUtils extends AbstractUtils
{
    public const SLOT_HEADER = 'HEADER';
    public const SLOT_FOOTER = 'FOOTER';
    public const PANEL_ADMIN = 'ADMIN PANEL';
    public const PANEL_USER = 'USER PANEL';
    public const PANEL_AUTHENTICATION = 'AUTHENTICATION PANEL';
    public const PANEL_FRONT = 'FRONT PANEL';
}