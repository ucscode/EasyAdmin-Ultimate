<?php

namespace App\Controller\Initial\Abstracts;

use App\Constants\FilePathConstants;
use App\Controller\Initial\Traits\InitialControllerTrait;
use App\Entity\User\User;
use App\Service\ConfigurationService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Base controller for general dashboard operations.
 *
 * This abstract class serves as the foundation for all dashboard-related controllers.
 * It extends from the `AbstractDashboardController`, inheriting its methods and properties,
 * and can be further extended by specific dashboard controllers to provide a consistent
 * interface for managing both administrative and user-facing dashboards.
 *
 * @author Ucscode
 */
abstract class AbstractInitialDashboardController extends AbstractDashboardController
{
    use InitialControllerTrait;

    protected ConfigurationService $configurationService;

    public function configureDashboard(): Dashboard
    {
        $this->configurationService = $this->container->get(configurationService::class);

        return Dashboard::new()

            ->setTitle($this->configurationService->get('app.name'))

            ->setFaviconPath($this->configurationService->get('app.logo'))

            ->renderContentMaximized()

            // ->disableDarkMode()

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

            ->addAssetMapperEntry(Asset::new('app'))

            ->addCssFile(Asset::new(FilePathConstants::SYSTEM_CSS_FILE))

            ->addJsFile(Asset::new(FilePathConstants::SYSTEM_JS_FILE)->htmlAttr('type', 'module'))

        ;
    }
}
