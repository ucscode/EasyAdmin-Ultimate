<?php

namespace App\Controller\Admin\Abstract;

use App\Controller\Admin\Crud\UserCrudController;
use App\Controller\General\Abstract\AbstractGeneralDashboardController;
use App\Entity\Configuration;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Specialized controller for Admin Dashboard operations.
 *
 * This abstract class extends `AbstractGeneralDashboardController` to provide a consistent
 * interface specifically for the Admin Dashboard. It encapsulates the logic and functionalities
 * pertinent to the administrative side of the application, ensuring a focused and streamlined admin experience.
 * 
 * @author Ucscode
 */
abstract class AbstractAdminDashboardController extends AbstractGeneralDashboardController
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
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Membership');

        yield MenuItem::linkToCrud('Users', 'fa fa-users', User::class)
            ->setController(UserCrudController::class);
            
        yield MenuItem::linkToCrud('Add User', 'fa fa-user-plus', User::class)
            ->setController(UserCrudController::class)
            ->setAction(Crud::PAGE_NEW);

        // yield MenuItem::linkToCrud('Properties', 'fa fa-infinity', UserProperty::class);
        // yield MenuItem::linkToCrud('Notifications', 'fa fa-bell', UserNotification::class);

        yield MenuItem::section('settings');

        yield MenuItem::linkToCrud('Configuration', 'fa fa-wrench', Configuration::class);
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);

        yield MenuItem::section('Misc');

        // yield MenuItem::linkToLogout('Logout', 'fa fa-exit');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)

        ;
    }
}