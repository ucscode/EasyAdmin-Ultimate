<?php

namespace App\Controller\Admin\Crud;

use App\Entity\CodeInfusion;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CodeInfusionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CodeInfusion::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title')
            ->setHelp('Define a title to remember this code')
        ;

        yield ChoiceField::new('slot')
            ->setFormTypeOption('choices', [
                'Header' => 'header',
                'Footer' => 'footer'
            ])
            ->setHelp('Where in a page should this code be placed?')
        ;

        yield ChoiceField::new('location')
            ->setFormTypeOption('choices', [
                'ADMIN PANEL' => 'ADMIN_PANEL',
                'USER PANEL' => 'USER_PANEL',
                'AUTHENTICATION PANEL' => 'AUTHENTICATION PANEL',
                'FRONT PANEL' => 'FRONT_PANEL',
            ])
            ->allowMultipleChoices()
            ->setHelp('In what panel should this code be added?')
        ;

        // What permission is required to access this code?

        yield BooleanField::new('enabled');

        yield NumberField::new('sort', 'Order')
            ->setHelp('How should this code be sorted (arranged)')
        ;

        yield CodeEditorField::new('content', 'HTML Code')
            ->setLanguage('xml')
            ->setNumOfRows(20)
        ;
    }
}
