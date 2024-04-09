<?php

namespace App\Controller\Admin;

use App\Entity\UserNotification;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AvatarField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use phpDocumentor\Reflection\Types\Boolean;

class UserNotificationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UserNotification::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('user');

        yield TextField::new('title');
        
        yield TextareaField::new('message')
            ->onlyOnForms();

        yield AvatarField::new('imageUrl', 'Avatar');

        yield TextField::new('actionUrl');

        yield DateField::new('createdAt')
            ->setRequired(false);

        yield BooleanField::new('seenByUser')
            ->renderAsSwitch(false)
            ->hideOnForm();

        yield TextField::new('actionText');
    }
}
