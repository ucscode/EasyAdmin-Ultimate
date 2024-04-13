<?php

namespace App\Controller\User\Abstract;

use App\Controller\General\Abstract\AbstractGeneralDashboardController;
use App\Controller\User\Crud\PasswordCrudController;
use App\Controller\User\DashboardController;
use App\Controller\User\Crud\ProfileCrudController;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;

abstract class AbstractUserDashboardController extends AbstractGeneralDashboardController
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