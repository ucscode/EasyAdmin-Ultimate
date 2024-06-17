<?php

namespace App\Controller\Security\Abstracts;

use App\Constants\PathConstant;
use App\Controller\Security\Interfaces\SecurityControllerInterface;
use App\Controller\Initial\Traits\InitialControllerTrait;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

abstract class AbstractSecurityController extends AbstractDashboardController implements SecurityControllerInterface
{
    use InitialControllerTrait;

    public function configureAssets(): Assets
    {
        return parent::configureAssets()

            ->addAssetMapperEntry('app')

            ->addCssFile(Asset::new(PathConstant::SYSTEM_CSS_FILE))

            ->addJsFile(Asset::new(PathConstant::SYSTEM_JS_FILE)->htmlAttr('type', 'module'))

        ;
    }
}
