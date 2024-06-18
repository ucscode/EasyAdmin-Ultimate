<?php

namespace App\Controller\Admin\Crud;

use App\Configuration\Factory\ContentSlotDesignFactory;
use App\Controller\Admin\Abstracts\AbstractAdminCrudController;
use App\Entity\Slot\Slot;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ContentSlotCrudController extends AbstractAdminCrudController
{
    public function __construct(protected ContentSlotDesignFactory $contentSlotVOFactory)
    {
        
    }

    public static function getEntityFqcn(): string
    {
        return Slot::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular("Slot")
            ->setEntityLabelInPlural("Slots")
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title')
            ->setHelp('Define a title to remember the code')
        ;

        yield ChoiceField::new('positions', 'Position')
            ->setFormTypeOption('choices', Slot::getChoices('POSITION_'))
            ->allowMultipleChoices()
            ->setHelp('Where in a page should the code be placed?')
            ->onlyOnForms()
        ;

        yield ChoiceField::new('targets')
            ->setFormTypeOption('choices', $this->getContentSlotChoices())
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

    protected function getContentSlotChoices(): array
    {
        $choices = [];

        foreach($this->contentSlotVOFactory->getItems() as $slotDesign) {
            $choices[$slotDesign->getTitle()] = $slotDesign->getName();
        }

        return $choices;
    }
}
