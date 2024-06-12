<?php

namespace App\Controller\Admin\Abstracts;

use App\Controller\Admin\Crud\ContentSlotCrudController;
use App\Controller\Admin\Crud\User\UserCrudController;
use App\Controller\Admin\Interfaces\AdminControllerInterface;
use App\Controller\Initial\Abstracts\AbstractInitialDashboardController;
use App\Controller\Initial\Media\MediaCrudController;
use App\Entity\ContentSlot;
use App\Entity\Media;
use App\Entity\User\User;
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

        yield MenuItem::section('Misc');

        yield MenuItem::linkToCrud('Media', 'fa fa-camera', Media::class)
            ->setController(MediaCrudController::class);

        yield MenuItem::linkToCrud('Slots', 'fa fa-code', ContentSlot::class)
            ->setController(ContentSlotCrudController::class);

        yield MenuItem::linkToLogout('Logout', 'fas fa-arrow-right-from-bracket');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)

        ;
    }
}
