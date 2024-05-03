<?php

namespace App\Controller\Admin\Interfaces;

/**
 * This interface is intended for implementation by Dashboard and Crud Controllers associated with Admin operations.
 * Its distinction enables the internal system to identify the category to which a given dashboard or crud controller belongs.
 *
 * For specialized interfaces tailored exclusively to Dashboard or Crud Controllers, the following are recommended:
 *
 * - AdminDashboardControllerInterface
 * - AdminCrudControllerInterface
 */
interface AdminControllerInterface
{
}
