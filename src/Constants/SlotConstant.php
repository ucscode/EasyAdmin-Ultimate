<?php

namespace App\Constants;

use App\Traits\ConstantTrait;

/**
 * This class is used to maintain consistency in slot management.
 *
 * Both within database, application context or twig environment.
 * It define slot names that can be used with the _slot() twig function
 */
final class SlotConstant
{
    use ConstantTrait;

    public const SLOT_HEADER = 'SLOT_HEADER';
    public const SLOT_FOOTER = 'SLOT_FOOTER';

    // add more slot names here
}
