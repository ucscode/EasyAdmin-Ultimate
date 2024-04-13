<?php

namespace App\Controller\General\Abstract;

use App\Controller\General\Trait\DashboardGeneralControllerTrait;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

abstract class AbstractGeneralCrudController extends AbstractCrudController
{
    use DashboardGeneralControllerTrait;
}