<?php

namespace App\Controller\Admin\Abstract;

use App\Entity\Configuration;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractAdminDashboardController extends AbstractDashboardController
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {}

    protected function getConfigurationValue(string $metaKey): ?string
    {
        $config = $this->entityManager->getRepository(Configuration::class)->findOneBy(['metaKey' => $metaKey]);
        $value = !$config ? null : $config->getMetaValueAsString();
        return $value;
    }
    
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Membership');

        yield MenuItem::linkToCrud('Users', 'fa fa-users', User::class);
        yield MenuItem::linkToCrud('Add User', 'fa fa-user-plus', User::class)
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