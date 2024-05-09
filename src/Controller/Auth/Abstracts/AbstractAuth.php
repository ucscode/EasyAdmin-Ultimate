<?php

namespace App\Controller\Auth\Abstracts;

use App\Constants\FilePathConstants;
use App\Controller\Auth\Interfaces\AuthControllerInterface;
use App\Controller\Base\Traits\BaseControllerTrait;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

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
}
