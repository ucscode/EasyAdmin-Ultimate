<?php

namespace App\Controller\User\Abstract;

use App\Controller\General\Abstract\AbstractGeneralDashboardController;
use App\Controller\User\Crud\PasswordCrudController;
use App\Controller\User\DashboardController;
use App\Controller\User\Crud\ProfileCrudController;
use App\Controller\User\Interface\UserControllerInterface;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;

/**
 * Specialized controller for User Dashboard operations.
 *
 * This abstract class extends `AbstractGeneralDashboardController` to provide a consistent
 * interface specifically for the User Dashboard. It encapsulates the logic and functionalities
 * pertinent to the user side of the application, ensuring a focused and streamlined user experience.
 *
 * @author Ucscode
 */
abstract class AbstractUserDashboardController extends AbstractGeneralDashboardController implements UserControllerInterface
{
    public function configureDashboard(): Dashboard
    {
        /**
         * You can modify this configuration
         */
        return parent::configureDashboard()
        ;
    }

    public function configureMenuItems(): iterable
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Account');

        yield MenuItem::linkToCrud('My Profile', 'fas fa-user', User::class)
            ->setController(ProfileCrudController::class)
            ->setAction(Crud::PAGE_EDIT)
            ->setEntityId($user->getId())
        ;

        yield MenuItem::linkToCrud('Change Password', 'fas fa-lock', User::class)
            ->setController(PasswordCrudController::class)
            ->setAction(Crud::PAGE_EDIT)
            ->setEntityId($user->getId())
        ;

        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
