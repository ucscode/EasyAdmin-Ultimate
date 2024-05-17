<?php

namespace App\Controller\Admin\Abstracts;

use App\Controller\Admin\Crud\UserCrudController;
use App\Controller\Admin\Interfaces\AdminControllerInterface;
use App\Controller\Initial\Abstracts\AbstractInitialDashboardController;
use App\Entity\CodeInfusion;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Specialized controller for Admin Dashboard operations.
 *
 * This abstract class extends `AbstractInitialDashboardController` to provide a consistent
 * interface specifically for the Admin Dashboard. It encapsulates the logic and functionalities
 * pertinent to the administrative side of the application, ensuring a focused and streamlined admin experience.
 *
 * @author Ucscode
 */
abstract class AbstractAdminDashboardController extends AbstractInitialDashboardController implements AdminControllerInterface
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
        yield MenuItem::linkToDashboard('Dashboard', 'fas fa-home');

        yield MenuItem::section('Membership');

        yield MenuItem::linkToCrud('Users', 'fas fa-users', User::class)
            ->setController(UserCrudController::class);

        yield MenuItem::linkToCrud('Add User', 'fas fa-user-plus', User::class)
            ->setController(UserCrudController::class)
            ->setAction(Crud::PAGE_NEW);

        // yield MenuItem::section('settings');

        yield MenuItem::section('Misc');

        yield MenuItem::linkToCrud('Code Infusion', 'fa fa-code', CodeInfusion::class);
        yield MenuItem::linkToLogout('Logout', 'fas fa-arrow-right-from-bracket');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)

        ;
    }
}
