<?php

namespace App\Controller\General\Abstract;

use App\Controller\General\Trait\GeneralDashboardControllerTrait;
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
abstract class AbstractGeneralCrudController extends AbstractCrudController
{
    use GeneralDashboardControllerTrait;
}
