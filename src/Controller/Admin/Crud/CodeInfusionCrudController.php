<?php

namespace App\Controller\Admin\Crud;

use App\Utils\CodeInfusionUtils;
use App\Entity\CodeInfusion;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
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
            ->setHelp('Define a title to remember the code')
        ;

        yield ChoiceField::new('slot')
            ->setFormTypeOption('choices', CodeInfusionUtils::getChoices('SLOT_'))
            ->setHelp('Where in a page should the code be placed?')
            ->onlyOnForms()
        ;

        yield ChoiceField::new('locations')
            ->setFormTypeOption('choices', CodeInfusionUtils::getChoices('PANEL_'))
            ->allowMultipleChoices()
            ->setHelp('In what panel should this code be available?')
            ->onlyOnForms()
        ;

        yield BooleanField::new('enabled');

        yield NumberField::new('sort', 'Order')
            ->setHelp('In what order should the code be arranged')
        ;

        yield CodeEditorField::new('content', 'HTML Code')
            ->setLanguage('xml')
            ->setNumOfRows(20)
        ;
    }
}
