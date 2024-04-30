<?php

namespace App\Controller\Base\Abstract;

use App\Controller\Base\Trait\BaseDashboardControllerTrait;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

/**
 * Base CRUD controller for managing entities across both Admin and User dashboards.
 *
 * This abstract class provides common functionalities required for entity management
 * within the administrative and user-facing dashboards, ensuring a consistent
 * CRUD interface for both contexts.
 *
 * @author Ucscode
 */
abstract class AbstractBaseCrudController extends AbstractCrudController
{
    use BaseDashboardControllerTrait;
}
