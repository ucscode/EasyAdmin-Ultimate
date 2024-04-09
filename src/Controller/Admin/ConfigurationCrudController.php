<?php

namespace App\Controller\Admin;

use App\Entity\Configuration;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ConfigurationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Configuration::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('metaKey', 'Name')
            ->setDisabled(true);

        yield TextField::new('metaValue', 'Value');
    }
}
