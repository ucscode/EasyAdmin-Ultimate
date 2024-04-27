<?php

namespace App\Controller\General\Abstract;

use App\Constants\FilePathConstants;
use App\Controller\General\Trait\GeneralDashboardControllerTrait;
use App\Entity\Configuration;
use App\Entity\User;
use App\Immutable\SystemConfig;
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
abstract class AbstractGeneralDashboardController extends AbstractDashboardController
{
    use GeneralDashboardControllerTrait;

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

            ->addCssFile(Asset::new(FilePathConstants::SYSTEM_CSS_FILE))

            ->addJsFile(Asset::new(FilePathConstants::SYSTEM_JS_FILE)->htmlAttr('type', 'module'))

        ;
    }
}
