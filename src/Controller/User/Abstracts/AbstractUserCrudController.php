<?php

namespace App\Controller\User\Abstracts;

use App\Controller\Initial\Abstracts\AbstractInitialCrudController;
use App\Controller\User\Interfaces\UserControllerInterface;

/**
 * Specialized CRUD controller for the User Dashboard.
 *
 * This abstract class is tailored specifically for the User Dashboard, extending the `AbstractInitialCrudController`
 * to provide CRUD operations within the user context. It encapsulates the logic and functionalities pertinent to
 * the user side of the application, ensuring a focused and streamlined user experience.
 *
 * @author Ucscode
 */
abstract class AbstractUserCrudController extends AbstractInitialCrudController implements UserControllerInterface
{
}
