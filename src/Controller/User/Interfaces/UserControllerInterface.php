<?php

namespace App\Controller\User\Interfaces;

/**
 * This interface is intended for implementation by Dashboard and Crud Controllers associated with User operations.
 * Its distinction enables the internal system to identify the category to which a given dashboard or crud controller belongs.
 *
 * For specialized interfaces tailored exclusively to Dashboard or Crud Controllers, the following are recommended:
 *
 * - UserDashboardControllerInterface
 * - UserCrudControllerInterface
 */
interface UserControllerInterface
{
}
