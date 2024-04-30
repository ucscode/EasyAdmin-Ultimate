<?php

namespace App\Controller\Admin\Abstract;

use App\Controller\Admin\Interface\AdminControllerInterface;
use App\Controller\Base\Abstract\AbstractBaseCrudController;

/**
 * Specialized CRUD controller for the Admin Dashboard.
 *
 * This abstract class is tailored specifically for the Admin Dashboard, extending the
 * `AbstractBaseCrudController` to provide CRUD operations within the admin context.
 * It encapsulates the logic and functionalities pertinent to the administrative side of the
 * application, ensuring a focused and streamlined admin experience.
 *
 * @author Ucscode
 */
abstract class AbstractAdminCrudController extends AbstractBaseCrudController implements AdminControllerInterface
{
    //
}
