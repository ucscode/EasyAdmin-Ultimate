<?php

namespace App\Controller\Admin;

use App\Entity\Configuration;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ConfigurationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Configuration::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('metaKey', 'Name')->setDisabled(true);
        
        if(in_array($pageName, [Crud::PAGE_INDEX, Crud::PAGE_DETAIL])) {        
            yield TextField::new('metaValueAsString', 'Value');
            return;
        }

        yield TextField::new('metaValue', 'Value');
    }
}
