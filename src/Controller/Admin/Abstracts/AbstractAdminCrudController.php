<?php

namespace App\Controller\Admin\Abstracts;

use App\Controller\Admin\Interfaces\AdminControllerInterface;
use App\Controller\Initial\Abstracts\AbstractInitialCrudController;

/**
 * Specialized CRUD controller for the Admin Dashboard.
 *
 * This abstract class is tailored specifically for the Admin Dashboard, extending the
 * `AbstractInitialCrudController` to provide CRUD operations within the admin context.
 * It encapsulates the logic and functionalities pertinent to the administrative side of the
 * application, ensuring a focused and streamlined admin experience.
 *
 * @author Ucscode
 */
abstract class AbstractAdminCrudController extends AbstractInitialCrudController implements AdminControllerInterface
{
    //
}
