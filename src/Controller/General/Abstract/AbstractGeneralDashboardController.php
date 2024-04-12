<?php

namespace App\Controller\General\Abstract;

use App\Entity\Configuration;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractGeneralDashboardController extends AbstractDashboardController
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
        
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()

            ->setTitle($this->getConfigurationValue('app.name'))    

            ->renderContentMaximized()
            
            ->disableDarkMode()

            // ->renderSidebarMinimized()

            /**
             * IMPORTANT: the locale feature won't work unless you add the {_locale} parameter 
             * in the admin dashboard URL (e.g. '/admin/{_locale}').
             * the name of each locale will be rendered in that locale
             * (in the following example you'll see: "English", "Polski")
            */

            // ->setLocales(['en', 'pl']) 
        ;
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        if(!$user instanceof User) {
            throw new \Exception("Invalid User Entity");
        }

        return parent::configureUserMenu($user)

            // ->setAvatarUrl($user->getAvatar()) 

        ;
    }

    public function configureAssets(): Assets
    {
        return parent::configureAssets()

            ->addAssetMapperEntry('app')
            
        ;
    }
    
    protected function getConfigurationValue(string $metaKey, ?string $default = null): ?string
    {
        $repository = $this->entityManager->getRepository(Configuration::class);
        /**
         * @var Configuration
         */
        $config = $repository->findOneBy(['metaKey' => $metaKey]);
        $value = $config?->getMetaValueAsString() ?? $default;
        
        return $value;
    }
}