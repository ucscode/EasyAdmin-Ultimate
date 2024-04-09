<?php

namespace App\Controller\Admin;

use App\Entity\UserProperty;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserPropertyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UserProperty::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('user')->autocomplete();
        yield TextField::new('metaKey');
        yield TextareaField::new('metaValue');
    }
}
