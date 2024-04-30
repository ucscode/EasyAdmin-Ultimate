<?php

namespace App\Controller\User\Abstract;

use App\Controller\Base\Abstract\AbstractBaseCrudController;
use App\Controller\User\Interface\UserControllerInterface;

/**
 * Specialized CRUD controller for the User Dashboard.
 *
 * This abstract class is tailored specifically for the User Dashboard, extending the `AbstractBaseCrudController`
 * to provide CRUD operations within the user context. It encapsulates the logic and functionalities pertinent to
 * the user side of the application, ensuring a focused and streamlined user experience.
 *
 * @author Ucscode
 */
abstract class AbstractUserCrudController extends AbstractBaseCrudController implements UserControllerInterface
{
}
