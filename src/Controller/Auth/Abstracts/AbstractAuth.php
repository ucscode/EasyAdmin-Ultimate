<?php

namespace App\Controller\Auth\Abstracts;

use App\Constants\FilePathConstants;
use App\Controller\Auth\Interfaces\AuthControllerInterface;
use App\Controller\Base\Traits\BaseControllerTrait;
use App\Service\ConfigurationService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

abstract class AbstractAuth extends AbstractDashboardController implements AuthControllerInterface
{
    use BaseControllerTrait;

    public function configureAssets(): Assets
    {
        return parent::configureAssets()

            ->addAssetMapperEntry('app')

            ->addCssFile(Asset::new(FilePathConstants::SYSTEM_CSS_FILE))

            ->addJsFile(Asset::new(FilePathConstants::SYSTEM_JS_FILE)->htmlAttr('type', 'module'))

        ;
    }

    protected function getConfigurationLogo(?string $default = null): ?string
    {
        /**
         * @var \App\Service\ConfigurationService
         */
        $configurationService = $this->container->get(ConfigurationService::class);

        $logo = $configurationService->get('app.logo');

        if(!empty($logo)) {
            $pathPackage = new PathPackage(FilePathConstants::SYSTEM_IMAGE_BASE_PATH, new EmptyVersionStrategy());
            return $pathPackage->getUrl($logo);
        }
        
        return $default;
    }
}