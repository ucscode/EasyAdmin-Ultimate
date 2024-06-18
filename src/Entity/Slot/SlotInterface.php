<?php

namespace App\Entity\Slot;

/**
 * This class is used to maintain consistency in slot management.
 *
 * Both within database, application context or twig environment.
 * It defines slot names that can be used with the _slot() twig function
 */
interface SlotInterface
{
    public const POSITION_HEADER = 'POSITION_HEADER';
    public const POSITION_FOOTER = 'POSITION_FOOTER';

    // add more slot names here
}
